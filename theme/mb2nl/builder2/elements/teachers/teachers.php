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

mb2_add_shortcode('mb2pb_teachers', 'mb2_shortcode_mb2pb_teachers');

/**
 *
 * Method to define shortcode
 *
 * @return HTML
 */
function mb2_shortcode_mb2pb_teachers($atts, $content=null) {

    global $PAGE;

    $atts2 = [
        'id' => 'teachers',
        'limit' => 8,
        'teacherids' => '',
        'exteachers' => 0,
        'carousel' => 0,
        'columns' => 3,
        'sloop' => 0,
        'snav' => 1,
        'sdots' => 0,
        'autoplay' => 0,
        'pausetime' => 5000,
        'animtime' => 450,
        'desclimit' => 25,
        'titlelimit' => 6,
        'gutter' => 'normal',
        'custom_class' => '',
        'desc' => 0,
        'mt' => 0,
        'mb' => 30,
        'template' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $cls = '';
    $listcls = '';
    $colcls = '';
    $style = '';
    $sliderid = $a['template'] ? '' : uniqid('swiper_');

    // Set column style.
    $col = 0;
    $colstyle = '';
    $liststyle = '';

    $teacheropts = theme_mb2nl_page_builder_2arrays($atts, $atts2);

    // Carousel layout.
    $listcls .= $a['carousel'] ? ' swiper-wrapper' : '';
    $listcls .= ! $a['carousel'] ? ' theme-boxes theme-col-' . $a['columns'] : '';
    $listcls .= ! $a['carousel'] ? ' gutter-' . $a['gutter'] : '';

    $containercls = $a['carousel'] ? ' swiper' : '';

    $cls .= $a['template'] ? ' mb2-pb-template-teachers' : '';

    if ($a['mt'] || $a['mb']) {
        $style .= ' style="';
        $style .= $a['mt'] ? 'margin-top:' . $a['mt'] . 'px;' : '';
        $style .= $a['mb'] ? 'margin-bottom:' . $a['mb'] . 'px;' : '';
        $style .= '"';
    }

    $output .= '<div class="mb2-pb-content mb2-pb-element mb2-pb-teachers clearfix' . $cls . '"' . $style .
    theme_mb2nl_page_builder_el_datatts($atts, $atts2) . '>';
    $output .= '<div class="element-helper"></div>';
    $output .= theme_mb2nl_page_builder_el_actions('element', 'teachers');
    $output .= '<div id="' . $sliderid . '" class="mb2-pb-content-inner mb2-pb-element-inner clearfix' . $containercls . '">';
    $output .= theme_mb2nl_shortcodes_swiper_nav($sliderid);
    $output .= '<div class="mb2-pb-content-list' . $listcls . '"></div>'; // Content loaded via js.
    $output .= theme_mb2nl_shortcodes_swiper_pagenavnav();
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}
