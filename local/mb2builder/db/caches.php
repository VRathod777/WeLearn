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

defined('MOODLE_INTERNAL') || die();

$definitions = [
    'pages' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
    ],
    'pagedata' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
    ],
    'footerdata' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
    ],
    'partdata' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
    ],
    'builder' => [
        'mode' => cache_store::MODE_APPLICATION,
        'simplekeys' => true,
    ],
];
