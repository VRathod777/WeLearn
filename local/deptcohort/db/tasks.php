<?php
defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname' => 'local_deptcohort\task\sync',
        'blocking' => 0,
        // Run every 1 minutes by default; admin can change scheduled task in Site administration.
        'minute' => '*/1',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'dayofweek' => '*'
    ]
];
