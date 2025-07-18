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
 * @package    local_mb2megamenu
 * @copyright  2019 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/classes/api.php');
require_once(__DIR__ . '/classes/helper.php');
require_once($CFG->libdir . '/adminlib.php');

// Optional parameters.
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$confirm = optional_param('confirm', false, PARAM_BOOL);

// Link generation.
$urlparameters = ['deleteid' => $deleteid];
$baseurl = new moodle_url('/local/mb2megamenu/delete.php', $urlparameters);
$managemenus = new moodle_url('/local/mb2megamenu/index.php');

// Configure the context of the page.
require_login();
admin_externalpage_setup('local_mb2megamenu_managemenus', '', null, $baseurl);
require_capability('local/mb2megamenu:manageitems', context_system::instance());

// The page title.
$titlepage = get_string('deletemenu', 'local_mb2megamenu');
$PAGE->navbar->add($titlepage);
$PAGE->set_heading($titlepage);
$PAGE->set_title($titlepage);
echo $OUTPUT->header();

$confirmed = ($confirm && data_submitted() && confirm_sesskey());

if (!$confirmed) {
    $optionsyes = ['action' => 'delete', 'deleteid' => $deleteid, 'sesskey' => sesskey(), 'confirm' => 1];
    $formcontinue = new single_button(new moodle_url($managemenus, $optionsyes), get_string('yes'));
    $formcancel = new single_button(new moodle_url($managemenus), get_string('no'), 'get');
    $callback = Mb2megamenuApi::get_record($deleteid);
    echo $OUTPUT->confirm(get_string('confirmdeletemenu', 'local_mb2megamenu', $callback->name), $formcontinue, $formcancel);
}

echo $OUTPUT->footer();
