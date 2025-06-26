<?php
require_once('../../config.php');
require_login();
require_sesskey();

if (!is_siteadmin()) {
    print_error('nopermissions', 'error');
}

$userid = required_param('id', PARAM_INT);

if ($userid != 1) {
    require_capability('moodle/user:delete', context_system::instance());
    delete_user($DB->get_record('user', ['id' => $userid]));
}

redirect(new moodle_url('/my'));
