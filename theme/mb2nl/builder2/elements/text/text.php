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

mb2_add_shortcode('mb2pb_text', 'mb2_shortcode_mb2pb_text');

/**
 *
 * Method to define shortcode
 *
 * @return HTML
 */
function mb2_shortcode_mb2pb_text($atts, $content = null) {

    $atts2 = [
        'id' => 'text',
        'align' => 'none',
        'size' => 'n',
        'sizerem' => 1,
        'color' => '',
        'showtitle' => 0,

        'fwcls' => 'global',
        'lhcls' => 'global',
        'lspacing' => 0,
        'wspacing' => 0,
        'tupper' => 0,

        'tfwcls' => 'global',
        'tlhcls' => 'global',
        'tlspacing' => 0,
        'twspacing' => 0,
        'tsizerem' => 1.4,
        'tcolor' => '',
        'upper' => 0,
        'title' => 'Title text',
        'bgcolor' => '',
        'bgcolor2' => '',
        'mt' => 0,
        'mb' => 30,
        'pv' => 0,
        'ph' => 0,
        'tmb' => 30,
        'width' => 2000,
        'rounded' => 0,
        'gradient' => 0,
        'graddeg' => 180,

        'button' => 0,
        'btype' => 'primary',
        'bsize' => 'normal',
        'link' => '#',
        'target' => 0,
        'brounded' => 0,
        'bmt' => 0,
        'bborder' => 0,
        'btext' => "Read more",
        'bfwcls' => 'global',

        'btncolor' => '',
        'btnbgcolor' => '',
        'btnbghcolor' => '',
        'btnhcolor' => '',
        'btnborcolor' => '',
        'btnborhcolor' => '',

        'scheme' => 'light',
        'custom_class' => '',
        'template' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $style = '';
    $innerstyle = '';
    $typostyle = '';
    $typosttyle = '';
    $fontcls = '';
    $titlecls = '';
    $btncls = '';
    $btnstyle = '';

    $cls = $a['custom_class'] ? ' ' . $a['custom_class'] : '';
    $cls .= ' align-' . $a['align'];
    $cls .= ' text-' . $a['color'];
    $cls .= ' title' . $a['showtitle'];
    $cls .= ' linkbtn' . $a['button'];
    $cls .= ' rounded' . $a['rounded'];
    $cls .= ' gradient' . $a['gradient'];
    $cls .= ' ' . $a['scheme'];

    $templatecls = $a['template'] ? ' mb2-pb-template-text' : '';

    $fontcls .= ' ' . theme_mb2nl_tsize_cls($a['sizerem']);
    $fontcls .= ' upper' . $a['upper'];
    $fontcls .= ' fw' . $a['fwcls'];
    $fontcls .= ' lh' . $a['lhcls'];

    $titlecls .= ' ' . theme_mb2nl_tsize_cls($a['sizerem']);
    $titlecls .= ' upper' . $a['tupper'];
    $titlecls .= ' fw' . $a['tfwcls'];
    $titlecls .= ' lh' . $a['tlhcls'];

    // Make content default text.
    $content = ! $content ? '<p>Element content here.</p>' : $content;
    $atts2['content'] = $content;

    $btncls .= ' type' . $a['btype'];
    $btncls .= ' size' . $a['bsize'];
    $btncls .= ' rounded' . $a['brounded'];
    $btncls .= ' btnborder' . $a['bborder'];
    $btncls .= ' fw' . $a['bfwcls'];

    // Text container style.
    $style .= ' style="';
    $style .= $a['mt'] ? 'margin-top:' . $a['mt'] . 'px;' : '';
    $style .= $a['mb'] ? 'margin-bottom:' . $a['mb'] . 'px;' : '';
    $style .= 'max-width:' . $a['width'] . 'px;margin-left:auto;margin-right:auto;';
    $style .= '"';

    $btnstyle .= ' style="';
    $btnstyle .= $a['btncolor'] ? '--mb2-pb-btn-color:' . $a['btncolor'] . ';' : '';
    $btnstyle .= $a['btnbgcolor'] ? '--mb2-pb-btn-bgcolor:' . $a['btnbgcolor'] . ';' : '';
    $btnstyle .= $a['btnbghcolor'] ? '--mb2-pb-btn-bghcolor:' . $a['btnbghcolor'] . ';' : '';
    $btnstyle .= $a['btnhcolor'] ? '--mb2-pb-btn-hcolor:' . $a['btnhcolor'] . ';' : '';
    $btnstyle .= $a['btnborcolor'] ? '--mb2-pb-btn-borcolor:' . $a['btnborcolor'] . ';' : '';
    $btnstyle .= $a['btnborhcolor'] ? '--mb2-pb-btn-borhcolor:' . $a['btnborhcolor'] . ';' : '';
    $btnstyle .= '"';

    $innerstyle .= ' style="';
    $innerstyle .= $a['pv'] ? 'padding-top:' . $a['pv'] . 'px;' : '';
    $innerstyle .= $a['pv'] ? 'padding-bottom:' . $a['pv'] . 'px;' : '';
    $innerstyle .= $a['ph'] ? 'padding-left:' . $a['ph'] . 'px;' : '';
    $innerstyle .= $a['ph'] ? 'padding-right:' . $a['ph'] . 'px;' : '';
    $innerstyle .= $a['bgcolor'] ? '--mb2-pb-textbg:' . $a['bgcolor'] . ';' : '';
    $innerstyle .= $a['bgcolor2'] ? '--mb2-pb-textbg2:' . $a['bgcolor2'] . ';' : '';
    $innerstyle .= '--mb2-pb-graddeg:' . $a['graddeg'] . 'deg;';
    $innerstyle .= '"';

    $typostyle .= ' style="';
    $typostyle .= $a['color'] ? 'color:' . $a['color'] . ';' : '';
    $typostyle .= $a['lspacing'] != 0 ? 'letter-spacing:' . $a['lspacing'] . 'px;' : '';
    $typostyle .= $a['wspacing'] != 0 ? 'word-spacing:' . $a['wspacing'] . 'px;' : '';
    $typostyle .= $a['sizerem'] ? 'font-size:' . $a['sizerem'] . 'rem;' : '';
    $typostyle .= '"';

    $typosttyle .= ' style="';
    $typosttyle .= $a['tcolor'] ? 'color:' . $a['tcolor'] . ';' : '';
    $typosttyle .= 'letter-spacing:' . $a['tlspacing'] . 'px;';
    $typosttyle .= 'word-spacing:' . $a['twspacing'] . 'px;';
    $typosttyle .= 'font-size:' . $a['tsizerem'] . 'rem;';
    $typosttyle .= 'margin-bottom:' . $a['tmb'] . 'px;';
    $typosttyle .= '"';

    $output .= '<div class="theme-text mb2-pb-element mb2pb-text' . $templatecls . '"' . $style .
    theme_mb2nl_page_builder_el_datatts($atts, $atts2) . '>';
    $output .= '<div class="element-helper"></div>';
    $output .= theme_mb2nl_page_builder_el_actions('element', 'text');
    $output .= '<div class="theme-text-inner' . $cls . '"' . $innerstyle . '>';
    $output .= '<h4 class="theme-text-title' . $titlecls . '"'. $typosttyle .'>' . $a['title'] . '</h4>';
    $output .= '<div class="theme-text-text' . $fontcls . '"' . $typostyle . '>';
    $output .= urldecode($content);
    $output .= '</div>';
    $output .= '<div class="theme-text-button" style="padding-top:' . $a['bmt'] . 'px;">';
    $output .= '<a href="#" class="arrowlink"' . $btnstyle . '>' . $a['btext'] . '</a>';
    $output .= '<a href="#" class="mb2-pb-btn' . $btncls . '"' . $btnstyle . '>' . $a['btext'] . '</a>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}
