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

$THEME->doctype = 'html5';
$THEME->name = 'mb2nl';

$THEME->extrascsscallback = 'theme_mb2nl_get_pre_scss_raw';
$THEME->prescsscallback = 'theme_mb2nl_get_pre_scss';
$THEME->scss = function($theme){
    return theme_mb2nl_get_scss_content($theme);
};
$THEME->sheets = ['style'];
$THEME->parents = ['boost'];
$THEME->supportscssoptimisation = false;
$THEME->yuicssmodules = [];
$THEME->rendererfactory = 'theme_overridden_renderer_factory';
$THEME->blockrtlmanipulations = [];
$THEME->enable_dock = false;
$THEME->editor_sheets = [];
$THEME->usefallback = true;
$THEME->haseditswitch = true;
$THEME->requiredblocks = isset($THEME->settings->navblocks) && $THEME->settings->navblocks ? ['navigation', 'settings'] : '';

// Define region arrays.
$fpregions = ['side-pre', 'side-post', 'content-top', 'content-bottom', 'adminblock', 'bottom', 'bottom-a', 'bottom-b', 'bottom-c',
'bottom-d'];
$incourseregions = ['side-pre', 'content-top', 'content-bottom', 'adminblock', 'bottom', 'bottom-a', 'bottom-b', 'bottom-c',
'bottom-d'];
$defregions = ['side-pre', 'side-post', 'content-top', 'content-bottom', 'adminblock', 'bottom', 'bottom-a', 'bottom-b', 'bottom-c',
'bottom-d'];
$def2colsregions = ['side-pre', 'content-top', 'content-bottom', 'adminblock', 'bottom', 'bottom-a', 'bottom-b', 'bottom-c',
'bottom-d'];

// Moodle documentation
// https://docs.moodle.org/dev/Page_API.
$THEME->layouts = [
    'base' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => [],
    ],
    'standard' => [
        'file' => 'columns2.php',
        'regions' => $def2colsregions,
        'defaultregion' => 'side-pre',
    ],
    'course' => [
        'file' => isset($THEME->settings->c2cols) && $THEME->settings->c2cols ? 'columns2.php' : 'columns3.php',
        'regions' => isset($THEME->settings->c2cols) && $THEME->settings->c2cols ? $def2colsregions : $defregions,
        'defaultregion' => 'side-pre',
    ],
    'coursecategory' => [
        'file' => 'columns2.php',
        'regions' => $def2colsregions,
        'defaultregion' => 'side-pre',
    ],
    'incourse' => [
        'file' => 'incourse.php',
        'regions' => $incourseregions,
        'defaultregion' => 'side-pre',
    ],
    'frontpage' => [
        'file' => 'frontpage.php',
        'regions' => $fpregions,
        'defaultregion' => 'side-pre',
        'options' => ['nonavbar' => true],
    ],
    'mydashboard' => [
        'file' => isset($THEME->settings->d2cols) && $THEME->settings->d2cols ? 'columns2.php' : 'columns3.php',
        'regions' => isset($THEME->settings->d2cols) && $THEME->settings->d2cols ? $def2colsregions : $defregions,
        'defaultregion' => 'side-pre',
        'options' => [],
    ],
    'mycourses' => [
        'file' => isset($THEME->settings->d2cols) && $THEME->settings->d2cols ? 'columns2.php' : 'columns3.php',
        'regions' => isset($THEME->settings->d2cols) && $THEME->settings->d2cols ? $def2colsregions : $defregions,
        'defaultregion' => 'side-pre',
    ],
    'admin' => [
        'file' => 'columns2.php',
        'regions' => $def2colsregions,
        'defaultregion' => 'side-pre',
    ],
    'mypublic' => [
        'file' => 'columns2.php',
        'regions' => $def2colsregions,
        'defaultregion' => 'side-pre',
    ],
    'login' => [
        'file' => 'columns2.php',
        'regions' => $def2colsregions,
        'defaultregion' => 'side-pre',
        'options' => [],
    ],
    'popup' => [
        'file' => 'popup.php',
        'regions' => [],
        'options' => [],
    ],
    'frametop' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nocoursefooter' => true],
    ],
    'embedded' => [
        'theme' => 'boost',
        'file' => 'embedded.php',
        'regions' => [],
    ],
    'maintenance' => [
        'file' => 'maintenance.php',
        'regions' => [],
    ],
    'print' => [
        'file' => 'columns1.php',
        'regions' => [],
        'options' => ['nofooter' => true, 'nonavbar' => false],
    ],
    'redirect' => [
        'theme' => 'boost',
        'file' => 'embedded.php',
        'regions' => [],
    ],
    'report' => [
        'file' => 'columns2.php',
        'regions' => $def2colsregions,
        'defaultregion' => 'side-pre',
    ],
    'secure' => [
        'file' => 'secure.php',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre',
    ],
    'mb2builder' => [
        'theme' => 'mb2nl',
        'file' => 'mb2builder.php',
        'regions' => ['adminblock', 'side-pre'],
        'defaultregion' => 'side-pre',
    ],
    'mb2builder_form' => [
        'theme' => 'mb2nl',
        'file' => 'mb2builder_form.php',
        'regions' => ['adminblock', 'side-pre'],
        'defaultregion' => 'side-pre',
    ],
];
