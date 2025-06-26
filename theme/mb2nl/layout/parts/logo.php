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

global $SITE, $PAGE;

$logos = ['logo-light', 'logo-dark'];

$clogolink = explode('|', theme_mb2nl_theme_setting($PAGE, 'clogolink'));
$url = $clogolink[0] ? $clogolink[0] : new moodle_url('/');
$linktarget = isset($clogolink[1]) ? ' target="_blank"' : '';
$label = theme_mb2nl_theme_setting($PAGE, 'clogotext') ? theme_mb2nl_theme_setting($PAGE, 'clogotext') : $SITE->fullname;
$html = '';

$html .= '<div class="logo-wrap">';
$html .= '<div class="main-logo">';
$html .= '<a href="' . $url . '" aria-label="' . $label . '"' . $linktarget . '>';

foreach ($logos as $l) {
    $pblogo = theme_mb2nl_builder_logo($l);
    $src = $l === 'logo-light' ? theme_mb2nl_logo_url() : theme_mb2nl_logo_url(false, $l);
    $src = $pblogo ? $pblogo : $src;

    $svgcls = theme_mb2nl_is_svg($src) ? ' is_svg' : ' no_svg';
    $html .= '<img class="' . $l . $svgcls . '" src="' . $src . '" alt="' . $label . '">';
}

$html .= '</a>';
$html .= '</div>';
$html .= '</div>';

echo $html;
