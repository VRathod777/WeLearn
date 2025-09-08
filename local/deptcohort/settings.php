<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_deptcohort', get_string('pluginname', 'local_deptcohort'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_heading('local_deptcohort/heading',
        get_string('pluginname', 'local_deptcohort'),
        'This plugin syncs users into cohorts automatically based on their Department profile field.'));
}
