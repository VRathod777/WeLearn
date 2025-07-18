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
class LocalMb2builderTextarea {

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

        if (!isset($attr['showon'])) {
            $attr['showon'] = '';
        }

        if (!isset($attr['showon2'])) {
            $attr['showon2'] = '';
        }

        $showon = local_mb2builder_showon_field($attr['showon']);
        $showon .= local_mb2builder_showon_field2($attr['showon2']);
        $actions = local_mb2builder_field_actions($attr);

        $output  = '<div class="form-group  mb2-pb-form-group">';
        $output .= '<div class="d-flex flex-row justify-content-between align-items-center">';
        $output .= '<label>' . $attr['title'] . '</label>';
        $output .= '<button class="mb2-pb-toggle-window-size themereset p-0" type="button">' .
        get_string('togglewindowsize', 'local_mb2builder') . '</button>';
        $output .= '</div>';
        $output .= '<textarea class="form-control mb2-pb-input mb2-pb-input-' . $key . '"' . $showon . $actions .
        ' data-attrname="' . $key . '">' .
        $attr['default'] . '</textarea>';

        if ($attr['desc']) {
            $output .= '<span class="mb2-pb-from-desc">' . $attr['desc'] . '</span>';
        }

        $output .= '</div>';

        return $output;

    }

}
