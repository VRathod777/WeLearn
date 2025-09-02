<?php
require_once(__DIR__ . '/../../config.php'); // Load Moodle configuration
require_once($CFG->libdir . '/tablelib.php'); // For paginated tables

// Ensure user is logged in and has the required capability
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Check if CSV export is requested
$download = optional_param('download', '', PARAM_ALPHA);

// Get Start Date From and Start Date To values from user input
$startdatefrom = trim(optional_param('startdatefrom', '', PARAM_TEXT));
$startdateto = trim(optional_param('startdateto', '', PARAM_TEXT));
$completiondatefrom = trim(optional_param('completiondatefrom', '', PARAM_TEXT));
$completiondateto = trim(optional_param('completiondateto', '', PARAM_TEXT));

// Construct the WHERE clause for the filters
$where = "WHERE 1 = 1";
$params = [];


// Start Date From filter
if (!empty($startdatefrom)) {
    // Check if the date is valid and convert to timestamp
    $startdatefromtimestamp = strtotime($startdatefrom);
    if ($startdatefromtimestamp !== false) {
        $where .= " AND ue.timecreated >= :startdatefrom";
        $params['startdatefrom'] = $startdatefromtimestamp; // Convert date to timestamp
    } else {
        throw new moodle_exception('invaliddate', 'error', '', $startdatefrom);
    }
}

// Start Date To filter
if (!empty($startdateto)) {
    // Check if the date is valid and convert to timestamp
    $startdatetotimestamp = strtotime($startdateto);
    if ($startdatetotimestamp !== false) {
        $where .= " AND ue.timecreated <= :startdateto";
        $params['startdateto'] = $startdatetotimestamp; // Convert date to timestamp
    } else {
        throw new moodle_exception('invaliddate', 'error', '', $startdateto);
    }
}


// Completion Date From filter
if (!empty($completiondatefrom)) {
    // Convert date to timestamp and validate it
    $completiondatefromtimestamp = strtotime($completiondatefrom);
    if ($completiondatefromtimestamp !== false) {
        $where .= " AND p.completiontime >= :completiondatefrom";
        $params['completiondatefrom'] = $completiondatefromtimestamp; // Convert date to timestamp
    } else {
        throw new moodle_exception('invaliddate', 'error', '', $completiondatefrom);
    }
}

// Completion Date To filter
if (!empty($completiondateto)) {
    // Convert date to timestamp and validate it
    $completiondatetotimestamp = strtotime($completiondateto);
    if ($completiondatetotimestamp !== false) {
        $where .= " AND p.completiontime <= :completiondateto";
        $params['completiondateto'] = $completiondatetotimestamp; // Convert date to timestamp
    } else {
        throw new moodle_exception('invaliddate', 'error', '', $completiondateto);
    }
}


