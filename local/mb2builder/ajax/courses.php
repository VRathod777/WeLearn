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

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get builder files.
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/api.php');

// Require theme lib files.
require_once(LOCAL_MB2BUILDER_PATH_THEME . '/lib.php');

// Get url params from ajax call.
$urloptions = $_GET;

$context = context_system::instance();
$PAGE->set_url('/local/mb2builder/ajax/courses.php', $urloptions);
$PAGE->set_context($context);

require_login();
require_sesskey();

// Get default settings.
$options = [
    'id' => 'courses',
    'limit' => 8,
    'catids' => '',
    'courseids' => '',
    'excourses' => 0,
    'excats' => 0,
    'tagids' => '',
    'extags' => 0,
    'instids' => '',
    'exinst' => 0,
    'carousel' => 0,
    'colnum' => 3,
    'sloop' => 0,
    'snav' => 1,
    'sdots' => 0,
    'autoplay' => 0,
    'pausetime' => 5000,
    'animtime' => 450,
    'desclimit' => 25,
    'titlelimit' => 6,
    'gridwidth' => 'normal',
    'linkbtn' => 0,
    'btntext' => '',
    'prestyle' => 0,
    'custom_class' => '',
    'colors' => '',
    'cistyle' => 'n',
    'crounded' => 1,
    'mt' => 0,
    'mb' => 30,
    'coursestudentscount' => 1,
    'coursinstructor' => 1,
    'courseprice' => 1,
    'template' => '',
];

$options = mb2builderApi::get_options($options, $urloptions);
$options['lazy'] = 0;

if (local_mb2builder_get_theme_name() === 'mb2nl' ) {
    if (function_exists( 'theme_mb2nl_get_courses' ) && function_exists( 'theme_mb2nl_shortcodes_course_template' ) ) {
        $items = theme_mb2nl_get_courses( $options );
        echo theme_mb2nl_shortcodes_course_template($items, $options, true);
    } else {
        echo 'Function doesn’t exist (theme_mb2nl_get_courses, theme_mb2nl_shortcodes_course_template).
        Update theme and page builder plugin.';
    }
}
die();
