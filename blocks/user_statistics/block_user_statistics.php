<?php
class block_user_statistics extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_user_statistics');
    }

    public function get_content() {
        global $OUTPUT, $DB, $USER;

        if ($this->content !== null) return $this->content;

        $context = context_user::instance($USER->id);

        $totalstudents = $DB->count_records('user', ['deleted' => 0, 'suspended' => 0]);
        $time = time() - (30 * 24 * 60 * 60);
        $newstudents = $DB->count_records_select('user', "timecreated > ? AND deleted = 0", [$time]);
        $totalcourses = $DB->count_records('course') - 1;

        $completedcourses = $DB->count_records_select('course_completions', 'userid = ? AND timecompleted IS NOT NULL', [$USER->id]);
        $certificates = 0;
        if ($DB->get_manager()->table_exists('customcert_issues')) {
            $certificates = $DB->count_records('customcert_issues', ['userid' => $USER->id]);
        }

        $data = (object)[
            'totalstudents' => $totalstudents,
            'newstudents' => $newstudents,
            'totalcourses' => $totalcourses,
            'completedcourses' => $completedcourses,
            'certificates' => $certificates
        ];

        $this->page->requires->css(new moodle_url('/blocks/user_statistics/styles.css'));

        $this->content = new stdClass();
        $rendered = $OUTPUT->render_from_template('block_user_statistics/dashboard', $data);
        $this->content->text = html_writer::div($rendered, 'block_user_statistics_wrapper');
        return $this->content;
    }
}