if ($download === 'csv') {
    // Fetch all records for CSV export
    $sql = "
        SELECT 
            c.id, c.fullname AS course_name, COUNT(ue.id) AS no_of_enrollments, 
            CASE WHEN c.visible = 1 THEN 'Active' ELSE 'Inactive' END AS course_status,
            c.timecreated AS course_created_date, 
            SUM(CASE WHEN p.progress = 100 THEN 1 ELSE 0 END) AS completed_count, 
            SUM(CASE WHEN p.progress > 0 AND p.progress < 100 THEN 1 ELSE 0 END) AS inprogress_count, 
            SUM(CASE WHEN p.progress = 0 THEN 1 ELSE 0 END) AS notstarted_count
        FROM mdl_course c
        JOIN mdl_enrol e ON e.courseid = c.id
        JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
        JOIN mdl_edwreports_course_progress p ON p.courseid = c.id AND p.userid = ue.userid
        $where
        GROUP BY c.id
        ORDER BY c.fullname";

    $records = $DB->get_records_sql($sql, $params);

    // echo $sql;
    // pr($params);
    // die;

    // Define CSV headers
    $csvheaders = array(
        get_string('coursefullname', 'local_custom_report'),
        get_string('no_of_enrollments', 'local_custom_report'),
        get_string('course_status', 'local_custom_report'),
        get_string('course_created_date', 'local_custom_report'),
        get_string('completed_count', 'local_custom_report'),
        get_string('inprogress_count', 'local_custom_report'),
        get_string('notstarted_count', 'local_custom_report')
    );

    // Start CSV output
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=course_usage_report.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, $csvheaders);

    foreach ($records as $record) {
        $row = array(
            $record->course_name,
            $record->no_of_enrollments,
            $record->course_status,
            userdate($record->course_created_date), // Format creation date
            $record->completed_count,
            $record->inprogress_count,
            $record->notstarted_count
        );

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

// Page setup
$PAGE->set_url('/local/custom_report/course_usage.php', [
    'startdatefrom' => $startdatefrom,
    'startdateto' => $startdateto,
    'completiondatefrom' => $completiondatefrom,
    'completiondateto' => $completiondateto
]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('report_heading_usage', 'local_custom_report'));
$PAGE->set_heading(get_string('report_heading_usage', 'local_custom_report'));
$PAGE->set_pagelayout('admin');

// Navigation
$PAGE->navbar->add(get_string('report_heading_usage', 'local_custom_report'), new moodle_url('/local/custom_report/course_usage.php'));
// Fetch total record count
$totalrecords = $DB->get_record_sql("
        SELECT 
            COUNT(DISTINCT c.id) AS counter
        FROM mdl_course c
        JOIN mdl_enrol e ON e.courseid = c.id
        JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
        JOIN mdl_edwreports_course_progress p ON p.courseid = c.id AND p.userid = ue.userid
         $where
", $params);

// Fetch total record count and status-wise counts
$sql = "
    SELECT 
        SUM(CASE WHEN p.progress = 100 THEN 1 ELSE 0 END) AS completed_count,
        SUM(CASE WHEN p.progress > 0 AND p.progress < 100 THEN 1 ELSE 0 END) AS inprogress_count,
        SUM(CASE WHEN p.progress = 0 THEN 1 ELSE 0 END) AS notstarted_count
    FROM mdl_course c
    JOIN mdl_enrol e ON e.courseid = c.id
    JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
    JOIN mdl_edwreports_course_progress p ON p.courseid = c.id AND p.userid = ue.userid
     $where";

$status_counts = $DB->get_record_sql($sql, $params);

// Output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('report_heading_usage', 'local_custom_report') . ' (' . $totalrecords->counter . ')');
// Display the chart using Chart.js
echo '<div style="width: 60%; height: 300px; margin: auto;">
    <canvas id="statusChart"></canvas>
</div>';

echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
echo '<script>
var ctx = document.getElementById("statusChart").getContext("2d");
var statusChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["' . get_string('status_completed', 'local_custom_report') . '", "' . get_string('status_inprogress', 'local_custom_report') . '", "' . get_string('status_notstarted', 'local_custom_report') . '"],
        datasets: [{
            data: [' . $status_counts->completed_count . ', ' . $status_counts->inprogress_count . ', ' . $status_counts->notstarted_count . '],
            backgroundColor: [
                "rgba(75, 192, 192, 0.5)",
                "rgba(54, 162, 235, 0.5)",
                "rgba(255, 99, 132, 0.5)"
            ],
            borderColor: [
                "rgba(75, 192, 192, 1)",
                "rgba(54, 162, 235, 1)",
                "rgba(255, 99, 132, 1)"
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false, // No legend needed without dataset label
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.chart.data.labels[context.dataIndex] || ""; // Use label from data.labels array
                        if (label) {
                            label += ": ";
                        }
                        if (context.parsed.y !== null) {
                            label += context.parsed.y;
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    font: {
                        size: 12
                    }
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1, 
                    font: {
                        size: 12
                    }
                }
            }
        }
    }
});
</script>';


echo "<br><hr>";
// Display filter form
echo '<form method="GET" action="" class="container">';

// Start the first row (Start Date From and Start Date To)
echo '<div class="row">';

// Start Date From (First row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('startdatefrom', 'local_custom_report') . '</label>';
echo '<input type="date" name="startdatefrom" value="' . s($startdatefrom) . '" class="form-control">';
echo '</div>';

// Start Date To (First row, second column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('startdateto', 'local_custom_report') . '</label>';
echo '<input type="date" name="startdateto" value="' . s($startdateto) . '" class="form-control">';
echo '</div>';

echo '</div>'; // Close the first row

// Start the second row (Completion Date From and Completion Date To)
echo '<div class="row">';

// Completion Date From (Second row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('completiondatefrom', 'local_custom_report') . '</label>';
echo '<input type="date" name="completiondatefrom" value="' . s($completiondatefrom) . '" class="form-control">';
echo '</div>';

