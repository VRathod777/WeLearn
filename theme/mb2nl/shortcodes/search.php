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

mb2_add_shortcode('search', 'mb2pb_shortcode_search');

/**
 *
 * Method to define shortcode
 *
 * @return HTML
 */
function mb2pb_shortcode_search($atts, $content = null) {

    global $CFG;

    $atts2 = [
        'id' => 'search',
        'placeholder' => get_string('searchcourses'),
        'global' => 0,
        'rounded' => 0,
        'width' => 750,
        'size' => 'n',
        'border' => 1,
        'borderw' => 1,
        'mt' => 0,
        'mb' => 30,
        'bgcolor' => '',
        'color' => '',
        'bocolor' => '',

        'btncolor' => '',
        'btnhcolor' => '',
        'btnbgcolor' => '',
        'btnbghcolor' => '',

        'custom_class' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $style = '';
    $btnstyle = '';
    $inputstyle = '';
    $cls = '';
    $cls .= ' border' . $a['border'];

    $formid = uniqid('searchform_');
    $inputname = $a['global'] ? 'q' : 'search';

    $action = new moodle_url('/course/search.php');

    if ($a['global'] && isset($CFG->enableglobalsearch) && $CFG->enableglobalsearch) {
        $action = new moodle_url('/search/index.php');
    }

    $cls .= $a['custom_class'] ? ' ' . $a['custom_class'] : '';
    $cls .= ' size' . $a['size'];
    $cls .= ' rounded' . $a['rounded'];

    $style .= ' style="';
    $style .= $a['mt'] ? 'margin-top:' . $a['mt'] . 'px;' : '';
    $style .= $a['mb'] ? 'margin-bottom:' . $a['mb'] . 'px;' : '';
    $style .= $a['width'] ? 'max-width:' . $a['width'] . 'px;' : '';
    $style .= $a['bgcolor'] ? '--mb2-pb-searchbg:' . $a['bgcolor'] . ';' : '';
    $style .= $a['bocolor'] ? '--mb2-pb-searchbcolor:' . $a['bocolor'] . ';' : '';
    $style .= $a['color'] ? '--mb2-pb-searchcolor:' . $a['color'] . ';' : '';
    $style .= '--mb2-pb-searchbw:' . $a['borderw'] . 'px;';
    $style .= '"';

    $btnstyle .= ' style="';
    $btnstyle .= $a['btncolor'] ? '--mb2-pb-btn-color:' . $a['btncolor'] . ';' : '';
    $btnstyle .= $a['btnhcolor'] ? '--mb2-pb-btn-hcolor:' . $a['btnhcolor'] . ';' : '';
    $btnstyle .= $a['btnbgcolor'] ? '--mb2-pb-btn-bgcolor:' . $a['btnbgcolor'] . ';' : '';
    $btnstyle .= $a['btnbghcolor'] ? '--mb2-pb-btn-bghcolor:' . $a['btnbghcolor'] . ';' : '';
    $btnstyle .= '"';

    $content = $content ? $content : 'Alert text here.';
    $atts2['text'] = $content;

    $output .= '<div class="mb2-pb-search' . $cls . '"' . $style . '>';
    $output .= '<form id="' . $formid . '" action="' . $action . '" class="' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';
    $output .= '<input id="' . $formid . '_search" type="text" value="" placeholder="' . $a['placeholder'] . '" name="' .
    $inputname . '" class="' . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
    $output .= '<button class="mb2-pb-btn p-0' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" type="submit"' . $btnstyle .
    '><span class="sr-only">' . get_string('search') . '</span><i class="fa fa-search"></i></button>';
    $output .= '</form>';
    $output .= '</div>';

    return $output;

}
