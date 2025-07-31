<?php
require_once(__DIR__ . '/../../config.php');
require_login();
require_sesskey();

if (!is_siteadmin()) {
    throw new \moodle_exception('Access denied');
}

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

require_once($CFG->dirroot . '/blocks/user_management/lib.php');
echo block_user_management_ajax_output($users, $page, ceil($totalusers / $perpage));
