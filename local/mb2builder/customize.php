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

// Moodle classess.
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');


// Builder classess.
require_once(__DIR__ . '/classes/pages_api.php');
require_once(__DIR__ . '/classes/footers_api.php');
require_once(__DIR__ . '/classes/parts_api.php');
require_once(__DIR__ . '/classes/builder.php');

require_login();

// Optional parameters.
$itemid = optional_param('itemid', 0, PARAM_INT);
$contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);
$footer = optional_param('footer', 0, PARAM_INT);
$part = optional_param('part', 0, PARAM_INT);
$pageid = optional_param('pageid', '', PARAM_TEXT);
$footerid = optional_param('footerid', '', PARAM_TEXT);
$partid = optional_param('partid', '', PARAM_TEXT);
$context = context::instance_by_id($contextid, MUST_EXIST);

// Link generation.
$urlparameters = ['itemid' => $itemid, 'pageid' => $pageid, 'footer' => $footer, 'part' => $part, 'footerid' => $footerid,
'partid' => $partid, 'contextid' => $contextid];
$baseurl = new moodle_url('/local/mb2builder/customize.php', $urlparameters);

// Configure the context of the page.
require_login();

if ($part) {
    require_capability('local/mb2builder:manageparts', $context);
} else if ($footer) {
    require_capability('local/mb2builder:managefooters', $context);
} else {
    require_capability('local/mb2builder:managepages', $context);
}

$PAGE->set_context($context);
$PAGE->set_url('/local/mb2builder/customize.php');

$data = '';
// Get page data by itemid.
if ($itemid && !$footer && !$part) {
    $data = Mb2builderPagesApi::get_record($itemid);
} else if (!$itemid && $pageid) {
    // Get page data by pageid.
    // It's require if user don't save page and refresh iframe after ajax saving.
    // remember that in this case itemid param in URL is still empty.
    $data = Mb2builderPagesApi::get_record($pageid, true);
} else if ($itemid && $footer) {
    // Now get footer data by ID.
    $data = Mb2builderFootersApi::get_record($itemid);
} else if (!$itemid && $footerid) {
    // Get footer data by footerid.
    // It's require if user don't save page and refresh iframe after ajax saving
    // remember that in this case itemid param in URL is still empty.
    $data = Mb2builderFootersApi::get_record($footerid, true);
} else if ($itemid && $part) {
    // Now get part data by ID.
    $data = Mb2builderPartsApi::get_record($itemid);
} else if (!$itemid && $partid) {
    // Get part data by partid.
    // It's require if user don't save page and refresh iframe after ajax saving
    // remember that in this case itemid param in URL is still empty.
    $data = Mb2builderPartsApi::get_record($partid, true);
}

// The page title.
$titlepage = get_string('customize', 'local_mb2builder');
$PAGE->set_pagelayout('mb2builder');
$PAGE->navbar->add($titlepage);
$PAGE->set_heading($titlepage);
$PAGE->set_title($titlepage);
echo $OUTPUT->header();

if (!mb2builderBuilder::check_shortcodes_filter()) {
    echo get_string('nofilter', 'local_mb2builder');
} else if (!mb2builderBuilder::check_urltolink_filter()) {
    echo get_string('urltolink', 'local_mb2builder');
} else {
    // Show page builder.
    echo format_text(mb2builderBuilder::get_builder_layout($data, $footer, $part), FORMAT_HTML);

    // Show page builder settings.
    echo mb2builderApi::get_builder_settings(['footer' => $footer, 'part' => $part]);

    if ($part) {
        echo Mb2builderPartsApi::get_form_democontent(['itemid' => $itemid, 'partid' => $partid]);
        $bckuptype = 3;
    } else if ($footer) {
        echo Mb2builderFootersApi::get_form_democontent(['itemid' => $itemid, 'footerid' => $footerid]);
        $bckuptype = 2;
    } else {
        $bckuptype = 1;
        echo Mb2builderPagesApi::get_form_democontent(['itemid' => $itemid, 'pageid' => $pageid]);
    }

    echo mb2builderBuilder::manage_layouts();
}

$PAGE->requires->js_call_amd('local_mb2builder/icons', 'themeIcons');
$PAGE->requires->js_call_amd('local_mb2builder/images', 'previewImages', [1, $itemid, $pageid, $footerid, $partid]); // Item.
$PAGE->requires->js_call_amd('local_mb2builder/images', 'previewImages', [2, $itemid, $pageid, $footerid, $partid]); // Global.
$PAGE->requires->js_call_amd('local_mb2builder/backup', 'backupList'); // Load all backups.
$PAGE->requires->js_call_amd('local_mb2builder/backup', 'createBackup');
$PAGE->requires->js_call_amd('local_mb2builder/backup', 'deleteBackup');
$PAGE->requires->js_call_amd('local_mb2builder/backup', 'uploadBackup');
$PAGE->requires->js_call_amd('local_mb2builder/admin', 'settingsTab');

echo $OUTPUT->footer();
