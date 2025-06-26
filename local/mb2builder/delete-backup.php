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
require_once(__DIR__ . '/classes/backup.php');

// Optional parameters.
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$type = optional_param('type', 1, PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
$filename = optional_param('filename', '', PARAM_RAW);
$confirm = optional_param('confirm', 0, PARAM_INT);

$context = context_system::instance();

// Link generation.
$urlparameters = ['deleteid' => $deleteid, 'type' => $type, 'itemid' => $itemid, 'filename' => $filename];
$baseurl = new moodle_url('/local/mb2builder/delete-backup.php', $urlparameters);
$managebackups = '/local/mb2builder/backups.php';

// Configure the context of the page.
require_login();
require_capability('local/mb2builder:managebackups', $context);

// The page title.
$titlepage = get_string('deletebackup', 'local_mb2builder');
$PAGE->set_context($context);
$PAGE->set_url($baseurl);
$PAGE->navbar->add($titlepage);
$PAGE->set_heading($titlepage);
$PAGE->set_title($titlepage);
$PAGE->navbar->add(get_string('backups', 'local_mb2builder'), $baseurl);
echo $OUTPUT->header();

if ($confirm == 0) {
    $yesurl = new moodle_url($managebackups, ['action' => 'delete', 'deleteid' => $deleteid, 'type' => $type, 'itemid' => $itemid,
    'filename' => $filename, 'sesskey' => sesskey(), 'confirm' => 1]);
    $nourl = new moodle_url($managebackups);
    $callback = $DB->get_record('local_mb2builder_backups', ['id' => $deleteid], '*', MUST_EXIST);
    echo $OUTPUT->confirm(get_string('confirmdeletebackup', 'local_mb2builder', ['title' =>
    userdate($callback->timecreated, '%Y-%m-%d %H:%M:%S') . ' ' . $callback->name]), $yesurl, $nourl);
}

echo $OUTPUT->footer();
