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

global $gl0bmb2blogpost;

$gl0bmb2blogpost = 0;

echo $OUTPUT->doctype();
echo $OUTPUT->theme_part('head');
echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">';

echo $OUTPUT->theme_part('header');

if ($courselayout = theme_mb2nl_course_layout()) {
    include($CFG->dirroot . theme_mb2nl_themedir() . '/mb2nl/layout/coulmns2_course' . $courselayout . '.php');
} else {
    include($CFG->dirroot . theme_mb2nl_themedir() . '/mb2nl/layout/columns2_normal.php');
}
