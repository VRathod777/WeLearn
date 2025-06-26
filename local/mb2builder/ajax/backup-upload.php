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

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get builder files.
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/backup.php');

$context = context_system::instance();
$PAGE->set_url('/local/mb2builder/ajax/backup-upload.php');
$PAGE->set_context($context);

require_login();
require_sesskey();

$opts = [
    'mediatype' => $_POST['mediatype'],
    'itemid' => $_POST['itemid'],
    'pageid' => $_POST['pageid'],
    'footerid' => $_POST['footerid'],
    'partid' => $_POST['partid'],
];

if ($opts['partid']) {
    $filearea = 'backuppart';
    $type = 3;
} else if ($opts['footerid']) {
    $filearea = 'backupfooter';
    $type = 2;
} else {
    $filearea = 'backuppage';
    $type = 1;
}

// Save backup file in to the draft area.
$fs = get_file_storage();
$draftfiles = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $_POST['backupfile'], 'id DESC', false);

if (!$draftfiles) {
    die;
}

// Get the first file from draft files.
$draftfile = reset($draftfiles);

// Define options for new file record.
$filerecord = [
    'contextid' => context_user::instance($USER->id)->id,
    'component' => 'user',
    'filearea' => 'draft',
    'itemid' => $_POST['backupfile'],
    'filepath' => '/',
    'filename' => $draftfile->get_filename(),
    'userid' => $USER->id,
];

$file = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'], $filerecord['itemid'],
$filerecord['filepath'], $filerecord['filename'], $filerecord['userid']);

if (!$file) {
    $fs->create_file_from_storedfile($filerecord, $draftfile);
}

echo json_encode(['itemid' => $filerecord['itemid'], 'name' => $filerecord['filename']], true);
die();
