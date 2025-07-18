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


mb2_add_shortcode('gap', 'mb2_shortcode_gap');

/**
 *
 * Method to define shortcode
 *
 * @return HTML
 */
function mb2_shortcode_gap ($atts, $content=null) {

    $atts2 = [
        'smallscreen' => '1',
        'size' => '20',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $cls = $a['smallscreen'] == 0 ? ' no-smallscreen' : '';
    $style = $a['size'] != '' ? ' style="height:' . $a['size'] . 'px;"' : '';

    return '<div class="mb2pb-gap gap clearfix' . $cls . '"' . $style . '></div>';

}
