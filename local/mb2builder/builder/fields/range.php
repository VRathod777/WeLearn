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
 * @package    local_mb2builder
 * @copyright  2018 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

/**
 * Field class
 */
class LocalMb2builderRange {

    /**
     * Filed method
     */
    public static function local_mb2builder_get_input($key, $attr) {

        if (!isset($attr['default'])) {
             $attr['default'] = '';
        }

        if (!isset($attr['desc'])) {
             $attr['desc'] = '';
        }

        if (!isset($attr['min'])) {
            $attr['min'] = '';
        }

        if (!isset($attr['max'])) {
            $attr['max'] = '';
        }

        if (!isset($attr['step'])) {
            $attr['step'] = 1;
        }

        if (!isset($attr['showon'])) {
            $attr['showon'] = '';
        }

        if (!isset($attr['showon2'])) {
            $attr['showon2'] = '';
        }

        if (!isset($attr['style_suffix'])) {
            $attr['style_suffix'] = 'px';
        } else if (isset($attr['style_suffix']) && $attr['style_suffix'] === 'none') {
            // This is require for the "Select" element ("selectmh" option).
            $attr['style_suffix'] = '%';
        }

        if (isset($attr['label_suffix'])) {
            $labelsuff = $attr['label_suffix'];
        } else {
            $labelsuff = $attr['style_suffix'];
        }

        $showon = local_mb2builder_showon_field($attr['showon']);
        $showon .= local_mb2builder_showon_field2($attr['showon2']);
        $actions = local_mb2builder_field_actions($attr);

        $output  = '<div class="form-group mb2-pb-form-group">';
        $output .= '<label>' . $attr['title'] . '</label>';
        $output .= '<div class="d-flex flex-row justify-content-between align-items-center">';
        $output .= '<input type="range" class="form-control form-range mb2-pb-input mb2-pb-input-' . $key . '"' . $showon . $actions
        . ' data-attrname="' . $key . '" min="' . $attr['min'] . '" max="' . $attr['max'] . '" step="' . $attr['step'] . '" value="'
        . $attr['default'] . '" />';
        $output .= '<div class="numvalwrap d-inline-flex align-items-center">';
        $output .= '<div class="numval d-inline-flex justify-content-center align-items-center">' . $attr['default'] . '</div>';
        $output .= '<div class="numvalsuff d-inline-flex align-items-center ml-1">' . $labelsuff . '</div>';
        $output .= '</div>';
        $output .= '</div>';
        if ($attr['desc']) {
            $output .= '<span class="mb2-pb-from-desc">' . $attr['desc'] . '</span>';
        }

        $output .= '</div>';

        return $output;

    }

}
