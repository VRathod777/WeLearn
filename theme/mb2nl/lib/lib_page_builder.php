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
 * Method to get page builder shortcode content
 *
 */
function theme_mb2nl_builder_content($pagedata = [], $footer = false, $date = '') {

    $output = '';

    if (!is_array($pagedata) || empty($pagedata) || (isset($pagedata[0]->attr) && empty($pagedata[0]->attr))) {
        return;
    }

    foreach ($pagedata as $page) {
        foreach ($page->attr as $section) {
            $output .= '[section' . theme_mb2nl_page_builder_el_settings($section->settings, ['admin_label']) . ']';

            foreach ($section->attr as $row) {
                $output .= '[row' . theme_mb2nl_page_builder_el_settings($row->settings, ['admin_label']) . ']';

                foreach ($row->attr as $col) {
                    $isfooter = $footer ? ' isfooter="1"' : ' isfooter="0"';
                    $output .= '[pbcolumn' . theme_mb2nl_page_builder_el_settings($col->settings, ['admin_label']) .
                    $isfooter . ']';

                    foreach ($col->attr as $element) {

                        $output .= '[' . $element->settings->id . theme_mb2nl_page_builder_el_settings($element->settings,
                        ['admin_label', 'id', 'subelement', 'text', 'content']) . ']';

                        $output .= theme_mb2nl_page_builder_el_content($element);

                        $output .= '[/' . $element->settings->id . ']';
                    }

                    $output .= '[/pbcolumn]';
                }
                $output .= '[/row]';
            }
            $output .= '[/section]';
        }
    }

    $output .= $date ? '<span class="mb2-pb-pagedate sr-only">' . get_string('lastmodified') . ': ' . $date . '</span>' : '';

    if (!function_exists('mb2_do_shortcode')) {
        $msg = '<div style="margin:10rem auto;max-width:650px;text-align:center;padding:1.6rem;background-color:green;color:#fff">';
        $msg .= '<b>Please, install and activate Mb2 Shortcodes FILTER plugin.</b></div>';
        return $msg;
    }

    return mb2_do_shortcode($output);

}



/**
 *
 * Method to get page builder elements settings attributes
 *
 */
function theme_mb2nl_page_builder_el_settings($item, $exclude = []) {

    $output = '';

    foreach ($item as $k => $v) {
        if (in_array($k, $exclude) || $v === '') {
            continue;
        }

        if (strlen($v) > 1 && in_array($k, theme_mb2nl_page_builder_id2decode())) {
            // Params comes encoded from page builder.
            // So we need to decode it.
            $v = urldecode($v);

            // In data attribute we need html entities.
            // But we need to prevent to etities twice.
            // So, first we need to remove entities with 'html_entity_decode'.
            // And then convert to entities width 'htmlentities'.
            $v = html_entity_decode($v);
            $v = htmlentities($v);
        }

        // We want to display only none-empty values.
        // We have to use !=='' because of "0" value which is not empty.
        $output .= ' ' . $k . '="' . $v . '"';
    }

    return $output;

}



/**
 *
 * Method to get page builder elements content
 *
 */
function theme_mb2nl_page_builder_el_content($element) {

    $output = '';

    foreach ($element->settings as $id => $value) {
        $output .= ($id === 'text' || $id === 'content') ? $value : '';
    }

    if (isset($element->attr)) {
        foreach ($element->attr as $subelement) {
            $output .= '[' . $subelement->settings->id . theme_mb2nl_page_builder_el_settings($subelement->settings,
            ['admin_label', 'id', 'text', 'content']) . ']';

            foreach ($subelement->settings as $id => $value) {
                $output .= ($id === 'text' || $id === 'content') ? $value : '';
            }

            $output .= '[/' . $subelement->settings->id . ']';
        }
    }

    return $output;

}




/**
 *
 * Method to display builder page content
 *
 */
function theme_mb2nl_builder_page() {

    global $CFG;

    $mpageid = theme_mb2nl_builder_mpageid();

    if (!$mpageid) {
        return;
    }

    // Check if Moodle page has builder page.
    if (!theme_mb2nl_builder_has_page()) {
        return;
    }

    // Define page cache.
    $cache = cache::make('local_mb2builder', 'pagedata');
    $cacheid = $mpageid == -1 ? 'fp' : $mpageid;

    // Check if cache of the page exist. If not, We have to set it.
    if (!$cache->get($cacheid)) {
        if (!class_exists('Mb2builderPagesApi')) {
            require($CFG->dirroot . '/local/mb2builder/classes/pages_api.php');
        }
        $page = Mb2builderPagesApi::get_record(theme_mb2nl_builder_pageid());
        $page->democontent = ''; // We don't need demo contecnt in the cache file.
        $cache->set($cacheid, $page);
    }

    if ($cache->get($cacheid)->timemodified) {
        $pagedate = userdate($cache->get($cacheid)->timemodified);
    } else {
        $pagedate = userdate($cache->get($cacheid)->timecreated);
    }

    if ($cache->get($cacheid)->content) {
        return theme_mb2nl_builder_content(json_decode($cache->get($cacheid)->content), false, $pagedate);
    }

}
