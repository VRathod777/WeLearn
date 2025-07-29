<?php

class block_user_statistics extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_user_statistics');
    }

    public function get_content() {
        global $OUTPUT, $DB, $USER;

        if ($this->content !== null) return $this->content;

    
        $context = context_system::instance();
        $isadmin = has_capability('moodle/site:config', $context);
        $isstudent = !$isadmin;

        $data = new stdClass();
        $data->isstudent = $isstudent;

        if (!$isstudent) {
            // Admin view
            $data->totalstudents = $DB->count_records('user', ['deleted' => 0, 'suspended' => 0]);

            $time = time() - (30 * 24 * 60 * 60);
            $data->newstudents = $DB->count_records_select('user', "timecreated > ? AND deleted = 0", [$time]);
        }

        // Total visible courses (site-wide)
        $data->totalcourses = $DB->count_records_select('course', 'id > 1 AND visible = 1');

        if ($isstudent) {
            //  My enrolled courses
            $sqlmy = "SELECT COUNT(DISTINCT c.id)
                        FROM {course} c
                        JOIN {enrol} e ON e.courseid = c.id
                        JOIN {user_enrolments} ue ON ue.enrolid = e.id
                       WHERE ue.userid = ?";
            $data->mycourses = $DB->count_records_sql($sqlmy, [$USER->id]);

            //  Completed courses (proper Moodle completions)
            $sqlcompleted = "SELECT COUNT(DISTINCT cc.course)
                               FROM {course_completions} cc
                               JOIN {course} c ON c.id = cc.course
                              WHERE cc.userid = ? AND cc.timecompleted IS NOT NULL AND c.visible = 1";
            $data->completedcourses = $DB->count_records_sql($sqlcompleted, [$USER->id]);
        } else {
            // Admin's own completed courses
            $data->completedcourses = $DB->count_records_select('course_completions', 'userid = ? AND timecompleted IS NOT NULL', [$USER->id]);
        }

        // ✅ Certificates
        $data->certificates = 0;
        if ($DB->get_manager()->table_exists('customcert_issues')) {
            $data->certificates = $DB->count_records('customcert_issues', ['userid' => $USER->id]);
        }

        $this->content = new stdClass();
        $rendered = $OUTPUT->render_from_template('block_user_statistics/dashboard', $data);
        $this->content->text = html_writer::div($rendered, 'block_user_statistics_wrapper');
        return $this->content;
    }
}
