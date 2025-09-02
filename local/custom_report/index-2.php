<?php
require_once(__DIR__ . '/../../config.php'); // Load Moodle configuration
require_once($CFG->libdir . '/tablelib.php'); // For paginated tables

// Ensure user is logged in and has the required capability
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Get filter values from user input
$entity = trim(optional_param('entity', '', PARAM_TEXT));
$department = trim(optional_param('department', '', PARAM_TEXT));
$sbu = trim(optional_param('sbu', '', PARAM_TEXT));
$trainingname = trim(optional_param('trainingname', '', PARAM_TEXT));
$status = trim(optional_param('status', '', PARAM_ALPHA));
// Get Start Date From and Start Date To values from user input
$startdatefrom = trim(optional_param('startdatefrom', '', PARAM_TEXT));
$startdateto = trim(optional_param('startdateto', '', PARAM_TEXT));
$completiondatefrom = trim(optional_param('completiondatefrom', '', PARAM_TEXT));
$completiondateto = trim(optional_param('completiondateto', '', PARAM_TEXT));


// Construct the WHERE clause for the filters
$where = "WHERE u.id != 2";
$params = [];

if (!empty($entity)) {
    $where .= " AND u.id IN(SELECT d1.userid from {user_info_data} d1 
JOIN {user_info_field} f1 ON d1.fieldid = f1.id  where shortname = 'PE' AND data LIKE :entity)";
    $params['entity'] = '%' . $entity . '%';
}
if (!empty($department)) {
    $where .= " AND u.id IN(SELECT d1.userid from {user_info_data} d1 
JOIN {user_info_field} f1 ON d1.fieldid = f1.id  where shortname = 'Dep' AND data LIKE :department)";
    $params['department'] = '%' . $department . '%';
}
if (!empty($sbu)) {
    $where .= " AND u.id IN(SELECT d1.userid from {user_info_data} d1 
JOIN {user_info_field} f1 ON d1.fieldid = f1.id  where shortname = 'SBU' AND data LIKE :sbu)";
    $params['sbu'] = '%' . $sbu . '%';
}
if (!empty($trainingname)) {
    $where .= " AND c.fullname LIKE :trainingname";
    $params['trainingname'] = '%' . $trainingname . '%';
}
if (!empty($status)) {
    if ($status == 'completed') {
        $where .= " AND p.progress = 100";
    } elseif ($status == 'inprogress') {
        $where .= " AND p.progress > 0 AND p.progress < 100";
    } else {
        $where .= " AND p.progress = 0";
    }
}
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

