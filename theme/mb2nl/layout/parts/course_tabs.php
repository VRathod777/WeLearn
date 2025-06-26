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

global $CFG, $PAGE, $COURSE;

$urlid = optional_param('id', 0, PARAM_INT);
$ctab = optional_param('ctab', '', PARAM_ALPHANUMEXT);
$cls = $vars['pos'] === 'sidebar' ? theme_mb2nl_bsfcls(1, 'column') : theme_mb2nl_bsfcls(1, 'wrap', 'center', 'center');
$linkcls = $vars['pos'] === 'sidebar' ? theme_mb2nl_bsfcls(2, '', '', 'center') . ' fwmedium w-100' :
theme_mb2nl_bsfcls(2, 'column', 'center', 'center') . ' h4 m-0';

// Start HTML.
$html = '';

if (theme_mb2nl_is_coursetabs()) {
    $html .= '<div class="course-tabs pos-' . $vars['pos'] . '">';
    $html .= '<ul class="course-tabs-list p-0' . $cls . '">';

    foreach (theme_mb2nl_is_coursetab_items() as $item) {

        if ($item['id'] === $ctab) {
            $isactive = 'active';
        } else {
            $isactive = '';
        }

        $html .= '<li class="tab-item item-' . $item['id'] . '">';
        $html .= '<a href="' . new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => $item['id']]) . '" class="' .$isactive.
        $linkcls . '"><span class="btn-icon" aria-hidden="true"><i class="' . $item['icon'] .
        '"></i></span> <span class="btn-intext">' . $item['str'] . '</span></a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    $html .= '</div>';
}

echo $html;
