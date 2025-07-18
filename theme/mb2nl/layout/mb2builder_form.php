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

$html = '';

$html .= $OUTPUT->doctype();
$html .= $OUTPUT->theme_part('head');
$html .= '<body ' . $OUTPUT->body_attributes() . '>';
$html .= $OUTPUT->standard_top_of_body_html();
$html .= '<div id="main-content">';
$html .= '<div id="page-content">';
$html .= $OUTPUT->main_content();
$html .= '<div class="sr-only">';
$html .= $OUTPUT->blocks('side-pre');
$html .= $OUTPUT->blocks('adminblock');
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= $OUTPUT->standard_end_of_body_html();
$html .= '</body>';
$html .= '</html>';

echo $html;

$PAGE->requires->js_amd_inline('require([\'theme_boost/loader\']);');
