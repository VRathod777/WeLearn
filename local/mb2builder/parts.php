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
require_once(__DIR__ . '/classes/parts_api.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/tablelib.php');

require_login();
$syscontext = context_system::instance();

// Optional parameters.
$contextid = optional_param('contextid', $syscontext->id, PARAM_INT);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$returnurl = optional_param('returnurl', '/local/mb2builder/parts.php', PARAM_LOCALURL);
$page = optional_param('page', 0, PARAM_INT);
$sort = optional_param('sort', 'timecreated', PARAM_ALPHANUMEXT);
$dir = optional_param('dir', 'DESC', PARAM_ALPHA);
$user = optional_param('user', 0, PARAM_INT);
$perpage = 20;

$context = context::instance_by_id($contextid, MUST_EXIST);

// Links.
$editpage = '/local/mb2builder/edit-part.php';
$manageparts = '/local/mb2builder/parts.php';
$deletepage = '/local/mb2builder/delete-part.php';
$baseurl = new moodle_url($manageparts, ['sort' => $sort, 'dir' => $dir, 'page' => $page, 'perpage' => $perpage, 'contextid' =>
$contextid]);
$returnurl = new moodle_url($returnurl);

// Configure the context of the page.
if ($contextid == $syscontext->id) {
    $PAGE->set_context(context_course::instance($contextid));
} else {
    $PAGE->set_context($context);
}
$PAGE->set_url($baseurl);

require_capability('local/mb2builder:manageparts', $context);
$canmanage = has_capability('local/mb2builder:manageparts', $context);

// Get items.
$sortorderitems = Mb2builderPartsApi::get_list_records(($page * $perpage), $perpage, false, '*', $sort, $dir);
$countitems = Mb2builderPartsApi::get_list_records(0, 0, true, '*', $sort, $dir);

// Delete the page.
if ($deleteid) {
    Mb2builderPartsApi::delete($deleteid);
    $message = get_string('partdeleted', 'local_mb2builder');
}

if (isset($message)) {
    redirect($returnurl, $message);
}

// Page title.
$namepage = get_string('pluginname', 'local_mb2builder');
$PAGE->set_heading($namepage);
$PAGE->set_title($namepage);
$PAGE->navbar->add(get_string('parts', 'local_mb2builder'), $baseurl);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('parts', 'local_mb2builder'));

// Table declaration.
$table = new flexible_table('mb2builder-parts-table');

// Customize the table.
$table->define_columns(
    [
        'name',
        'timecreated',
        'timemodified',
        'actions',
    ]
);

$table->define_headers(
    [
        get_string('name'),
        get_string('timecreated', 'local_mb2builder'),
        get_string('timemodified', 'local_mb2builder'),
        get_string('actions'),
    ]
);

$table->define_baseurl($baseurl);
$table->set_attribute('class', 'generaltable');
$table->column_class('timecreated', 'text-center align-middle');
$table->column_class('timemodified', 'text-center align-middle');
$table->column_class('actions', 'text-right align-middle');

$table->headers = Mb2builderPartsApi::get_table_header($table->columns, ['timecreated', 'timemodified']);
$table->setup();

foreach ($sortorderitems as $item) {

    // Filling of information columns.
    $namecallback = '<div>';
    $namecallback .= $item->name;
    $namecallback .= '<div class="part-shortcode">';
    $namecallback .= '<input type="text" id="part_' . $item->id . '" name="part_' . $item->id . '" value="' .
    htmlentities('[mb2part id="' . $item->id . '"]') . '" readonly><button class="mb2part-getcode themereset" type="button"
    data-part="part_' . $item->id . '"><span class="text1">' . get_string('copycode', 'local_mb2builder') .
    '</span><span class="text2 hidden">' . get_string('copied', 'local_mb2builder') . '</span></button>';
    $namecallback .= '';
    $namecallback .= '</div>';
    $namecallback .= '</div>';

    // Created and modified by.
    $createduser = Mb2builderPartsApi::get_user($item->createdby);
    $createduserdate = userdate($item->timecreated, get_string('strftimedatemonthabbr', 'local_mb2builder'));
    $modifieduserdate = userdate($item->timemodified, get_string('strftimedatemonthabbr', 'local_mb2builder'));
    $modifieduser = Mb2builderPartsApi::get_user($item->modifiedby);
    $createdbyitem = $createduser ? '<div>' . $createduserdate . '</div><div class="mb2slides-admin-username">' .
    $createduser->firstname . ' ' . $createduser->lastname .  '</div>' : '&minus;';
    $modifiedbyitem = $modifieduser ? '<div>' . $modifieduserdate . '</div><div class="mb2slides-admin-username">' .
    $modifieduser->firstname . ' ' . $modifieduser->lastname .  '</div>' : '&minus;';

    // Link for editing.
    $editlink = new moodle_url($editpage, ['itemid' => $item->id, 'contextid' => $contextid]);
    $edititem = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit')));

    // Link to remove.
    $deletelink = new moodle_url($deletepage, ['deleteid' => $item->id, 'contextid' => $contextid]);
    $deleteitem = $OUTPUT->action_icon($deletelink, new pix_icon('t/delete', get_string('delete')));

    // Check if user can manage items.
    $actions = Mb2builderPartsApi::can_delete($item) ? $edititem . $deleteitem : '';

    $table->add_data([$namecallback, $createdbyitem, $modifiedbyitem, $actions]);

}

echo $OUTPUT->single_button(new moodle_url($editpage, ['contextid' => $contextid, 'partid' => md5(uniqid(rand(), 1)), 'name' =>
urlencode('Part ' . time())]), get_string('addpart', 'local_mb2builder'), 'get', ['class' => 'mb-3']);

echo Mb2builderPartsApi::part_filter();

$PAGE->requires->js_call_amd('local_mb2builder/admin', 'getPartCode');

// Display the table.
$table->print_html();
echo $OUTPUT->paging_bar($countitems, $page, $perpage, $baseurl);
echo $OUTPUT->footer();
