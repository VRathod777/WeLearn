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
 * @package    local_mb2megamenu
 * @copyright  2019 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require(__DIR__ . '/lib.php');
require(__DIR__ . '/classes/settings.php');

/**
 * External theme API
 */
class local_mb2megamenu_external extends external_api {


    /**
     *
     * Method to get a list of all services.
     *
     */
    public static function get_theme_icons() {

        $params = self::validate_parameters(self::get_theme_icons_parameters(), []);

        $results = [
            'icons' => Mb2megamenuSettings::font_icon_tabs(),
        ];

        return $results;

    }




    /**
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_theme_icons_parameters() {
        return new external_function_parameters(
            []
        );
    }



    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_theme_icons_returns() {
        return new external_single_structure(
            [
                'icons' => new external_value(PARAM_RAW, 'Theme font icons list'),
                'warnings' => new external_warnings(),
            ]
        );
    }

}
