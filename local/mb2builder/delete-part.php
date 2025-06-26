<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package    local_mb2builder
 * @copyright  2018 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/classes/parts_api.php');

// Optional parameters.
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);
$contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);

$context = context::instance_by_id($contextid, MUST_EXIST);

// Link generation.
$urlparameters = ['deleteid' => $deleteid, 'contextid' => $contextid];
$baseurl = new moodle_url('/local/mb2builder/delete-part.php', $urlparameters);
$manageparts = '/local/mb2builder/parts.php';

// Configure the context of the page.
require_login();
require_capability('local/mb2builder:manageparts', $context);

// The page title.
$titlepage = get_string('deletepart', 'local_mb2builder');
$PAGE->set_context($context);
$PAGE->set_url($baseurl);
$PAGE->navbar->add($titlepage);
$PAGE->set_heading($titlepage);
$PAGE->set_title($titlepage);
$PAGE->navbar->add(get_string('parts', 'local_mb2builder'), $baseurl);
echo $OUTPUT->header();

if ($confirm == 0) {
    $yesurl = new moodle_url($manageparts, ['contextid' => $contextid, 'action' => 'delete', 'deleteid' => $deleteid,
    'sesskey' => sesskey(), 'confirm' => 1]);
    $nourl = new moodle_url($manageparts, ['contextid' => $contextid]);
    $callback = Mb2builderpartsApi::get_record($deleteid);
    echo $OUTPUT->confirm(get_string('confirmdeletepart', 'local_mb2builder', ['title' => $callback->name]), $yesurl, $nourl);
}

echo $OUTPUT->footer();
