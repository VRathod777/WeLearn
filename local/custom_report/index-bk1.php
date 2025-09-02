<?php
require_once(__DIR__ . '/../../config.php'); // Load Moodle configuration
require_once($CFG->libdir . '/tablelib.php'); // For paginated tables

// Ensure user is logged in and has the required capability
require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);

// Check if CSV export is requested
$download = optional_param('download', '', PARAM_ALPHA);

if ($download === 'csv') {
    // Fetch all records for CSV export
    $sql = "
        SELECT u.username, u.firstname, u.lastname, u.email, c.fullname AS coursefullname, p.progress, 
                ue.timecreated AS enrollmentdate
        FROM {user} u
        JOIN {edwreports_course_progress} p ON u.id = p.userid
        JOIN {course} c ON p.courseid = c.id
        JOIN {user_enrolments} ue ON ue.userid = u.id
        JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id
        ORDER BY u.username";

    $records = $DB->get_records_sql($sql);

    // Define CSV headers
    $csvheaders = array(
        get_string('username', 'local_custom_report'),
        get_string('firstname', 'local_custom_report'),
        get_string('lastname', 'local_custom_report'),
        get_string('email', 'local_custom_report'),
        get_string('coursefullname', 'local_custom_report'),
        get_string('progress', 'local_custom_report'),
        get_string('status', 'local_custom_report'),
        get_string('enrollmentdate', 'local_custom_report')
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

        $row = array(
            $record->username,
            $record->firstname,
            $record->lastname,
            $record->email,
            $record->coursefullname,
            $record->progress,
            $status,
            userdate($record->enrollmentdate) // Format enrollment date
        );

        fputcsv($output, $row);
    }

    fclose($output);
    exit;
}

// Page setup
$PAGE->set_url('/local/custom_report/index.php');
$PAGE->set_context($context);
$PAGE->set_title(get_string('report_title', 'local_custom_report'));
$PAGE->set_heading(get_string('report_heading', 'local_custom_report'));
$PAGE->set_pagelayout('admin');

// Navigation
$PAGE->navbar->add(get_string('report_title', 'local_custom_report'), new moodle_url('/local/custom_report/index.php'));

// Fetch total record count
$totalrecords = $DB->count_records_sql("
    SELECT COUNT(*)
    FROM {user} u
    JOIN {edwreports_course_progress} p ON u.id = p.userid
    JOIN {course} c ON p.courseid = c.id
    JOIN {user_enrolments} ue ON ue.userid = u.id
    JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id 
");

// Output starts here
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('report_heading', 'local_custom_report') . ' (' . $totalrecords . ')');

// Add export button
$exporturl = new moodle_url('/local/custom_report/index.php', array('download' => 'csv'));
echo html_writer::link($exporturl, get_string('exportcsv', 'local_custom_report'), array('class' => 'btn btn-primary csv_export_custom'));
echo '<br>';

// Set up the table
$table = new flexible_table('course-progress-table');
$table->define_columns(array('username', 'firstname', 'lastname', 'email', 'coursefullname', 'progress', 'status', 'enrollmentdate'));
$table->define_headers(array(
    get_string('username', 'local_custom_report'),
    get_string('firstname', 'local_custom_report'),
    get_string('lastname', 'local_custom_report'),
    get_string('email', 'local_custom_report'),
    get_string('coursefullname', 'local_custom_report'),
    get_string('progress', 'local_custom_report'),
    get_string('status', 'local_custom_report'),
    get_string('enrollmentdate', 'local_custom_report')
));
$table->define_baseurl($PAGE->url);
$table->sortable(true, 'username', SORT_ASC); // Enable sorting for the username
$table->no_sorting('status'); // Disable sorting for the status column
$table->no_sorting('enrollmentdate'); // Disable sorting for the enrollment date column
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
$sql = "
    SELECT u.username, u.firstname, u.lastname, u.email, c.fullname AS coursefullname, p.progress, 
            ue.timecreated  AS enrollmentdate
    FROM {user} u
    JOIN {edwreports_course_progress} p ON u.id = p.userid
    JOIN {course} c ON p.courseid = c.id
    JOIN {user_enrolments} ue ON ue.userid = u.id
    JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = c.id  
    ORDER BY $sort
    LIMIT $start, $perpage";

$records = $DB->get_records_sql($sql);

// Set up the table with data
foreach ($records as $record) {
    // Determine the status based on progress
    if ($record->progress == 100) {
        $status = get_string('status_completed', 'local_custom_report');
    } elseif ($record->progress > 0) {
        $status = get_string('status_inprogress', 'local_custom_report');
    } else {
        $status = get_string('status_notstarted', 'local_custom_report');
    }

    $table->add_data(array(
        $record->username,
        $record->firstname,
        $record->lastname,
        $record->email,
        $record->coursefullname,
        $record->progress,
        $status,
        userdate($record->enrollmentdate), // Format enrollment date
    ));
}
$table->print_html();

// Display pagination
echo $OUTPUT->paging_bar($totalrecords, $page, $perpage, $PAGE->url);

// Output ends here
echo $OUTPUT->footer();
