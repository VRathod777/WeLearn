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




/**
 *
 * Method to check if the toggle sidebar is enabled
 *
 */
function theme_mb2nl_is_tgsdb() {

    global $PAGE, $CFG;

    if ($PAGE->pagelayout === 'mb2builder' || preg_match('@mb2builder_form@', $PAGE->pagelayout) || theme_mb2nl_is_login(true)) {
        return false;
    }

    $isblock = (theme_mb2nl_isblock('side-pre') || (!theme_mb2nl_is_coursetabs() && theme_mb2nl_rating_block()) ||
    theme_mb2nl_isfakeblock());

    if ((theme_mb2nl_tgsdb_setting() || theme_mb2nl_full_screen_module()) && (theme_mb2nl_is_site_menu() ||
    theme_mb2nl_is_chome() || theme_mb2nl_is_toc() || $isblock)) {
        return true;
    }

    return false;

}






/**
 *
 * Method to check if is toggle sidebar settin enabled
 *
 */
function theme_mb2nl_tgsdb_setting() {

    global $PAGE;

    $buildertgsdb = theme_mb2nl_builder_tgsdb();
    $tgsdbfield = theme_mb2nl_mb2fields_filed('mb2tgsdb');
    $tgsdb = theme_mb2nl_theme_setting($PAGE, 'tgsdb');

    if ($buildertgsdb != 0) {
        $tgsdb = $buildertgsdb == -1 ? 0 : 1;
    } else if (!is_null($tgsdbfield) && $tgsdbfield !== '') {
        $tgsdb = $tgsdbfield;
    }

    return $tgsdb;

}



/**
 *
 * Method to check if is toggle sidebar settin enabled
 *
 */
function theme_mb2nl_tgsdb_cssvar() {
    global $PAGE;

    $var = '';

    $var .= '--tgsdb_tgsdbbg:' . theme_mb2nl_theme_setting($PAGE, 'tgsdbbg') . ';';

    return $var;

}









/**
 *
 * Method to get toggle sidebar
 *
 */
function theme_mb2nl_tgsdb() {
    global $CFG, $OUTPUT, $PAGE;

    $output = '';

    if (!theme_mb2nl_is_tgsdb()) {
        return;
    }

    // Css class.
    $cls = theme_mb2nl_theme_setting($PAGE, 'tgsdbdark') ? ' dark' : ' light';

    // Table of contents.
    $clayout = theme_mb2nl_course_layout();

    // Blocks.
    $blocks = theme_mb2nl_isblock('side-pre') || (!theme_mb2nl_is_coursetabs() && theme_mb2nl_rating_block());

    // Course home section.
    $chome = theme_mb2nl_is_chome();

    // Active class.
    $active = theme_mb2nl_tgsdb_active();
    $activetoc = $active === 'toc' ? ' active' : '';
    $activeblocks = $active === 'blocks' ? ' active' : '';
    $activechome = $active === 'chome' ? ' active' : '';

 $output .= theme_mb2nl_tgsdb_button();

// $output .= '<div id="toggle-sidebar" class="toggle-sidebar pagelayout-a' . $cls . '" data-tgsdb_active="' . $active . '" style="' . theme_mb2nl_tgsdb_cssvar() . '">';
$output .= '<div id="toggle-sidebar" class=" pagelayout-a' . $cls . '" data-tgsdb_active="' . $active . '" style="' . theme_mb2nl_tgsdb_cssvar() . '">';

// $output .= '<div class="header-gap"></div>';
// $output .= '<div class="sidebar-inner ' . theme_mb2nl_bsfcls(1, 'row') . '" style="background-image: linear-gradient(to bottom, var(--tgsdb_grad2) 0%, var(--tgsdb_grad1) 100%);">';
$output .= '<div class="sidebar-inner ' . theme_mb2nl_bsfcls(1, 'row') . '" style="width: 200px;">';

$output .= '<div class="sidebar-content position-relative w-100">';

    $output .= '<div class="sidebar-tabs">';
    $output .= theme_mb2nl_tgsdb_nav();
    $output .= '<div class="sidebar-tabs-content position-absolute h-100">';
    $output .= theme_mb2nl_tgsdbtab_button();

    if ($chome) {
        $output .= '<div id="tgsdb_chome" class="tgsdb-section tgsdb-chome' . $activechome . '">';
        $output .= theme_mb2nl_tgsdb_chome();
        $output .= '</div>'; // ...tgsdb-chome
    }

    if (theme_mb2nl_is_toc()) {
        $output .= '<div id="tgsdb_toc" class="tgsdb-section tgsdb-toc' . $activetoc . '">';
        $output .= theme_mb2nl_toc_tools();
        $output .= theme_mb2nl_module_sections();
        $output .= '</div>'; // ...tgsdb-toc
    }

    if ($blocks) {
        $output .= '<div id="tgsdb_blocks" class="tgsdb-section tgsdb-blocks' . $activeblocks . '">';
        $output .= !theme_mb2nl_is_coursetabs() ? theme_mb2nl_rating_block('toggle-block', true) : '';
        $output .= $OUTPUT->blocks('side-pre', 'toggle-block');
        $output .= '</div>'; // ...tgsdb-blocks
    }

    $output .= '</div>'; // ...sidebar-tabs-content
    $output .= '</div>'; // ...sidebar-tabs
    $output .= '<div class="sidebar-menu position-relative">';
    $output .= theme_mb2nl_site_menu(true, true);
    $output .= '</div>';
    // $output .= '<div class="sidebar-footer position-relative' . theme_mb2nl_bsfcls(1, 'column', '', 'center') . '">';
    $output .= theme_mb2nl_site_menu(true, true, true);
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>'; // ... sidebars
    $output .= '</div>'; // ...toggle-sidebar

    return $output;

}




