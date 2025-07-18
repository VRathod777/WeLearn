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

mb2_add_shortcode('mb2pb_blog', 'mb2_shortcode_mb2pb_blog');

/**
 *
 * Method to define blog shortcode
 *
 * @return HTML
 */
function mb2_shortcode_mb2pb_blog($atts, $content=null) {

    global $PAGE;

    $atts2 = [
        'id' => 'blog',
        'limit' => 8,
        'sortcreated' => 0,
        'postexternal' => 1,
        'postids' => '',
        'exposts' => 0,
        'tagids' => '',
        'extags' => 0,
        'author' => 0,
        'date' => 1,
        'desc' => 1,
        // ...
        'columns' => 3,
        'sloop' => 0,
        'snav' => 1,
        'sdots' => 0,
        'autoplay' => 0,
        'pausetime' => 5000,
        'animtime' => 450,
        // ...
        'layout' => 2, // ... 1 - modern, 2 - columns, 3 - carousel
        'superpost' => 0,
        'gutter' => 'normal',
        'linkbtn' => 0,
        'btntext' => '',
        'prestyle' => 'none',
        'custom_class' => '',
        'mt' => 0,
        'mb' => 30,
        'tcolor' => '',
        'tfs' => 1.4,
        'template' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $cls = '';
    $listcls = '';
    $style = '';
    $sliderid = $a['template'] ? '' : uniqid('swiper_');

    $cls .= $a['custom_class'] ? ' ' . $a['custom_class'] : '';

    $blogopts = theme_mb2nl_page_builder_2arrays($atts, $atts2);
    $blogopts['lazy'] = 0;
    $posts2 = theme_mb2nl_get_blog_posts($blogopts);
    $firstpost = array_shift($posts2);

    // Carousel layout.
    $listcls .= $a['layout'] == 3 ? ' swiper-wrapper' : '';
    $listcls .= $a['layout'] == 2 ? ' theme-boxes theme-col-' . $a['columns'] : '';
    $listcls .= ' gutter-' . $a['gutter'];
    $listcls .= ' layout' . $a['layout'];

    $supercls = ' gutter-' . $a['gutter'];

    $containercls = $a['layout'] == 3 ? ' swiper' : '';

    $cls .= $a['template'] ? ' mb2-pb-template-blog' : '';

    $style .= ' style="';
    $style .= $a['mt'] ? 'margin-top:' . $a['mt'] . 'px;' : '';
    $style .= $a['mb'] ? 'margin-bottom:' . $a['mb'] . 'px;' : '';
    $style .= $a['tcolor'] ? '--mb2-pb-blog-tcolor:' . $a['tcolor'] . ';' : '';
    $style .= '--mb2-pb-blog-tfs:' . $a['tfs'] . 'rem;';
    $style .= '"';

    $output .= '<div class="mb2-pb-content mb2-pb-element mb2-pb-blog clearfix' . $cls . '"' . $style .
    theme_mb2nl_page_builder_el_datatts($atts, $atts2) . '>';
    $output .= '<div class="element-helper"></div>';
    $output .= theme_mb2nl_page_builder_el_actions('element', 'blog');
    $output .= '<div class="superpost' . $supercls . '">';
    $output .= theme_mb2nl_blog_template_item($firstpost, $blogopts, true);
    $output .= '</div>'; // Superpost.
    $output .= '<div id="' . $sliderid . '" class="mb2-pb-content-inner mb2-pb-element-inner clearfix' . $containercls . '">';
    $output .= theme_mb2nl_shortcodes_swiper_nav($sliderid);
    $output .= '<div class="mb2-pb-content-list' . $listcls . '"></div>'; // Content loaded via js.
    $output .= theme_mb2nl_shortcodes_swiper_pagenavnav();
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}
