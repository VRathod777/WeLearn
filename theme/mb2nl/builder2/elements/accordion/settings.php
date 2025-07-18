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

$mb2settings = [
    'id' => 'accordion',
    'subid' => 'accordion_item',
    'title' => get_string('accordion', 'local_mb2builder'),
    'icon' => 'fa fa-bars',
    'tabs' => [
        'general' => get_string('generaltab', 'local_mb2builder'),
        'heading' => get_string('heading', 'local_mb2builder'),
        'content' => get_string('content', 'local_mb2builder'),
        'icon' => get_string('icon', 'local_mb2builder'),
        'style' => get_string('styletab', 'local_mb2builder'),
    ],
    'attr' => [
        'parent' => [
            'type' => 'yesno',
            'section' => 'general',
            'title' => get_string('accordionparent', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 1,
            'action' => 'callback',
            'callback' => 'accordion-parent',
        ],
        'accordion_active' => [
            'type' => 'yesno',
            'section' => 'general',
            'title' => get_string('active1item', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 1,
            'action' => 'callback',
            'callback' => 'accordion-active',
        ],
        'size' => [
            'type' => 'buttons',
            'section' => 'general',
            'title' => get_string('sizelabel', 'local_mb2builder', ''),
            'options' => [
                's' => get_string('small', 'local_mb2builder'),
                'm' => get_string('medium', 'local_mb2builder'),
                'l' => get_string('large', 'local_mb2builder'),
            ],
            'default' => 's',
            'action' => 'class',
            'class_remove' => 'sizel sizem sizes',
            'class_prefix' => 'size',
        ],
        'builder' => [
            'type' => 'hidden',
            'section' => 'general',
            'title' => '',
            'default' => 1,
        ],
        'tfs' => [
            'type' => 'range',
            'section' => 'heading',
            'title' => get_string('titlefs', 'local_mb2builder'),
            'min' => 1,
            'max' => 10,
            'step' => 0.01,
            'default' => 1,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.card-header button',
            'style_properity' => 'font-size',
            'style_suffix' => 'rem',
            'numclass' => 1,
            'sizepref' => 'pbtsize',
        ],
        'tfw' => [
            'type' => 'buttons',
            'section' => 'heading',
            'title' => get_string('fweight', 'local_mb2builder'),
            'options' => [
                'global' => get_string('global', 'local_mb2builder'),
                'light' => get_string('fwlight', 'local_mb2builder'),
                'normal' => get_string('normal', 'local_mb2builder'),
                'medium' => get_string('wmedium', 'local_mb2builder'),
                'semibold' => get_string('semibold', 'local_mb2builder'),
                'bold' => get_string('fwbold', 'local_mb2builder'),
            ],
            'default' => 'global',
            'action' => 'class',
            'selector' => '.acc-text',
            'class_remove' => 'fwglobal fwlight fwnormal fwmedium fwsemibold fwbold',
            'class_prefix' => 'fw',
        ],

        'group_acc_start_1' => [
        'type' => 'group_start', 'section' => 'heading', 'title' => get_string('normal', 'local_mb2builder')], // Group start.
        'tcolor' => [
            'type' => 'color',
            'section' => 'heading',
            'title' => get_string('color', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-header button',
            'cssvariable' => '--mb2-pb-acc-tcolor',
        ],

        'hbgcolor' => [
            'type' => 'color',
            'section' => 'heading',
            'title' => get_string('bgcolor', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-header button',
            'cssvariable' => '--mb2-pb-acc-hbgcolor',
        ],
        'group_acc_end_1' => ['type' => 'group_end', 'section' => 'heading'], // Group end.

        'group_acc_start_2' => [
        'type' => 'group_start', 'section' => 'heading', 'title' => get_string('hover_active', 'local_mb2builder')], // Group start.
        'thcolor' => [
            'type' => 'color',
            'section' => 'heading',
            'title' => get_string('color', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-header button',
            'cssvariable' => '--mb2-pb-acc-thcolor',
        ],

        'hbghcolor' => [
            'type' => 'color',
            'section' => 'heading',
            'title' => get_string('bgcolor', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-header button',
            'cssvariable' => '--mb2-pb-acc-hbghcolor',
        ],
        'group_acc_end_2' => ['type' => 'group_end', 'section' => 'heading'], // Group end.


        'padding' => [
            'type' => 'yesno',
            'section' => 'content',
            'title' => get_string('padding', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'class_prefix' => 'padding',
            'class_remove' => 'padding0 padding1',
        ],

        'scheme' => [
            'type' => 'buttons',
            'section' => 'content',
            'title' => get_string('scheme', 'local_mb2builder'),
            'options' => [
                'light' => get_string('light', 'local_mb2builder'),
                'dark' => get_string('dark', 'local_mb2builder'),
            ],
            'default' => 'dark',
            'action' => 'class',
            'selector' => '.card-body',
            'class_remove' => 'light dark',
        ],

        'cbgcolor' => [
            'type' => 'color',
            'section' => 'content',
            'title' => get_string('bgcolor', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-body',
            'cssvariable' => '--mb2-pb-acc-cbgcolor',
        ],

        'isicon' => [
            'type' => 'yesno',
            'section' => 'icon',
            'title' => get_string('icon', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'class_prefix' => 'isicon',
            'class_remove' => 'isicon0 isicon1',
        ],
        'icon' => [
            'type' => 'icon',
            'section' => 'icon',
            'showon' => 'isicon:1',
            'title' => get_string('icon', 'local_mb2builder'),
            'action' => 'icon',
            'default' => 'fa fa-trophy',
            'selector' => '.card-header i',
            'globalparent' => 1,
        ],
        'iconcolor' => [
            'type' => 'color',
            'section' => 'icon',
            'showon' => 'isicon:1',
            'title' => get_string('color', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-header button i',
            'cssvariable' => '--mb2-pb-acc-iconcolor',
        ],
        'iconhcolor' => [
            'type' => 'color',
            'section' => 'icon',
            'showon' => 'isicon:1',
            'title' => get_string('hcolor', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.card-header button i',
            'cssvariable' => '--mb2-pb-acc-iconhcolor',
        ],
        'type' => [
            'type' => 'list',
            'section' => 'style',
            'title' => get_string('prestyle', 'local_mb2builder'),
            'options' => [
                'default' => get_string('default', 'local_mb2builder'),
                'minimal' => get_string('minimal', 'local_mb2builder'),
            ],
            'default' => 'default',
            'action' => 'class',
            'class_remove' => 'style-default style-minimal',
            'class_prefix' => 'style-',
        ],
        'rounded' => [
            'type' => 'yesno',
            'section' => 'style',
            'showon' => 'type:default',
            'title' => get_string('rounded', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'class_prefix' => 'rounded',
            'class_remove' => 'rounded0 rounded1',
        ],
        'mt' => [
            'type' => 'range',
            'section' => 'style',
            'title' => get_string('mt', 'local_mb2builder'),
            'min' => 0,
            'max' => 300,
            'default' => 0,
            'action' => 'style',
            'changemode' => 'input',
            'style_properity' => 'margin-top',
        ],
        'mb' => [
            'type' => 'range',
            'section' => 'style',
            'title' => get_string('mb', 'local_mb2builder'),
            'min' => 0,
            'max' => 300,
            'default' => 30,
            'action' => 'style',
            'changemode' => 'input',
            'style_properity' => 'margin-bottom',
        ],
        'custom_class' => [
            'type' => 'text',
            'section' => 'style',
            'title' => get_string('customclasslabel', 'local_mb2builder'),
            'desc' => get_string('customclassdesc', 'local_mb2builder'),
        ],
    ],
    'subelement' => [
        'tabs' => [
            'general' => get_string('generaltab', 'local_mb2builder'),
        ],
        'attr' => [
            'title' => [
                'type' => 'text',
                'section' => 'general',
                'title' => get_string('title', 'local_mb2builder'),
                'default' => 'Accordion title here',
                'action' => 'text',
                'selector' => '.acc-text',
            ],
            'icon' => [
                'type' => 'icon',
                'section' => 'general',
                'title' => get_string('icon', 'local_mb2builder'),
                'action' => 'icon',
                'default' => '',
                'selector' => '.card-header i',
                'globalchild' => 1,
            ],
            'text' => [
                'type' => 'editor',
                'section' => 'general',
                'title' => get_string('content', 'local_mb2builder'),
                'default' => 'Accordion content here.',
                'selector' => '.card-body',
            ],
        ],
    ],
];

define('LOCAL_MB2BUILDER_SETTINGS_ACCORDION', base64_encode(json_encode($mb2settings)));
