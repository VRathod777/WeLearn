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
 *
 */

defined('MOODLE_INTERNAL') || die();

theme_mb2nl_onetopic_fix();
$ratingblock = theme_mb2nl_rating_block();
$toc = theme_mb2nl_is_toc();
$ctabs = theme_mb2nl_is_coursetabs();
$ctab = optional_param('ctab', '', PARAM_ALPHANUMEXT);
$tgsdb = theme_mb2nl_is_tgsdb();
$stickysidebar = theme_mb2nl_is_cmainpage() && $tgsdb && $ctabs == 1;

if ($tgsdb) {
    $sidepre = theme_mb2nl_isblock('side-post') || $ctabs == 1;
    $sidepost = false;
} else {
    $sidepre = theme_mb2nl_isblock('side-pre') || (!$ctab && $toc) || (!$ctabs && $ratingblock) || $ctabs == 1;
    $sidepost = theme_mb2nl_isblock('side-post');
}

$sidebarpos = theme_mb2nl_sidebarpos();

$sidebar = ($sidepre || $sidepost);
$contentcol = 'col-lg-12';
$sideprecol = 'col-lg-3';
$sidepostcol = 'col-lg-3';

if ($sidepre && $sidepost) {
    $contentcol = 'col-lg-6';

    if ($sidebarpos === 'classic') {
        $contentcol .= ' order-2';
        $sideprecol .= ' order-1';
        $sidepostcol .= ' order-3';
    } else if ($sidebarpos === 'left') {
        $contentcol .= ' order-3';
        $sideprecol .= ' order-1';
        $sidepostcol .= ' order-2';
    }
} else if ($sidepre || $sidepost) {
    $contentcol = 'col-lg-9';

    if ($sidebarpos === 'classic') {
        $contentcol .= ' order-2';
        $sideprecol .= ' order-1';
        $sidepostcol .= ' order-3';
    } else if ($sidebarpos === 'left') {
        $contentcol .= ' order-3';
        $sideprecol .= ' order-1';
        $sidepostcol .= ' order-2';
    }
}

if ($stickysidebar) {
    $sideprecol .= ' course-sidebar';
}

if ($ctabs == 1) {
    $sideprecol .= ' ctabs-sidebar';
    $contentcol .= ' ctabs-content';
}

$html = '';

$html .= theme_mb2nl_notice();
$html .= $OUTPUT->theme_part('course_banner');
$html .= '<div id="main-content">';
$html .= '<div class="container-fluid">';
$html .= '<div id="theme-main-content" class="row">';
$html .= '<section class="content-col ' . $contentcol . '">';
$html .= '<div id="region-main">';
$html .= theme_mb2nl_show_hide_sidebars(['sidebar' => $sidebar], true);
$html .= '<div id="page-content">';

if ($ctabs == 2) {
    $html .= $OUTPUT->theme_part('course_tabs', ['pos' => 'top']);
}

$html .= $OUTPUT->course_content_header();
$html .= $OUTPUT->theme_part('dashboard');
$html .= theme_mb2nl_check_plugins();

if (theme_mb2nl_isblock('content-top')) {
    $html .= $OUTPUT->blocks('content-top', theme_mb2nl_block_cls('content-top', 'none'));
}

$html .= $OUTPUT->main_content();

if (theme_mb2nl_isblock('content-bottom')) {
    $html .= $OUTPUT->blocks('content-bottom', theme_mb2nl_block_cls('content-bottom', 'none'));
}

$html .= theme_mb2nl_custom_sectionnav();
$html .= $OUTPUT->course_content_footer();

if ($ctabs) {
    if ($ctab === 'courseinfo') {
        $html .= theme_mb2nl_course_info_html();
    } else if (preg_match('@mb2section@', $ctab)) {
        $html .= theme_mb2nl_course_csection_html($ctab);
    } else if ($ctab === 'reviews') {
        $html .= theme_mb2nl_course_reviews_html();
    }
}

$html .= '</div>';
$html .= '</div>';
$html .= '</section>';

if ($sidepre) {
    $html .= '<div class="sidebar-col ' . $sideprecol . '">';
    $html .= $stickysidebar ? '<div class="sidebar-inner">' : '';

    if (theme_mb2nl_is_cmainpage() && ($ctabs == 1 || $toc)) {
        $html .= theme_mb2nl_course_progressbar();
        $html .= theme_mb2nl_course_boxes('circle');
    }

    if ($ctabs == 1) {
        $html .= $OUTPUT->theme_part('course_tabs', ['pos' => 'sidebar']);
    }

    if (!$tgsdb && $toc && !$ctab) {
        $html .= theme_mb2nl_module_sections(true, false);
    }

    $html .= !$tgsdb && !$ctabs ? theme_mb2nl_rating_block() : '';

    if ($tgsdb && theme_mb2nl_isblock('side-post')) {
        $html .= $OUTPUT->blocks('side-post', theme_mb2nl_block_cls('side-post', 'default'));
    } else if (theme_mb2nl_isblock('side-pre')) {
        $html .= $OUTPUT->blocks('side-pre', theme_mb2nl_block_cls('side-pre', 'default'));
    }

    $html .= $stickysidebar ? '</div>' : '';
    $html .= '</div>';
}

if ($sidepost) {
    $html .= '<div class="sidebar-col ' . $sidepostcol . '">';
    $html .= $OUTPUT->blocks('side-post', theme_mb2nl_block_cls('side-post', 'default'));
    $html .= '</div>';
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

$html .= $OUTPUT->standard_after_main_region_html();
$html .= $OUTPUT->theme_part('region_bottom');
$html .= $OUTPUT->theme_part('region_bottom_abcd');
$html .= $OUTPUT->theme_part('footer', ['sidebar' => $sidebar]);

echo $html;