/**
 *
 * Method to get toggle sidebar navigation
 *
 */
function theme_mb2nl_tgsdb_button() {
    global $PAGE, $CFG;

    $output = '';

    if ($CFG->version < 2023100900) { // Up to Moodle 4.3.
        user_preference_allow_ajax_update('mb2_tgsdb', PARAM_INT);
    }

    $tgsdbdefault = theme_mb2nl_theme_setting($PAGE, 'tgsdbdef') ? 1 : 0;
    $expanded = theme_mb2nl_user_preference('mb2_tgsdb', $tgsdbdefault) ? 'true' : 'false';
    $str = $expanded === 'true' ? get_string('tgsdbclose', 'theme_mb2nl') : get_string('tgsdbopen', 'theme_mb2nl');

    $output .= '<button type="button" class="tgsdb_btn p-2 position-fixed' . theme_mb2nl_bsfcls(2, '', 'end', 'end') .
    '" aria-controls="toggle-sidebar" aria-expanded="' . $expanded . '">';
    $output .= '<span class="sr-only">' . $str . '</span>';
    $output .= '<i class="bi bi-chevron-double-left"></i>';
    $output .= '</button>';

    return $output;
}




/**
 *
 * Method to get toggle sidebar navigation
 *
 */
function theme_mb2nl_tgsdbtab_button() {
    global $PAGE, $CFG;

    $output = '';

    if ($CFG->version < 2023100900) { // Up to Moodle 4.3.
        user_preference_allow_ajax_update('mb2_tgsdbb', PARAM_INT);
        user_preference_allow_ajax_update('mb2_tgsdbactv', PARAM_ALPHA);
    }

    $tgsdbdefault = theme_mb2nl_theme_setting($PAGE, 'tgsdbdef') ? 1 : 0;
    $expanded = theme_mb2nl_user_preference('mb2_tgsdbb', $tgsdbdefault) ? 'true' : 'false';
    $ariacontrols = $expanded === 'true' ? ' aria-controls="tgsdb_' . theme_mb2nl_user_preference('mb2_tgsdbactv', 'none') . '"' :
    '';

    $output .= '<button type="button" class="tgsdbb_toggle p-0' . theme_mb2nl_bsfcls(2, '', 'center', 'center') .
    '" aria-expanded="' . $expanded . '" title="' . get_string('tgsdbclosetab', 'theme_mb2nl') . '"' . $ariacontrols . '>';
    $output .= '<span class="sr-only">' . get_string('tgsdbclosetab', 'theme_mb2nl') . '</span>';
    $output .= '<i class="bi bi-chevron-double-left"></i>';
    $output .= '</button>';

    return $output;
}



/**
 *
 * Method to get toggle sidebar navigation
 *
 */
