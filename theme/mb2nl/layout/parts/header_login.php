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

global $OUTPUT;

$bgimage = theme_mb2nl_pagebg_image();

$html = '';

$html .= '<body ' . $OUTPUT->body_attributes(theme_mb2nl_body_cls()) . '>';
$html .= $bgimage ? '<div class="page-bgimg lazy position-fixed" data-bg="' . $bgimage . '"></div>' : '';
$html .= $OUTPUT->standard_top_of_body_html();
$html .= theme_mb2nl_acsb_block();
$html .= theme_mb2nl_loading_screen();
$html .= '<div class="page-outer position-relative' . theme_mb2nl_bsfcls(1, 'column') . '" id="page"' .
theme_mb2nl_ajax_data_atts() . '>';
$html .= '<div class="pagelayout' . theme_mb2nl_bsfcls(1, 'row') . '">';
$html .= '<div class="pagelayout-b' . theme_mb2nl_bsfcls(1, 'column') . '">';
$html .= '<div class="pagelayout-content' . theme_mb2nl_bsfcls(1, 'column') . '">';
$html .= '<header class="login-header">';
$html .= '<div class="container-fluid">';
$html .= '<div class="row">';
$html .= '<div class="col-md-12">';
$html .= $OUTPUT->theme_part('logo');
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</header>';

echo $html;
