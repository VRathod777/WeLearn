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
require_once(__DIR__ . '/form-part.php');
require_once(__DIR__ . '/classes/builder.php');
require_once(__DIR__ . '/classes/parts_api.php');
require_once($CFG->libdir . '/adminlib.php');

// Optional parameters.
$itemid = optional_param('itemid', 0, PARAM_INT);
$contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);
$name = optional_param('name', '', PARAM_TEXT);
$part = optional_param('part', 1, PARAM_INT);
$partid = optional_param('partid', '', PARAM_TEXT);
$returnurl = optional_param('returnurl', '/local/mb2builder/parts.php', PARAM_LOCALURL);

$context = context::instance_by_id($contextid, MUST_EXIST);

// Link generation.
$urlparameters = ['itemid' => $itemid, 'contextid' => $contextid, 'returnurl' => $returnurl, 'name' => $name, 'partid' => $partid,
'part' => $part];
$baseurl = new moodle_url('/local/mb2builder/edit-part.php', $urlparameters);
$returnurl = new moodle_url($returnurl, ['contextid' => $contextid]);

// Configure the context of the part.
require_login();
require_capability('local/mb2builder:manageparts', $context);
$PAGE->set_context($context);
$PAGE->set_url($baseurl);

// Create an editing form.
$mform = new local_mb2builder_part_edit_form($PAGE->url);

// Getting the data.
$formopts = ['itemid' => $itemid, 'contextid' => $contextid, 'name' => $name, 'partid' => $partid];
$recordata = Mb2builderPartsApi::set_record_data($formopts);
$data = Mb2builderPartsApi::get_form_data($mform, $recordata);

// Now user build a new part.
// We have to save it in database.
// This is require for saving demo content of new part (with itemid=0) in ajax.
if (!$itemid && $partid) {
    // Make sure that record doesn't exist.
    if (!Mb2builderPartsApi::is_partid($partid)) {
        Mb2builderPartsApi::add_record($recordata);
    }
}

// Cancel processing.
if ($mform->is_cancelled()) {
    $message = '';
    // Itemid doesn't exist and user click the cancel button,
    // so We have to delete the part record.
    if (!$itemid && $partid) {
        $recordtodelete = Mb2builderPartsApi::get_record($partid, true);
        // Delete a part record and don't recreate cache.
        Mb2builderPartsApi::delete($recordtodelete->id, false);
    } else if ($itemid) {
        // In this case user edit existing part.
        // We have to set democontent the same as content.
        // This is require becaue in next part editing builder will load democontent.
        $itemtoupdate = Mb2builderPartsApi::get_record($itemid);
        $itemtoupdate->democontent = $itemtoupdate->content;
        Mb2builderPartsApi::update_record_data($itemtoupdate);
    }
}

// Processing of received data.
if (!empty($data)) {
    if ($itemid) {
        // Update the part record and recreate cache.
        Mb2builderPartsApi::update_record_data($data, true, true);
        $message = get_string('partupdated', 'local_mb2builder', ['title' => $data->name]);
    } else if (!$itemid && $partid) {
        // Now we need to get record ID for update record in database.
        // We don't have item ID in url because user now create new part.
        // part exists already in database but in UREL we don't have the part id.
        $recordforid = Mb2builderPartsApi::get_record($partid, true);
        $data->id = $recordforid->id;

        // Update the part record and recreate cache.
        Mb2builderPartsApi::update_record_data($data, true, true);
        $message = get_string('partupdated', 'local_mb2builder', ['title' => $data->name]);
    } else {
        // Update the part record and recreate cache.
        Mb2builderPartsApi::add_record($data, true);
        $message = get_string('partcreated', 'local_mb2builder');
    }
}

// Then redirect to to the part.
if (isset($message)) {
    redirect($returnurl, $message);
}

// The part title.
$titlepart = get_string('editpart', 'local_mb2builder');
$PAGE->set_pagelayout('mb2builder_form');
$PAGE->navbar->add($titlepart);
$PAGE->set_title($titlepart);
echo $OUTPUT->header();
echo $OUTPUT->heading($titlepart);

// Now We have to get the 'partid'. It's required for image.
$ispartid = $partid ? $partid : Mb2builderPartsApi::get_record($itemid)->partid;

// Check if user can edit content part.
// Non-admin users canedit only own content parts.
$isid = $itemid ? $itemid : $ispartid;
if (!Mb2builderPartsApi::can_edit_part($isid)) {
    echo '<div style="text-align:center;color:#fff;">' . get_string('cannoteditpart', 'local_mb2builder', $isid) . '</div>';
} else {
    // Displays the form.
    $mform->display();
    echo mb2builderBuilder::get_demo_iframe(['itemid' => $itemid, 'contextid' => $contextid, 'partid' => $ispartid,
    'part' => $part]);
}

echo $OUTPUT->footer();