function theme_mb2nl_tgsdb_navitems() {
    global $PAGE;

    $items = [
        [
            'id' => 'chome',
            'label' => get_string('courseinfo'),
            'icon' => '<i class="ri-information-line"></i>',
            'show' => theme_mb2nl_is_chome(),
        ],
        [
            'id' => 'toc',
            'label' => get_string('sections'),
            'icon' => '<i class="ri-align-left"></i>',
            'show' => theme_mb2nl_is_toc(),
        ],
        [
            'id' => 'blocks',
            'label' => get_string('blocks'),
            'icon' => '<i class="ri-code-block"></i>',
            'show' => theme_mb2nl_isblock('side-pre') || (!theme_mb2nl_is_coursetabs() && theme_mb2nl_rating_block()) ||
            theme_mb2nl_isfakeblock(),
        ],
    ];

    return $items;

}



/**
 *
 * Method to get toggle sidebar navigation
 *
 */
function theme_mb2nl_tgsdb_nav() {

    global $PAGE;

    $output = '';
    $active = theme_mb2nl_tgsdb_active();
    $items = theme_mb2nl_tgsdb_navitems();

    $output .= '<div class="sidebar-tabs-list position-relative' . theme_mb2nl_bsfcls(1, 'column', 'center') . '">';

    foreach ($items as $item) {
        if (!$item['show']) {
            continue;
        }

        $sctivecls = $active === $item['id'] ? ' active' : '';
        $expanded = $active === $item['id'] ? 'true' : 'false';

        $output .= '<button type="button" class="themereset tgsdb-btn p-0 ml-auto mr-auto' .
        theme_mb2nl_bsfcls(2, 'column', 'center', 'center') . $sctivecls . '" data-id="' . $item['id'] . '" aria-controls="tgsdb_' .
        $item['id'] . '" aria-expanded="' . $expanded . '">';
        $output .= $item['icon'];
        $output .= '<span class="label px-1 text' . theme_mb2nl_tcls('xxsmall') . '">' . $item['label'] . '</span>';
        $output .= '</button>';
    }

    $output .= '</div>'; // ...sidebar-tabs-list

    return $output;
}




/**
 *
 * Method to get toggle sidebar active item
 *
 */
function theme_mb2nl_tgsdb_active() {
    global $CFG, $PAGE;

    if ($CFG->version < 2023100900) { // Up to Moodle 4.3.
        user_preference_allow_ajax_update('mb2_tgsdbactv', PARAM_ALPHA);
    }

    $pref = theme_mb2nl_user_preference('mb2_tgsdbactv', 'none');
    $blocks = theme_mb2nl_isblock('side-pre') || (!theme_mb2nl_is_coursetabs() && theme_mb2nl_rating_block());

    // Always activate blocks.
    if (theme_mb2nl_isfakeblock() || $PAGE->user_is_editing()) {
        return 'blocks';
    }

    if ($pref === 'toc' && theme_mb2nl_is_toc()) {
        return 'toc';
    }

    if ($pref === 'chome' && theme_mb2nl_is_chome()) {
        return 'chome';
    }

    if ($pref === 'blocks' && $blocks) {
        return 'blocks';
    }

    return;
}






/**
 *
 * Method to get rating block
 *
 */
function theme_mb2nl_rating_block($style = '') {
    global $CFG, $PAGE, $COURSE;

    if (!theme_mb2nl_is_review_plugin()) {
        return;
    }

    // In some course layouts the rating blocks is not displayed.
    if (in_array(theme_mb2nl_course_layout(), theme_mb2nl_noratingblock())) {
        return;
    }

    $style = $style ? $style : theme_mb2nl_theme_setting($PAGE, 'blockstyle2');

    if (!class_exists('Mb2reviewsHelper')) {
        require($CFG->dirroot . '/local/mb2reviews/classes/helper.php');
    }

    // In the latest version of the reviews plugin We don't need the second attribute.
    // The attribute is addedd to avoid error during theme installation if user has the old reviews plugin version.
    // Remove this attribute 'false' after a few months (27 Sep 2024).
    // Do the same in the 'rating' shortcode.
    return Mb2reviewsHelper::rating_block($style, false);

}





/**
 *
 * Method to get course sidebar home section
 *
 */
function theme_mb2nl_tgsdb_chome() {
    global $COURSE, $PAGE;

    $output = '';

    $output .= '<div class="course-sidebar-home"></div>'; // Content loaded via js.

    $PAGE->requires->js_call_amd('theme_mb2nl/tgsdb', 'loadChome', [$COURSE->id]);

    return $output;

}