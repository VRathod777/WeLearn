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

global $PAGE, $COURSE;

$mb2section = theme_mb2nl_mb2fields_filed('mb2section') && theme_mb2nl_theme_setting($PAGE, 'csection');
$urlctab = optional_param('ctab', '', PARAM_ALPHANUMEXT);

// Start HTML.
$html = '';

$html .= $vars['layout'] == 3 ? theme_mb2nl_course_section_boxes() : theme_mb2nl_tabcontent_topics();

if (preg_match('@mb2section@', $urlctab)) {
    $html .= theme_mb2nl_course_csection_html($urlctab);
} else if ($urlctab === 'reviews') {
    $html .= theme_mb2nl_course_reviews_html();
} else if ($urlctab === 'courseinfo') {
    $html .= theme_mb2nl_course_info_html();
}

echo $html;
