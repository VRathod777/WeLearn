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

mb2_add_shortcode('mb2pb_accordionimg', 'mb2_shortcode_mb2pb_accordionimg');
mb2_add_shortcode('mb2pb_accordionimg_item', 'mb2_shortcode_mb2pb_accordionimg_item');

/**
 *
 * Method to define accordion image shortcode
 *
 * @return HTML
 */
function mb2_shortcode_mb2pb_accordionimg($atts, $content = null) {

    global $PAGE,
    $gl0accitem,
    $gl0acctfw,

    $gl0baibtncolor,
    $gl0baibtnbgcolor,
    $gl0baibtnbghcolor,
    $gl0baibtnhcolor,
    $gl0baibtnborcolor,
    $gl0baibtnborhcolor,

    $gl0baibtntext,
    $gl0baibtntype,
    $gl0baibtnsize,
    $gl0baibtnfwcls,
    $gl0baibtnborder,
    $gl0baibtnrounded;

    $atts2 = [
        'id' => 'accordionimg',
        'custom_class' => '',
        'navw' => 280,
        'space' => 2,
        'vspace' => .4,
        'tfs' => 1.4,
        'tfw' => 'global',
        'tcolor' => '',
        'tacolor' => '',
        'txtcolor' => '',
        'abgcolor' => '',
        'pluscolor' => '',
        'plusacolor' => '',
        'mt' => 0,
        'mb' => 30,
        'rounded' => 1,

        'linkbtn' => 0,
        'btntext' => '',
        'btntype' => 'primary',
        'btnsize' => 'normal',
        'btnfwcls' => 'global',
        'btnrounded' => 0,
        'btnborder' => 0,

        'btncolor' => '',
        'btnbgcolor' => '',
        'btnbghcolor' => '',
        'btnhcolor' => '',
        'btnborcolor' => '',
        'btnborhcolor' => '',

        'template' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $style = '';
    $cls = '';

    // Get accordion uniq id and send it as global.
    $gl0accitem = 0;
    $gl0acctfw = $a['tfw'];

    $gl0baibtncolor = $a['btncolor'];
    $gl0baibtnbgcolor = $a['btnbgcolor'];
    $gl0baibtnbghcolor = $a['btnbghcolor'];
    $gl0baibtnhcolor = $a['btnhcolor'];
    $gl0baibtnborcolor = $a['btnborcolor'];
    $gl0baibtnborhcolor = $a['btnborhcolor'];

    $gl0baibtntext = $a['btntext'];
    $gl0baibtntype = $a['btntype'];
    $gl0baibtnsize = $a['btnsize'];
    $gl0baibtnfwcls = $a['btnfwcls'];
    $gl0baibtnborder = $a['btnborder'];
    $gl0baibtnrounded = $a['btnrounded'];

    $cls .= ' rounded' . $a['rounded'];
    $cls .= $a['custom_class'] ? ' ' . $a['custom_class'] : '';
    $cls .= ' linkbtn' . $a['linkbtn'];
    $cls .= $a['template'] ? ' mb2-pb-template-accordionimg' : '';

    $style .= ' style="';
    $style .= 'margin-top:' . $a['mt'] . 'px;';
    $style .= 'margin-bottom:' . $a['mb'] . 'px;';
    $style .= '--mb2-pb-accimg-navw:' . $a['navw'] . 'px;';
    $style .= '--mb2-pb-accimg-space:' . $a['space'] . 'rem;';
    $style .= '--mb2-pb-accimg-vspace:' . $a['vspace'] . 'rem;';
    $style .= '--mb2-pb-accimg-tfs:' . $a['tfs'] . 'rem;';
    $style .= $a['tcolor'] ? '--mb2-pb-accimg-tcolor:' . $a['tcolor'] . ';' : '';
    $style .= $a['tacolor'] ? '--mb2-pb-accimg-tacolor:' . $a['tacolor'] . ';' : '';
    $style .= $a['txtcolor'] ? '--mb2-pb-accimg-txtcolor:' . $a['txtcolor'] . ';' : '';
    $style .= $a['abgcolor'] ? '--mb2-pb-accimg-abgcolor:' . $a['abgcolor'] . ';' : '';
    $style .= $a['pluscolor'] ? '--mb2-pb-accimg-pluscolor:' . $a['pluscolor'] . ';' : '';
    $style .= $a['plusacolor'] ? '--mb2-pb-accimg-plusacolor:' . $a['plusacolor'] . ';' : '';
    $style .= '"';

    if (! $content) {
        for ($i = 1; $i <= 3; $i++) {
            $content .= '[mb2pb_accordionimg_item][/mb2pb_accordionimg_item]';
        }
    }

    $output .= '<div class="mb2-pb-element mb2-pb-accordionimg' . $cls . '"' . $style.
    theme_mb2nl_page_builder_el_datatts($atts, $atts2) . '>';
    $output .= '<div class="element-helper"></div>';
    $output .= theme_mb2nl_page_builder_el_actions('element', 'accordionimg');
    $output .= '<div class="mb2-pb-element-inner mb2-pb-accordionimg_inner">';
    $output .= '<div class="mb2-pb-sortable-subelements accimg-nav">';
    $output .= mb2_do_shortcode($content);
    $output .= '</div>';
    $output .= '<div class="accimg-img-preview">';
    $output .= '<div class="accimg-preview-inner">';
    $output .= '<img src="' . theme_mb2nl_dummy_image('800x600') . '" alt="">';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}


/**
 *
 * Method to define accordion image item shortcode
 *
 * @return HTML
 */
function mb2_shortcode_mb2pb_accordionimg_item($atts, $content=null) {

    global $gl0accitem,
    $gl0acctfw,

    $gl0baibtncolor,
    $gl0baibtnbgcolor,
    $gl0baibtnbghcolor,
    $gl0baibtnhcolor,
    $gl0baibtnborcolor,
    $gl0baibtnborhcolor,

    $gl0baibtntext,
    $gl0baibtntype,
    $gl0baibtnsize,
    $gl0baibtnfwcls,
    $gl0baibtnborder,
    $gl0baibtnrounded;

    $atts2 = [
        'id' => 'accordionimg_item',
        'title' => 'Accordion title here',
        'btntext' => '',
        'link' => '',
        'link_target' => 0,
        'image' => theme_mb2nl_dummy_image('800x600'),
        'isbadge' => 0,
        'badge' => 'New',
        'badgecolor' => '',
        'badgebgcolor' => '',
        'template' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $btnstyle = '';
    $show = '';
    $tcls = ' fw' . $gl0acctfw;
    $style = '';
    $cls = '';

    $cls .= ' isbadge' . $a['isbadge'];

    if ($a['badgecolor'] || $a['badgebgcolor']) {
        $style .= ' style="';
        $style .= $a['badgecolor'] ? '--mb2-pb-accimg-badgecolor:' . $a['badgecolor'] . ';' : '';
        $style .= $a['badgebgcolor'] ? '--mb2-pb-accimg-badgebgcolor:' . $a['badgebgcolor'] . ';' : '';
        $style .= '"';
    }

    // Define accordion number.
    if (isset($gl0accitem)) {
        $gl0accitem++;
    } else {
        $gl0accitem = 1;
    }

    // Activate the first item.
    if ($gl0accitem == 1) {
        $show = ' show';
    }

    if ($gl0baibtncolor || $gl0baibtnbgcolor || $gl0baibtnbghcolor || $gl0baibtnhcolor || $gl0baibtnborcolor ||
    $gl0baibtnborhcolor) {
        $btnstyle .= ' style="';
        $btnstyle .= $gl0baibtncolor ? '--mb2-pb-btn-color:' . $gl0baibtncolor . ';' : '';
        $btnstyle .= $gl0baibtnbgcolor ? '--mb2-pb-btn-bgcolor:' . $gl0baibtnbgcolor . ';' : '';
        $btnstyle .= $gl0baibtnbghcolor ? '--mb2-pb-btn-bghcolor:' . $gl0baibtnbghcolor . ';' : '';
        $btnstyle .= $gl0baibtnhcolor ? '--mb2-pb-btn-hcolor:' . $gl0baibtnhcolor . ';' : '';
        $btnstyle .= $gl0baibtnborcolor ? '--mb2-pb-btn-borcolor:' . $gl0baibtnborcolor . ';' : '';
        $btnstyle .= $gl0baibtnborhcolor ? '--mb2-pb-btn-borhcolor:' . $gl0baibtnborhcolor . ';' : '';
        $btnstyle .= '"';
    }

    $content = $content ? $content : 'Accordion content here.';
    $atts2['text'] = $content;

    if ($a['btntext']) {
        $btntext = $a['btntext'];
    } else if ($gl0baibtntext) {
        $btntext = $gl0baibtntext;
    } else {
        $btntext = get_string('readmorefp', 'local_mb2builder');
    }

    $output .= '<div class="mb2-pb-subelement mb2-pb-accordionimg_item' . $cls . '"' . $style .
    theme_mb2nl_page_builder_el_datatts($atts, $atts2) . '>';
    $output .= theme_mb2nl_page_builder_el_actions('subelement');
    $output .= '<div class="subelement-helper"></div>';
    $output .= '<div class="mb2-pb-subelement-inner">';

    $output .= '<div class="accimg-item' . $show . '">';
    $output .= '<div class="accimg-badge mb-2 lhsmall"><span class="badge-text tsizexsmall upper1 fwbold d-block-inline">' .
    $a['badge'] . '</span></div>';
    $output .= '<div class="accimg-header">';
    $output .= '<button type="button" class="themereset accimg-btn' . theme_mb2nl_bsfcls(1, '', 'between', 'center') . '">';
    $output .= '<span class="accimg-title mb-0 h4' . $tcls . theme_mb2nl_bsfcls(2) . '">' . $a['title'] . '</span>';
    $output .= '<span class="accimg-plus' . theme_mb2nl_bsfcls(2, '', '', 'center') . '"></span>';
    $output .= '</button>';
    $output .= '</div>';

    $output .= '<div class="accimg-content position-relative">';
    $output .= '<div class="accimg-content-inner">';
    $output .= '<div class="accimg-text">' . urldecode($content) . '</div>';
    $output .= '<div class="box-readmore">';
    $output .= '<a href="#" class="arrowlink"' . $btnstyle . '>' . $btntext . '</a>';
    $output .= '<a class="mb2-pb-btn type' . $gl0baibtntype . ' size' . $gl0baibtnsize . ' rounded' . $gl0baibtnrounded .
    ' btnborder' . $gl0baibtnborder . ' fw' . $gl0baibtnfwcls . '" href="#" ' .
    $btnstyle . '>' . $btntext . '</a>';
    $output .= '</div>';
    $output .= '<div class="accimg-image">';
    $output .= '<img class="accimg-image-src" src="" alt="">';
    $output .= '</div>'; // ...accimg-image
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>'; // ...accimg-item

    $output .= '</div>';
    $output .= '</div>';

    return $output;
}
