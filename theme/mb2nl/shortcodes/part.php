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
 */

defined('MOODLE_INTERNAL') || die();

mb2_add_shortcode('mb2part', 'mb2_shortcode_mb2part');

/**
 *
 * Method to define shortcode
 *
 * @return HTML
 */
function mb2_shortcode_mb2part($atts, $content = null) {

    global $PAGE, $CFG;

    $atts2 = ['id' => 0];

    $a = mb2_shortcode_atts($atts2, $atts);
    $a['id'] = (int) $a['id'];
    $output = '';

    if (!theme_mb2nl_check_builder()) {
        return;
    }

    // Get part api file.
    if (!class_exists('Mb2builderPartsApi')) {
        require($CFG->dirroot . '/local/mb2builder/classes/parts_api.php');
    }

    if (!Mb2builderPartsApi::is_partid($a['id'])) {
        return '<span class="d-block align-center pt-5 pb-5">' . get_string('partnoexists', 'local_mb2builder',
        ['id' => $a['id']]) . '</span>';
    }

    $output .= theme_mb2nl_mb2part_editlink($a['id']);

    // Get cached footer content.
    $cache = cache::make('local_mb2builder', 'partdata');

    if (!$cache->get($a['id'])) {
        // Get footer record and set cache content.
        $part = Mb2builderPartsApi::get_record($a['id']);
        $part->democontent = ''; // We don't need demo content in the cache file.
        $cache->set($part->id, $part);
    }

    $output .= theme_mb2nl_builder_content(json_decode($cache->get($a['id'])->content), true);

    return $output;

}



/**
 *
 * Method to get part link
 *
 * @return HTML
 */
function theme_mb2nl_mb2part_editlink($id) {

    global $CFG, $PAGE;
    $output = '';
    $context = context_system::instance();
    $viewhidden = has_capability('moodle/course:viewhiddenactivities', $context);

    if (!has_capability('local/mb2builder:manageparts', $context)) {
        return;
    }

    // Get part api file.
    if (!class_exists('Mb2builderPartsApi')) {
        require($CFG->dirroot . '/local/mb2builder/classes/parts_api.php');
    }

    // This condidtion can be removed.
    if (!method_exists(new Mb2builderPartsApi(), 'is_my_part') && !$viewhidden) {
        return;
    }

    if (!Mb2builderPartsApi::is_my_part($id) && !$viewhidden) {
        return;
    }

    $linkparams = [
        'itemid' => $id,
        'returnurl' => $PAGE->url->out_as_local_url(),
    ];

    $output .= '<div class="builder-links">';
    $output .= '<a class="mb2pb-editfooter" href="' . new moodle_url('/local/mb2builder/edit-part.php', $linkparams) . '">';
    $output .= get_string('editpart', 'local_mb2builder');
    $output .= '</a>';
    $output .= '</div>';

    return $output;

}
