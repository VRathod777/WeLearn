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

mb2_add_shortcode('banner', 'mb2_shortcode_banner');

/**
 *
 * Method to define banner shortcode
 *
 * @return HTML
 */
function mb2_shortcode_banner($atts, $content = null) {

    $atts2 = [
        'title' => '',
        'iscontent' => 1,
        'padding' => 'normal',
        // ...
        'linkbtn' => 0,
        'link' => '#',
        'link_target' => 0,
        'linktext' => get_string('readmorefp', 'local_mb2builder'),
        // ...
        'fs' => 1,
        'fw' => 'global',
        'lh' => 'global',
        'tmt' => 1.3,
        // ...
        'tfs' => 1.4,
        'tfw' => 'global',
        'tlh' => 'global',
        // ...
        'btncolor' => '',
        'btnbgcolor' => '',
        'btnbocolor' => '',
        // ...
        'btnhcolor' => '',
        'btnhbgcolor' => '',
        'btnhbocolor' => '',
        // ...
        'btnsize' => 'lg',
        'btnrounded' => 0,
        'btnborder' => 0,
        'btnmt' => 1.3,
        // ...
        'cwidth' => 450,
        'height' => 350,
        'halign' => 'start',
        'rounded' => 1,
        'shadow' => 0,
        // ...
        'tcolor' => '',
        'color' => '',
        'bgcolor' => '',
        // ...
        'bgimage' => '',
        // ...
        'image' => '',
        'imgvalign' => 'center',
        'imgwidth' => 350,
        'imghpos' => 0,
        'imgpos' => 'right',
        'imgmt' => 0,
        'imgcrop' => 0,
        'imgonsm' => 1,
        // ...
        'mt' => 0,
        'mb' => 30,
        // ...
        'custom_class' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $style = '';
    $cls = '';
    $imgcls = '';
    $btncls = '';
    $btnstyle = '';
    $img3style = '';
    $ccls = '';
    $tcls = '';

    $content = ! $content ? '<p>Element content here...</p>' : $content;
    $atts2['content'] = $content;

    $cls .= $a['custom_class'] ? ' ' . $a['custom_class'] : '';
    $cls .= ' align-items-' . $a['halign'];
    $cls .= ' imgpos-' . $a['imgpos'];
    $cls .= ' iscontent' . $a['iscontent'];
    $cls .= ' imgcrop' . $a['imgcrop'];
    $cls .= ' imgvalign' . $a['imgvalign'];
    $cls .= ' imgonsm' . $a['imgonsm'];
    $cls .= ' rounded' . $a['rounded'];
    $cls .= ' shadow' . $a['shadow'];
    $cls .= ' padding' . $a['padding'];

    $btncls .= ' size' . $a['btnsize'];
    $btncls .= ' rounded' . $a['btnrounded'];
    $btncls .= ' btnborder' . $a['btnborder'];

    $ccls .= ' ' . theme_mb2nl_tsize_cls($a['fs']);
    $ccls .= ' fw' . $a['fw'];
    $ccls .= ' lh' . $a['lh'];

    $tcls .= ' ' . theme_mb2nl_tsize_cls($a['tfs']);
    $tcls .= ' fw' . $a['tfw'];
    $tcls .= ' lh' . $a['tlh'];

    $target = $a['link_target'] ? ' target="_blank"' : '';

    $style .= ' style="';
    $style .= $a['mt'] ? 'margin-top:' . $a['mt'] . 'px;' : '';
    $style .= $a['mb'] ? 'margin-bottom:' . $a['mb'] . 'px;' : '';
    $style .= $a['bgcolor'] ? '--mb-pb-ba_bgcolor:' . $a['bgcolor'] . ';' : '';
    $style .= $a['tcolor'] ? '--mb-pb-ba_tcolor:' . $a['tcolor'] . ';' : '';
    $style .= $a['color'] ? '--mb-pb-ba_color:' . $a['color'] . ';' : '';
    $style .= '--mb-pb-ba_imghpos:' . $a['imghpos'] . '%;';
    $style .= '--mb-pb-ba_cwidth:' . $a['cwidth'] . 'px;';

    $style .= $a['bgimage'] ? 'background-image:url(\'' . $a['bgimage'] . '\');' : '';
    $style .= 'min-height:' . $a['height'] . 'px;';
    $style .= '"';

    $btnstyle .= ' style="';
    $btnstyle .= $a['btncolor'] ? '--mb2-pb-btn-color:' . $a['btncolor'] . ';' : '';
    $btnstyle .= $a['btnbgcolor'] ? '--mb2-pb-btn-bgcolor:' . $a['btnbgcolor'] . ';' : '';
    $btnstyle .= $a['btnbocolor'] ? '--mb2-pb-btn-borcolor:' . $a['btnbocolor'] . ';' : '';
    $btnstyle .= $a['btnhcolor'] ? '--mb2-pb-btn-hcolor:' . $a['btnhcolor'] . ';' : '';
    $btnstyle .= $a['btnhbgcolor'] ? '--mb2-pb-btn-bghcolor:' . $a['btnhbgcolor'] . ';' : '';
    $btnstyle .= $a['btnhbocolor'] ? '--mb2-pb-btn-borhcolor:' . $a['btnhbocolor'] . ';' : '';
    $btnstyle .= '"';

    $img3style .= ' style="';
    $img3style .= 'width:' . $a['imgwidth'] . 'px;';
    $img3style .= '--mb2-pb-ba_imgmt:' . $a['imgmt'] . 'px;';
    $img3style .= '"';

    $output .= '<div class="mb2-pb-ba position-relative' . $cls .
    theme_mb2nl_bsfcls(1, 'column', 'center', '') . '"' . $style . '>';
    $output .= '<div class="mb2-pb-ba_inner position-relative">';

    if ($a['title']) {
        $output .= '<div class="mb2-pb-ba_title">';
        $output .= '<h4 class="ba_title title mb-0' . $tcls . '" style="font-size:' .
        $a['tfs'] . 'rem;"><span class="title-text">' . theme_mb2nl_format_str($a['title']) . '</span></h4>';
        $output .= '</div>'; // ...mb2-pb-ba_title
    }

    if ($a['iscontent']) {
        $output .= '<div class="mb2-pb-ba_content' . $ccls . '" style="font-size:' .
        $a['fs'] . 'rem;margin-top:' . $a['tmt'] . 'rem;">';
        $output .= theme_mb2nl_format_txt(urldecode($content), FORMAT_HTML);
        $output .= '</div>'; // ...mb2-pb-ba_content
    }

    if ($a['linkbtn']) {
        $output .= '<div class="mb2-pb-ba_btn" style="margin-top:' . $a['btnmt'] . 'rem;">';
        $output .= '<a href="' . $a['link'] . '" class="mb2-pb-btn' . $btncls . '"' . $btnstyle . $target . '>' .
        theme_mb2nl_format_str($a['linktext']) . '</a>';
        $output .= '</div>'; // ...mb2-pb-ba_content
    }

    $output .= '</div>'; // ...mb2-pb-ba_inner

    if ($a['image']) {
        $output .= '<div class="ba_img d-flex position-absolute w-100 h-100" aria-hidden="true">';
        $output .= '<div class="ba_img2 d-flex position-relative w-100' . $imgcls . '">';
        $output .= '<div class="ba_img3 position-absolute"' . $img3style . '>';
        $output .= '<img class="ba_img_img lazy" src="' . theme_mb2nl_lazy_plc(true) . '" data-src="' . $a['image'] . '" alt="' .
        theme_mb2nl_format_str($a['title']) . '">';
        $output .= '</div>'; // ...ba_img3
        $output .= '</div>'; // ...ba_img2
        $output .= '</div>'; // ...ba_img
    }

    $output .= '</div>';

    return $output;

}
