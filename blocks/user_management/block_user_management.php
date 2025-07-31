<?php
class block_user_management extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_user_management');
    }

    public function get_content() {
        global $OUTPUT, $DB, $USER, $CFG;

        if (!is_siteadmin()) {
            $this->content = new stdClass;
            $this->content->text = get_string('notadmin', 'block_user_management');
            return $this->content;
        }

        // require_once($CFG->libdir . '/accesslib.php');
        require_once($CFG->dirroot . '/blocks/user_management/lib.php');

        $this->page->requires->css(new moodle_url('/blocks/user_management/styles.css'));
        $this->page->requires->js(new moodle_url('/blocks/user_management/script.js'));

        // Pagination setup
        $perpage = 10;
        $page = optional_param('page', 1, PARAM_INT);
        $start = ($page - 1) * $perpage;

        $totalusers = $DB->count_records('user', ['deleted' => 0]);

        $users = $DB->get_records_sql("
            SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.suspended, ra.roleid
            FROM {user} u
            LEFT JOIN {role_assignments} ra ON ra.userid = u.id AND ra.contextid = 1
            WHERE u.deleted = 0
            ORDER BY u.id ASC
            LIMIT $perpage OFFSET $start
        ");

        $html = '<div id="usertable">';
        $html .= block_user_management_ajax_output($users, $page, ceil($totalusers / $perpage));
        $html .= '</div>';

        $this->content = new stdClass;
        $this->content->text = $html;
        return $this->content;
    }
}