// Check if CSV export is requested
$download = optional_param('download', '', PARAM_ALPHA);
if ($download === 'csv') {
    // Fetch all records for CSV export
    $sql = "
        SELECT concat(u.id,'_',c.id) as uid1, u.username, u.firstname, u.lastname, u.email, c.fullname AS coursefullname, p.progress, 
                ue.timecreated AS enrollmentdate, u.city, p.completiontime, u.id as userid
        FROM {user} u
        JOIN {edwreports_course_progress} p ON u.id = p.userid
        JOIN {course} c ON p.courseid = c.id
        JOIN {user_enrolments} ue ON ue.userid = u.id
        JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id
        LEFT JOIN {user_info_data} uid ON uid.userid = u.id
        LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid 
        $where
        GROUP BY u.id, c.id
        ORDER BY u.username";

    // echo  $sql;
    // pr($params);
    // die;

    $records = $DB->get_records_sql($sql, $params);

    // Define CSV headers in the required order
    $csvheaders = array(
        get_string('username', 'local_custom_report'),    // EMPLOYEE ID
        get_string('firstname', 'local_custom_report'),     // FIRST NAME
        get_string('lastname', 'local_custom_report'),      // LAST NAME
        get_string('email', 'local_custom_report'),  // EMAIL ADDRESS
        get_string('managername', 'local_custom_report'),   // Manager Name
        // get_string('manageremployeeid', 'local_custom_report'), // Manager Employee ID
        get_string('manageremailid', 'local_custom_report'),    // Manager Email ID
        get_string('entity', 'local_custom_report'),        // Entity
        get_string('department', 'local_custom_report'),    // Department
        get_string('sbu', 'local_custom_report'),           // SBU
        get_string('trainingname', 'local_custom_report'),  // Training Name
        get_string('enrollmentdate', 'local_custom_report'),  // Assigned Date
        get_string('completiondate', 'local_custom_report'), // Completed Date
        get_string('location', 'local_custom_report'),      // Location
        get_string('status', 'local_custom_report'),        // Status
    );

    // Start CSV output
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=course_progress_report.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, $csvheaders);

    foreach ($records as $record) {
        // Determine the status based on progress
        if ($record->progress == 100) {
            $status = get_string('status_completed', 'local_custom_report');
        } elseif ($record->progress > 0) {
            $status = get_string('status_inprogress', 'local_custom_report');
        } else {
            $status = get_string('status_notstarted', 'local_custom_report');
        }

        // Initialize the row1 array to store additional field data
        $row1 = [
            'Manager' => '',
            'Reporting_manager' => '',
            'Reporting_manager_Email_ID' => '',
            'PE' => '',
            'Dep' => '',
            'SBU' => '',
        ];

        $inner_record = $DB->get_records_sql("SELECT 
            uif.shortname,  uid.data  FROM {user} u LEFT JOIN {user_info_data} uid ON uid.userid = u.id
        LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid WHERE u.id = ?", [$record->userid]);

        // Populate the $row1 array with corresponding data
        foreach ($inner_record as $recoinner) {
            $row1[$recoinner->shortname] = $recoinner->data; // Only set if data exists
        }


        // Create the row with fields in the specified order
        if ($record->progress == 100) {
            $completion_date = userdate($record->completiontime);
        } else {
            $completion_date = '-';
        }

        $row = array(
            $record->username,                      // EMPLOYEE ID (you may need to adjust this depending on how employee ID is fetched)
            $record->firstname,                     // FIRST NAME
            $record->lastname,                      // LAST NAME
            $record->email,                         // EMAIL ADDRESS
            // $row1['Manager'],                       // Manager Name
            $row1['Reporting_manager'],                     // Manager Employee ID
            $row1['Reporting_manager_Email_ID'],                    // Manager Email ID
            $row1['PE'],                            // Entity
            $row1['Dep'],                           // Department
            $row1['SBU'],                           // SBU
            $record->coursefullname,                // Training Name
            userdate($record->enrollmentdate),      // Assigned Date
            $completion_date,               // Completed Date
            $record->city,                          // Location
            $status,                                // Status
        );

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

$entity = trim(optional_param('entity', '', PARAM_TEXT));
$department = trim(optional_param('department', '', PARAM_TEXT));
$sbu = trim(optional_param('sbu', '', PARAM_TEXT));
$trainingname = trim(optional_param('trainingname', '', PARAM_TEXT));
$status = trim(optional_param('status', '', PARAM_ALPHA));

// Page setup
$PAGE->set_url('/local/custom_report/index.php', [
    'entity' => $entity,
    'department' => $department,
    'sbu' => $sbu,
    'trainingname' => $trainingname,
    'status' => $status,
    'startdatefrom' => $startdatefrom,
    'startdateto' => $startdateto,

    'completiondatefrom' => $completiondatefrom,
    'completiondateto' => $completiondateto,
]);
$PAGE->set_context($context);
$PAGE->set_title(get_string('report_title', 'local_custom_report'));
$PAGE->set_heading(get_string('report_heading', 'local_custom_report'));
$PAGE->set_pagelayout('admin');

// Navigation
$PAGE->navbar->add(get_string('report_title', 'local_custom_report'), new moodle_url('/local/custom_report/index.php'));
// Fetch total record count
$totalrecords = $DB->count_records_sql("
    SELECT COUNT(*)
    FROM (
        SELECT u.id, c.id as cid
        FROM {user} u
        JOIN {edwreports_course_progress} p ON u.id = p.userid
        JOIN {course} c ON p.courseid = c.id
        JOIN {user_enrolments} ue ON ue.userid = u.id
        JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id 
        LEFT JOIN {user_info_data} uid ON uid.userid = u.id
        LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid 
        $where
        GROUP BY u.id, c.id
    ) AS grouped_records
", $params);


// Fetch total record count and status-wise counts
// $sql = "
//     SELECT 
//         SUM(CASE WHEN p.progress = 100 THEN 1 ELSE 0 END) AS completed_count,
//         SUM(CASE WHEN p.progress > 0 AND p.progress < 100 THEN 1 ELSE 0 END) AS inprogress_count,
//         SUM(CASE WHEN p.progress = 0 THEN 1 ELSE 0 END) AS notstarted_count
//     FROM {user} u
//     JOIN {edwreports_course_progress} p ON u.id = p.userid
//     JOIN {course} c ON p.courseid = c.id
//     JOIN {user_enrolments} ue ON ue.userid = u.id
//     JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id 
//     LEFT JOIN {user_info_data} uid ON uid.userid = u.id
//     LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid 
//     $where
//      GROUP BY u.id, c.id
//     ";

// $status_counts = $DB->get_record_sql($sql, $params);

// Output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('report_heading', 'local_custom_report') . ' (' . $totalrecords . ')');
// Display the chart using Chart.js


echo '<div style="width: 60%; height: 300px; margin: auto;">
    <canvas id="statusChart"></canvas>
</div>';

echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

echo "<br><hr>";
// Display filter form
echo '<form method="GET" action="" class="container">';

// Start the first row
echo '<div class="row">';

// Entity (First row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('entity', 'local_custom_report') . '</label>';
echo '<input type="text" name="entity" value="' . s($entity) . '" class="form-control">';
echo '</div>';

// Department (First row, second column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('department', 'local_custom_report') . '</label>';
echo '<input type="text" name="department" value="' . s($department) . '" class="form-control">';
echo '</div>';

echo '</div>'; // Close the first row

// Start the second row
echo '<div class="row">';

// SBU (Second row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('sbu', 'local_custom_report') . '</label>';
echo '<input type="text" name="sbu" value="' . s($sbu) . '" class="form-control">';
echo '</div>';

// Training Name (Second row, second column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('trainingname', 'local_custom_report') . '</label>';
echo '<input type="text" name="trainingname" value="' . s($trainingname) . '" class="form-control">';
echo '</div>';

echo '</div>'; // Close the second row

// Start the third row
echo '<div class="row">';

// Start Date From (Third row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('startdatefrom', 'local_custom_report') . '</label>';
echo '<input type="date" name="startdatefrom" value="' . s($startdatefrom) . '" class="form-control">';
echo '</div>';

// Start Date To (Third row, second column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('startdateto', 'local_custom_report') . '</label>';
echo '<input type="date" name="startdateto" value="' . s($startdateto) . '" class="form-control">';
echo '</div>';

echo '</div>'; // Close the third row

// Start the fourth row
echo '<div class="row">';

// Completion Date From (Fourth row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('completiondatefrom', 'local_custom_report') . '</label>';
echo '<input type="date" name="completiondatefrom" value="' . s($completiondatefrom) . '" class="form-control">';
echo '</div>';

// Completion Date To (Fourth row, second column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('completiondateto', 'local_custom_report') . '</label>';
echo '<input type="date" name="completiondateto" value="' . s($completiondateto) . '" class="form-control">';
echo '</div>';

echo '</div>'; // Close the fourth row



// Start the third row
echo '<div class="row">';

// Status (Third row, first column)
echo '<div class="form-group col-md-6 mb-3">';
echo '<label>' . get_string('status', 'local_custom_report') . '</label>';
echo '<select name="status" class="form-control">';
echo '<option value="">' . get_string('all', 'local_custom_report') . '</option>';
echo '<option value="completed"' . ($status == 'completed' ? ' selected' : '') . '>' . get_string('status_completed', 'local_custom_report') . '</option>';
echo '<option value="inprogress"' . ($status == 'inprogress' ? ' selected' : '') . '>' . get_string('status_inprogress', 'local_custom_report') . '</option>';
echo '<option value="notstarted"' . ($status == 'notstarted' ? ' selected' : '') . '>' . get_string('status_notstarted', 'local_custom_report') . '</option>';
echo '</select>';
echo '</div>';

// Submit and Reset buttons (Third row, second column)
echo '<div class="form-group col-md-6 mb-3 d-flex align-items-end">';
echo '<input type="submit" value="' . get_string('filter', 'local_custom_report') . '" class="btn btn-primary mr-2">';
echo '<a href="' . $CFG->wwwroot . '/local/custom_report/index.php" class="btn btn-secondary">' . get_string('reset') . '</a>';
echo '</div>';

echo '</div>'; // Close the third row
echo '</form>';

echo '<br>';
// Add export button
$exporturl = new moodle_url('/local/custom_report/index.php', array(
    'download' => 'csv',
    'entity' => $entity,
    'department' => $department,
    'sbu' => $sbu,
    'trainingname' => $trainingname,
    'status' => $status,
    'startdatefrom' => $startdatefrom,
    'startdateto' => $startdateto,

    'completiondatefrom' => $completiondatefrom,
    'completiondateto' => $completiondateto,
));
echo html_writer::link($exporturl, get_string('exportcsv', 'local_custom_report'), array('class' => 'btn btn-primary csv_export_custom'));
echo '<br>';


// Set up the table
$table = new flexible_table('course-progress-table');
$table->define_columns(array(
    'username',            // EMPLOYEE ID
    'firstname',           // FIRST NAME
    'lastname',            // LAST NAME
    'email',               // EMAIL ADDRESS
    'managername',         // Manager Name
    'manageremployeeid',   // Manager Employee ID
    'manageremailid',       // Manager Email ID
    'PE',          // Entity
    'Dep',          // Department
    'SBU',                 // SBU
    'coursefullname',   // Training Name
    'enrollmentdate',      // Assigned Date
    'completiontime',      // Completed Date
    'city',            // Location
    'progress',
));
$table->define_headers(array(
    get_string('username', 'local_custom_report'),            // EMPLOYEE ID
    get_string('firstname', 'local_custom_report'),           // FIRST NAME
    get_string('lastname', 'local_custom_report'),            // LAST NAME
    get_string('email', 'local_custom_report'),               // EMAIL ADDRESS
    get_string('managername', 'local_custom_report'),         // Manager Name
    // get_string('manageremployeeid', 'local_custom_report'),   // Manager Employee ID
    get_string('manageremailid', 'local_custom_report'),      // Manager Email ID
    get_string('entity', 'local_custom_report'),              // Entity
    get_string('department', 'local_custom_report'),          // Department
    get_string('sbu', 'local_custom_report'),                 // SBU
    get_string('trainingname', 'local_custom_report'),        // Training Name
    get_string('enrollmentdate', 'local_custom_report'),      // Assigned Date
    get_string('completiondate', 'local_custom_report'),      // Completed Date
    get_string('location', 'local_custom_report'),            // Location
    get_string('status', 'local_custom_report'),              // Status
));
$table->define_baseurl($PAGE->url);
$table->sortable(true, 'username', SORT_ASC); // Enable sorting for the username
$table->no_sorting('status'); // Disable sorting for the status column
$table->no_sorting('managername');
$table->no_sorting('manageremployeeid');
$table->no_sorting('manageremailid');
$table->no_sorting('PE');
$table->no_sorting('Dep');
$table->no_sorting('SBU');

$baseurl = new moodle_url('/local/custom_report/index.php', array(
    'entity' => $entity,
    'department' => $department,
    'sbu' => $sbu,
    'trainingname' => $trainingname,
    'status' => $status,
    'startdatefrom' => $startdatefrom,
    'startdateto' => $startdateto,

    'completiondatefrom' => $completiondatefrom,
    'completiondateto' => $completiondateto,
));

$table->define_baseurl($baseurl);

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
    $sort = 'u.username ASC'; // Default sort if no sort is set
}
// Fetch the user data along with course progress
// $sql = "
//     SELECT 
//         CONCAT(u.id, '_', c.id) AS uid,
//         u.username, 
//         u.firstname, 
//         u.lastname, 
//         u.email, 
//         c.fullname AS coursefullname, 
//         p.progress, 
//         ue.timecreated AS enrollmentdate, 
//         u.city, 
//         p.completiontime,
//          u.id as userid
//     FROM {user} u
//     JOIN {edwreports_course_progress} p ON u.id = p.userid
//     JOIN {course} c ON p.courseid = c.id
//     JOIN {user_enrolments} ue ON ue.userid = u.id
//     JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id  
//     LEFT JOIN {user_info_data} uid ON uid.userid = u.id
//     LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid
//     $where 
//     GROUP BY u.id, c.id
//     ORDER BY $sort
//     LIMIT $start, $perpage";


    $sql = "
    SELECT concat(u.id,'_',c.id) as uid1, u.username, u.firstname, u.lastname, u.email, c.fullname AS coursefullname, p.progress, 
            ue.timecreated AS enrollmentdate, u.city, p.completiontime, u.id as userid
    FROM {user} u
    JOIN {edwreports_course_progress} p ON u.id = p.userid
    JOIN {course} c ON p.courseid = c.id
    JOIN {user_enrolments} ue ON ue.userid = u.id
    JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id
    LEFT JOIN {user_info_data} uid ON uid.userid = u.id
    LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid 
    $where
    GROUP BY u.id, c.id
    ORDER BY $sort
    LIMIT $start, $perpage";

// pr($sql);
// pr($params); die;

$records = $DB->get_records_sql($sql, $params);
$notstartedcount = $completedcount = $inporgresscount = 0;

// Set up the table with data
foreach ($records as $record) {
    // Determine the status based on progress
    // $status = $record->progress === 100 ? get_string('status_completed', 'local_custom_report') : ($record->progress > 0 ? get_string('status_inprogress', 'local_custom_report') : get_string('status_notstarted', 'local_custom_report'));

    // Determine the status based on progress
    if ($record->progress == 100) {
        $completedcount++;
        $status = get_string('status_completed', 'local_custom_report');
    } elseif ($record->progress > 0) {
        $inporgresscount++;
        $status = get_string('status_inprogress', 'local_custom_report');
    } else {
        $status = get_string('status_notstarted', 'local_custom_report');
        $notstartedcount++;
    }

    // Initialize the row1 array to store additional field data
    $row1 = [
        'Manager' => '',
        'Reporting_manager' => '',
        'Reporting_manager_Email_ID' => '',
        'PE' => '',
        'Dep' => '',
        'SBU' => '',
    ];

    $inner_record = $DB->get_records_sql("SELECT 
	 uif.shortname,  uid.data  FROM {user} u LEFT JOIN {user_info_data} uid ON uid.userid = u.id
LEFT JOIN {user_info_field} uif ON uif.id = uid.fieldid WHERE u.id = ?", [$record->userid]);

    // Populate the $row1 array with corresponding data
    foreach ($inner_record as $recoinner) {
        $row1[$recoinner->shortname] = $recoinner->data; // Only set if data exists
    }

    // Handle completion date based on progress
    $completion_date = ($record->progress == 100) ? userdate($record->completiontime) : '-';

    // Create the row with fields in the specified order
    $row = array(
        '<a href="' . $CFG->wwwroot . '/user/profile.php?id=' . $record->userid . '">' . $record->username . '</a>',  // EMPLOYEE ID
        $record->firstname,                     // FIRST NAME
        $record->lastname,                      // LAST NAME
        $record->email,                         // EMAIL ADDRESS
        // $row1['Manager'],                       // Manager Name
        $row1['Reporting_manager'],                     // Manager Employee ID
        $row1['Reporting_manager_Email_ID'],                    // Manager Email ID
        $row1['PE'],                            // Entity
        $row1['Dep'],                           // Department
        $row1['SBU'],                           // SBU
        $record->coursefullname,                // Training Name
        userdate($record->enrollmentdate),      // Assigned Date
        $completion_date,                       // Completed Date
        $record->city,                          // Location
        $status,                                // Status
    );

    // Add data to the table or output
    $table->add_data($row);  // Assuming you're adding it to a table, adjust if needed
}


echo '<script>
var ctx = document.getElementById("statusChart").getContext("2d");
var statusChart = new Chart(ctx, {
    type: "bar",
    data: {
        labels: ["' . get_string('status_completed', 'local_custom_report') . '", "' . get_string('status_inprogress', 'local_custom_report') . '", "' . get_string('status_notstarted', 'local_custom_report') . '"],
        datasets: [{
            data: [' . $completedcount . ', ' . $inporgresscount . ', ' . $notstartedcount . '],
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


$table->print_html();

// Display pagination
echo $OUTPUT->paging_bar($totalrecords, $page, $perpage, $PAGE->url);

// Output ends here
echo $OUTPUT->footer();
