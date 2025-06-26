<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'block/user_management:view' => [
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
            'admin' => CAP_ALLOW,
        ],
    ],
];