// Completion Date To (Second row, second column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('completiondateto', 'local_custom_report') . '</label>';
echo '<input type="date" name="completiondateto" value="' . s($completiondateto) . '" class="form-control">';
echo '</div>';

echo '</div>'; // Close the second row

// Submit and Reset buttons
echo '<div class="row">';
echo '<div class="form-group col-md-6 mb-3 d-flex align-items-end">';
echo '<input type="submit" value="' . get_string('filter', 'local_custom_report') . '" class="btn btn-primary mr-2">';
echo '<a href="' . $CFG->wwwroot . '/local/custom_report/course_usage.php" class="btn btn-secondary">' . get_string('reset') . '</a>';
echo '</div>';
echo '</div>'; // Close the button row

echo '</form>';



// Add export button
$exporturl = new moodle_url('/local/custom_report/course_usage.php', array(
    'download' => 'csv',
    'startdatefrom' => $startdatefrom,
    'startdateto' => $startdateto,
    'completiondatefrom' => $completiondatefrom,
    'completiondateto' => $completiondateto,
));
echo html_writer::link($exporturl, get_string('exportcsv', 'local_custom_report'), array('class' => 'btn btn-primary csv_export_custom'));
echo '<br><br>';

// Set up the table
$table = new flexible_table('course-progress-table');
$table->define_columns(array('course_name', 'no_of_enrollments', 'course_status', 'course_created_date', 'completed_count', 'inprogress_count', 'notstarted_count'));
$table->define_headers(array(
    get_string('coursefullname', 'local_custom_report'),
    get_string('no_of_enrollments', 'local_custom_report'),
    get_string('course_status', 'local_custom_report'),
    get_string('course_created_date', 'local_custom_report'),
    get_string('completed_count', 'local_custom_report'),
    get_string('inprogress_count', 'local_custom_report'),
    get_string('notstarted_count', 'local_custom_report')
));
$table->define_baseurl($PAGE->url);
$table->sortable(true, 'course_name', SORT_ASC); // Enable sorting for the course name
$table->no_sorting('course_status'); // Disable sorting for the course status column
$table->no_sorting('course_created_date'); // Disable sorting for the course creation date column
$table->collapsible(true);
$table->set_attribute('class', 'generaltable');
$table->set_attribute('id', 'course-progress-table');

// Pagination setup
$perpage = 10; // Number of records per page
$page = optional_param('page', 0, PARAM_INT);
$start = $page * $perpage;

// Call setup() before accessing sorting
$table->setup();

// Sorting setup
$sort = $table->get_sql_sort();
if (!$sort) {
    $sort = 'c.fullname ASC'; // Default sort if no sort is set
}

// Fetch the course data along with progress counts
$sql = "
    SELECT 
        c.id, c.fullname AS course_name, COUNT(ue.id) AS no_of_enrollments, 
        CASE WHEN c.visible = 1 THEN 'Active' ELSE 'Inactive' END AS course_status,
        c.timecreated AS course_created_date, 
        SUM(CASE WHEN p.progress = 100 THEN 1 ELSE 0 END) AS completed_count, 
        SUM(CASE WHEN p.progress > 0 AND p.progress < 100 THEN 1 ELSE 0 END) AS inprogress_count, 
        SUM(CASE WHEN p.progress = 0 THEN 1 ELSE 0 END) AS notstarted_count
    FROM mdl_course c
    JOIN mdl_enrol e ON e.courseid = c.id
    JOIN mdl_user_enrolments ue ON ue.enrolid = e.id
    JOIN mdl_edwreports_course_progress p ON p.courseid = c.id AND p.userid = ue.userid
    $where
    GROUP BY c.id
    ORDER BY $sort";
$records = $DB->get_records_sql($sql, $params, null, $start, $perpage);

$table->pagesize($perpage, $totalrecords);

// Loop through each record and add it to the table
foreach ($records as $record) {
    $row = array(
        $record->course_name,
        $record->no_of_enrollments,
        $record->course_status,
        userdate($record->course_created_date), // Format creation date
        $record->completed_count,
        $record->inprogress_count,
        $record->notstarted_count
    );

    $table->add_data($row);
}

// Finish output
$table->finish_output();

// Output ends here
echo $OUTPUT->footer();
