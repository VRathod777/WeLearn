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
require_once(__DIR__ . '/classes/backup.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

// Optional parameters.
$itemtypeid = optional_param('itemtypeid', '', PARAM_RAW);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '/local/mb2builder/backups.php', PARAM_LOCALURL);
$type = optional_param('type', 1, PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
$filename = optional_param('filename', '', PARAM_RAW);
$page = optional_param('page', 0, PARAM_INT);
$createdby = optional_param('createdby', 0, PARAM_INT);
$sort = optional_param('sort', 'timecreated', PARAM_ALPHANUMEXT);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$perpage = 20;

// Links.
$managebackups = '/local/mb2builder/backups.php';
$deletepage = '/local/mb2builder/delete-backup.php';
$baseurl = new moodle_url($managebackups, ['sort' => $sort, 'dir' => $dir, 'page' => $page, 'perpage' => $perpage]);
$returnurl = new moodle_url($returnurl);

// Configure the context of the page.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url( '/local/mb2builder/backups.php' );
$PAGE->set_pagelayout('admin'); // This is require for page navigation.

require_login();
require_capability('local/mb2builder:managebackups', $context);
$canmanage = has_capability('local/mb2builder:managebackups', $context);

// Get items.
$sortorderitems = Mb2builderBackup::get_list_records(($page * $perpage), $perpage, false, '*', $sort, $dir);
$countitems = Mb2builderBackup::get_list_records(0, 0, true, '*', $sort, $dir);

// Delete the backup.
if ($canmanage && $deleteid && $type && $itemid && $filename) {
    Mb2builderBackup::delete_backup($deleteid, $type, $itemid, $createdby, $filename);
    $message = get_string('backupdeleted', 'local_mb2builder');
}

if (isset($message)) {
    redirect($returnurl, $message);
}

// Page title.
$namepage = get_string('pluginname', 'local_mb2builder');
$PAGE->set_heading($namepage);
$PAGE->set_title($namepage);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('backups', 'local_mb2builder'));

// Table declaration.
$table = new flexible_table('mb2builder-backups-table');

// Customize the table.
$table->define_columns(
    [
        'timecreated',
        'type',
        'filesize',
        'createdby',
        'actions',
    ]
);

$table->define_headers(
    [
        get_string('name', 'moodle'),
        get_string('type', 'local_mb2builder'),
        get_string('filesize', 'local_mb2builder'),
        get_string('createdby', 'local_mb2builder'),
        get_string('actions', 'moodle'),
    ]
);

$table->define_baseurl($baseurl);
$table->set_attribute('class', 'generaltable');
$table->column_class('name', 'align-middle');
$table->column_class('type', 'align-middle text-center');
$table->column_class('filesize', 'align-middle text-center');
$table->column_class('createdby', 'align-middle text-center');
$table->column_class('actions', 'text-right align-middle');

$table->headers = Mb2builderBackup::get_table_header($table->columns, ['timecreated', 'type', 'filesize', 'createdby']);
$table->setup();

foreach ($sortorderitems as $item) {

    // Filling of information columns.
    $namecallback = userdate($item->timecreated, '%Y-%m-%d %H:%M:%S') . ' ' . $item->name;

    // Type.
    if ($item->type == 3) {
        $type = get_string('part', 'local_mb2builder');
    } else if ($item->type == 2) {
        $type = get_string('footer', 'local_mb2builder');
    } else {
        $type = get_string('page', 'local_mb2builder');
    }

    // File size.
    $filesize = round($item->filesize / (1024 * 1024), 1) . ' MB';

    // Created and modified by.
    $createduser = Mb2builderBackup::get_user($item->createdby);
    $createduserdate = userdate($item->timecreated, get_string('strftimedatemonthabbr', 'local_mb2builder'));
    $modifieduserdate = userdate($item->timemodified, get_string('strftimedatemonthabbr', 'local_mb2builder'));
    $createdbyitem = $createduser ? '<div class="mb2slides-admin-username">' . $createduser->firstname . ' ' .
    $createduser->lastname .  '</div>' : get_string('coresystem');

    // Link to download.
    $filearea = Mb2builderBackup::bakup_file_area($item->type);
    $downloadurl = moodle_url::make_pluginfile_url(context_system::instance()->id, 'local_mb2builder', $filearea, $item->itemid, '/'
    , $item->filename, true);
    $downloadlink = new moodle_url($downloadurl);
    $downloaditem = $OUTPUT->action_icon($downloadlink, new pix_icon('t/download', get_string('download')));

    // Link to remove.
    $deletelink = new moodle_url($deletepage, ['deleteid' => $item->id, 'type' => $item->type, 'itemid' => $item->itemid, 'filename'
    => $item->filename]);
    $deleteitem = $OUTPUT->action_icon($deletelink, new pix_icon('t/delete', get_string('delete')));

    // Check if user can manage items.
    $actions = $downloaditem;
    $actions .= $canmanage ? $deleteitem : '';

    $table->add_data([$namecallback, $type, $filesize, $createdbyitem, $actions]);

}

echo Mb2builderBackup::filter();

// Display the table.
$table->print_html();
echo $OUTPUT->paging_bar($countitems, $page, $perpage, $baseurl);
echo $OUTPUT->footer();
