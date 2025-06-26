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
 * @package   theme_mb2nl
 * @copyright 2017 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license   PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 *
 */

defined('MOODLE_INTERNAL') || die();

global $ADMIN, $OUTPUT, $PAGE;

$html = '';

// Disable secondary navigation.
// New Learning provide custom navigation.
if (!theme_mb2nl_theme_setting($PAGE, 'secnav')) {
    $PAGE->set_secondary_navigation(false);
}

$PAGE->requires->data_for_js('mb2nljs', theme_mb2nl_php2js());
$PAGE->requires->js_call_amd('theme_mb2nl/actions', 'init');
$PAGE->requires->js_call_amd('theme_mb2nl/access', 'focusClass');
$PAGE->requires->js_call_amd('theme_mb2nl/sidebars', 'sidebarToggle');
$PAGE->requires->js_call_amd('theme_mb2nl/scrollpos', 'panelLink');
$PAGE->requires->js_call_amd('theme_mb2nl/megamenu', 'setWrapPos');
$PAGE->requires->js_call_amd('theme_mb2nl/megamenu', 'toggleSubmenus');
$PAGE->requires->js_call_amd('theme_mb2nl/stickynav', 'init');
$PAGE->requires->js_call_amd('theme_mb2nl/tgsdb', 'init');

if (theme_mb2nl_theme_setting($PAGE, 'showmorebtn')) {
    $PAGE->requires->js_call_amd('theme_mb2nl/morelessbtn', 'init');
    $PAGE->requires->js_call_amd('theme_mb2nl/morelessbtn', 'toggleContent');
}

if (theme_mb2nl_is_notice_plugin()) {
    $PAGE->requires->js_call_amd('theme_mb2nl/mb2notices', 'closeNotice');
}

if (has_capability('moodle/site:config', context_system::instance())) {
    $PAGE->requires->js_call_amd('theme_mb2nl/settings', 'toggleAll');
    $PAGE->requires->js_call_amd('theme_mb2nl/settings', 'toggleCat');
}

theme_mb2nl_scripts();
theme_mb2nl_skiplinks();
theme_mb2nl_fpredirect();

$html .= '<html ' . $OUTPUT->htmlattributes() . ' class="html' . theme_mb2nl_acsb_cls() . '">';
$html .= '<head>';
$html .= theme_mb2nl_ganalytics();
$html .= '<title>' . $OUTPUT->page_title() . '</title>';
$html .= theme_mb2nl_favicon();
$html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
$html .= theme_mb2nl_gfonts();
$html .= $OUTPUT->standard_head_html();
$html .= theme_mb2nl_builder_style();
$html .= theme_mb2nl_acsb_style();
$html .= theme_mb2nl_style();
$html .= '</head>';

echo $html;
