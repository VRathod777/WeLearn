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

mb2_add_shortcode('accordionimg', 'mb2_shortcode_accordionimg');
mb2_add_shortcode('accordionimg_item', 'mb2_shortcode_accordionimg_item');

/**
 *
 * Method to define accordion image shortcode
 *
 * @return HTML
 */
function mb2_shortcode_accordionimg($atts, $content=null) {

    global $PAGE,
    $gl0accitem,
    $gl0acctfw,
    $gl0ailinkbtn,

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

        'mt' => 0,
        'mb' => 30,
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $style = '';
    $cls = '';

    $gl0accitem = 0;
    $gl0acctfw = $a['tfw'];
    $gl0ailinkbtn = $a['linkbtn'];

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

    $output .= '<div class="mb2-pb-accordionimg' . theme_mb2nl_bsfcls(1, '', 'between', 'center') . $cls . '"' . $style . '>';
    $output .= '<div class="accimg-nav">';
    $output .= mb2_do_shortcode($content);
    $output .= '</div>';
    $output .= '<div class="accimg-img-preview" aria-hidden="true">';
    $output .= '<div class="accimg-preview-inner">';

    // Get carousel content for sortable elements.
    $regex = '\\[(\\[?)(accordionimg_item)\\b([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)';
    $regex .= '(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
    preg_match_all("/$regex/is", $content, $match);
    $accitems = $match[0];
    $i = 0;

    foreach ($accitems as $item) {
        $itematts = shortcode_parse_atts($item);
        $i++;

        $output .= '<div class="accimg-preview accimg-preview-'. $i .'">';
        $output .= '<img src="' . theme_mb2nl_lazy_plc() . '" class="lazy" data-src="' . $itematts['image'] . '" alt="'.
        theme_mb2nl_format_str($itematts['title']) . '">';
        $output .= '</div>';
    }

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
function mb2_shortcode_accordionimg_item($atts, $content=null) {

    global $gl0accitem,
    $gl0acctfw,
    $gl0ailinkbtn,

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
        'title' => 'Accordion title here',
        'link' => '',
        'link_target' => 0,
        'btntext' => '',
        'image' => theme_mb2nl_dummy_image('800x600'),
        'isbadge' => 0,
        'badge' => 'New',
        'badgecolor' => '',
        'badgebgcolor' => '',
    ];

    $a = mb2_shortcode_atts($atts2, $atts);

    $output = '';
    $show = '';
    $tcls = ' fw' . $gl0acctfw;
    $expanded = 'false';
    $style = '';
    $btnstyle = '';

    // Get accordion ids.
    $accid = uniqid('accitem_');

    // Define accordion number.
    if (isset($gl0accitem)) {
        $gl0accitem++;
    } else {
        $gl0accitem = 1;
    }

    // Check if is active.
    if ($gl0accitem == 1) {
        $show = ' show';
        $expanded = 'true';
    }

    if ($a['badgecolor'] || $a['badgebgcolor']) {
        $style .= ' style="';
        $style .= $a['badgecolor'] ? '--mb2-pb-accimg-badgecolor:' . $a['badgecolor'] . ';' : '';
        $style .= $a['badgebgcolor'] ? '--mb2-pb-accimg-badgebgcolor:' . $a['badgebgcolor'] . ';' : '';
        $style .= '"';
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

    $a['title'] = theme_mb2nl_format_str($a['title']);

    if ($a['btntext']) {
        $btntext = theme_mb2nl_format_str($a['btntext']);
    } else if ($gl0baibtntext) {
        $btntext = theme_mb2nl_format_str($gl0baibtntext);
    } else {
        $btntext = get_string('readmorefp', 'local_mb2builder');
    }

    $target = $a['link_target'] ? ' target="_blank"' : '';

    $output .= '<div class="accimg-item' . $show . '" data-num="' . $gl0accitem . '"' . $style . '>';
    $output .= '<div class="accimg-header">';

    if ($a['isbadge']) {
        $output .= '<div class="accimg-badge mb-2 lhsmall"><span class="badge-text tsizexsmall upper1 fwbold d-block-inline">'.
        $a['badge'] . '</span></div>';
    }

    $output .= '<button type="button" class="themereset accimg-btn'.
    theme_mb2nl_bsfcls(1, '', 'between', 'center') . '" aria-expanded="' . $expanded . '" aria-controls="' . $accid . '">';
    $output .= '<span class="accimg-title mb-0 h4' . $tcls . theme_mb2nl_bsfcls(2) . '">' . $a['title']. '</span>';
    $output .= '<span class="accimg-plus' . theme_mb2nl_bsfcls(2, '', '', 'center') . '"></span>';
    $output .= '</button>'; // ...accimg-header
    $output .= '</div>'; // ...accimg-header
    $output .= '<div id="' . $accid . '" class="accimg-content position-relative">';
    $output .= '<div class="accimg-content-inner">';
    $output .= '<div class="accimg-text">';
    $output .= theme_mb2nl_check_for_tags($content, 'iframe') ? $content :
    mb2_do_shortcode(theme_mb2nl_format_txt($content, FORMAT_HTML));
    $output .= '</div>'; // ...accimg-text

    if ($gl0ailinkbtn && $a['link']) {
        $output .= '<div class="box-readmore">';
        $output .= $gl0ailinkbtn == 1 ? '<a href="' . $a['link'] . '" ' . $target . ' class="arrowlink"' . $btnstyle . '>' .
        $btntext . '</a>' : '';

        if ($gl0ailinkbtn == 2) {
            $output .= '<a class="mb2-pb-btn type' . $gl0baibtntype . ' size' . $gl0baibtnsize . ' rounded' . $gl0baibtnrounded .
            ' btnborder' . $gl0baibtnborder . ' fw' . $gl0baibtnfwcls . '" href="' . $a['link'] . '"' . $target . ' ' .
            $btnstyle . '>' . $btntext . '</a>';
        }

        $output .= '</div>';
    }

    $output .= '<div class="accimg-image">';
    $output .= '<img src="' . theme_mb2nl_lazy_plc() . '" class="accimg-image-src lazy" data-src="' . $a['image'] . '" alt="'.
    strip_tags($a['title']) . '">';
    $output .= '</div>'; // ...accimg-image
    $output .= '</div>'; // ...accimg-content-inner
    $output .= '</div>'; // ...accimg-content
    $output .= '</div>'; // ...accimg-item

    return $output;
}
