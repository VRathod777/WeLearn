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
    'id' => 'text',
    'subid' => '',
    'title' => get_string('text', 'local_mb2builder'),
    'icon' => 'fa fa-pencil',
    'tabs' => [
        'general' => get_string('generaltab', 'local_mb2builder'),
        'typo' => get_string('typotab', 'local_mb2builder'),
        'bg' => get_string('background', 'local_mb2builder'),
        'button' => get_string('button', 'local_mb2builder'),
        'style' => get_string('styletab', 'local_mb2builder'),
    ],
    'attr' => [
        'showtitle' => [
            'type' => 'yesno',
            'section' => 'general',
            'title' => get_string('title', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.theme-text-inner',
            'class_remove' => 'title0 title1',
            'class_prefix' => 'title',
        ],
        'title' => [
            'type' => 'text',
            'section' => 'general',
            'showon' => 'showtitle:1',
            'title' => get_string('titletext', 'local_mb2builder'),
            'action' => 'text',
            'selector' => '.theme-text-title',
        ],
        'content' => [
            'type' => 'editor',
            'section' => 'general',
            'title' => get_string('text', 'local_mb2builder'),
            'selector' => '.theme-text-text',
        ],
        'group_text_start_2' => ['type' => 'group_start', 'section' => 'typo',
        'title' => get_string('text', 'local_mb2builder')], // Group start.
        'sizerem' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('fsizerem', 'local_mb2builder'),
            'min' => 1,
            'max' => 10,
            'step' => 0.01,
            'default' => 1,
            'action' => 'style',
            'changemode' => 'input',
            'style_properity' => 'font-size',
            'selector' => '.theme-text-text',
            'style_suffix' => 'rem',
            'numclass' => 1,
            'sizepref' => 'pbtsize',
        ],
        'fwcls' => [
            'type' => 'buttons',
            'section' => 'typo',
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
            'selector' => '.theme-text-text',
            'class_remove' => 'fwglobal fwlight fwnormal fwmedium fwsemibold fwbold',
            'class_prefix' => 'fw',
        ],
        'lhcls' => [
            'type' => 'buttons',
            'section' => 'typo',
            'title' => get_string('lh', 'local_mb2builder'),
            'options' => [
                'global' => get_string('global', 'local_mb2builder'),
                'small' => get_string('wsmall', 'local_mb2builder'),
                'medium' => get_string('wmedium', 'local_mb2builder'),
            ],
            'default' => 'global',
            'action' => 'class',
            'selector' => '.theme-text-text',
            'class_remove' => 'lhglobal lhsmall lhmedium',
            'class_prefix' => 'lh',
        ],
        'lspacing' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('lspacing', 'local_mb2builder'),
            'min' => -10,
            'max' => 30,
            'step' => 1,
            'default' => 0,
            'action' => 'style',
            'selector' => '.theme-text-text',
            'changemode' => 'input',
            'style_properity' => 'letter-spacing',
        ],
        'wspacing' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('wspacing', 'local_mb2builder'),
            'min' => -10,
            'max' => 30,
            'step' => 1,
            'default' => 0,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-text-text',
            'style_properity' => 'word-spacing',
        ],
        'color' => [
            'type' => 'color',
            'section' => 'typo',
            'title' => get_string('color', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.theme-text-text',
            'style_properity' => 'color',
        ],
        'upper' => [
            'type' => 'yesno',
            'section' => 'typo',
            'title' => get_string('uppercase', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.theme-text-text',
            'class_remove' => 'upper0 upper1',
            'class_prefix' => 'upper',
        ],
        'group_text_end_2' => ['type' => 'group_end', 'section' => 'typo'], // Group end.
        'group_text_start_1' => ['type' => 'group_start', 'section' => 'typo',
        'title' => get_string('title', 'local_mb2builder')], // Group start.
        'tsizerem' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('fsizerem', 'local_mb2builder'),
            'min' => 1,
            'max' => 10,
            'step' => 0.01,
            'default' => 1.4,
            'action' => 'style',
            'changemode' => 'input',
            'style_properity' => 'font-size',
            'selector' => '.theme-text-title',
            'style_suffix' => 'rem',
            'numclass' => 1,
            'sizepref' => 'pbtsize',
        ],
        'tfwcls' => [
            'type' => 'buttons',
            'section' => 'typo',
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
            'selector' => '.theme-text-title',
            'class_remove' => 'fwglobal fwlight fwnormal fwmedium fwsemibold fwbold',
            'class_prefix' => 'fw',
        ],
        'tlhcls' => [
            'type' => 'buttons',
            'section' => 'typo',
            'title' => get_string('lh', 'local_mb2builder'),
            'options' => [
                'global' => get_string('global', 'local_mb2builder'),
                'small' => get_string('wsmall', 'local_mb2builder'),
                'medium' => get_string('wmedium', 'local_mb2builder'),
            ],
            'default' => 'global',
            'action' => 'class',
            'selector' => '.theme-text-title',
            'class_remove' => 'lhglobal lhsmall lhmedium',
            'class_prefix' => 'lh',
        ],
        'tlspacing' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('lspacing', 'local_mb2builder'),
            'min' => -10,
            'max' => 30,
            'step' => 1,
            'default' => 0,
            'action' => 'style',
            'selector' => '.theme-text-title',
            'changemode' => 'input',
            'style_properity' => 'letter-spacing',
        ],
        'twspacing' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('wspacing', 'local_mb2builder'),
            'min' => -10,
            'max' => 30,
            'step' => 1,
            'default' => 0,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-text-title',
            'style_properity' => 'word-spacing',
        ],
        'tupper' => [
            'type' => 'yesno',
            'section' => 'typo',
            'title' => get_string('uppercase', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.theme-text-title',
            'class_remove' => 'upper0 upper1',
            'class_prefix' => 'upper',
        ],
        'tmb' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('mb', 'local_mb2builder'),
            'min' => 0,
            'max' => 300,
            'default' => 30,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-text-title',
            'style_properity' => 'margin-bottom',
        ],
        'tcolor' => [
            'type' => 'color',
            'section' => 'typo',
            'title' => get_string('color', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.theme-text-title',
            'style_properity' => 'color',
        ],
        'group_text_end_1' => ['type' => 'group_end', 'section' => 'typo'], // Group end.
        'align' => [
            'type' => 'buttons',
            'section' => 'style',
            'title' => get_string('alignlabel', 'local_mb2builder'),
            'options' => [
                'none' => get_string('none', 'local_mb2builder'),
                'left' => get_string('left', 'local_mb2builder'),
                'center' => get_string('center', 'local_mb2builder'),
                'right' => get_string('right', 'local_mb2builder'),
            ],
            'default' => 'none',
            'action' => 'class',
            'selector' => '.theme-text-inner',
            'class_remove' => 'align-none align-left align-right align-center',
            'class_prefix' => 'align-',
        ],
        'scheme' => [
            'type' => 'buttons',
            'section' => 'bg',
            'title' => get_string('scheme', 'local_mb2builder'),
            'options' => [
                'light' => get_string('light', 'local_mb2builder'),
                'dark' => get_string('dark', 'local_mb2builder'),
            ],
            'default' => 'light',
            'action' => 'class',
            'selector' => '.theme-text-inner',
            'class_remove' => 'light dark',
        ],
        'gradient' => [
            'type' => 'yesno',
            'section' => 'bg',
            'title' => get_string('gradient', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.theme-text-inner',
            'class_remove' => 'gradient0 gradient1',
            'class_prefix' => 'gradient',
        ],
        'graddeg' => [
            'type' => 'range',
            'section' => 'bg',
            'showon' => 'gradient:1',
            'title' => get_string('angle', 'local_mb2builder'),
            'min' => 0,
            'max' => 360,
            'default' => 45,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-text-inner',
            'style_properity' => '--mb2-pb-graddeg',
            'style_suffix' => 'deg',
        ],
        'bgcolor' => [
            'type' => 'color',
            'section' => 'bg',
            'title' => get_string('bgcolor', 'local_mb2builder'),
            'action' => 'color',
            'selector' => '.theme-text-inner',
            'style_properity' => '--mb2-pb-textbg',
        ],
        'bgcolor2' => [
            'type' => 'color',
            'showon' => 'gradient:1',
            'section' => 'bg',
            'title' => get_string('gradcolorn', 'local_mb2builder', ['n' => 2]),
            'action' => 'color',
            'selector' => '.theme-text-inner',
            'style_properity' => '--mb2-pb-textbg2',
        ],
        'width' => [
            'type' => 'range',
            'section' => 'style',
            'title' => get_string('widthlabel', 'local_mb2builder'),
            'min' => 0,
            'max' => 2000,
            'default' => 2000,
            'action' => 'style',
            'changemode' => 'input',
            'style_properity' => 'max-width',
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
        'pv' => [
            'type' => 'range',
            'section' => 'style',
            'title' => get_string('pvlabel', 'local_mb2builder'),
            'min' => 0,
            'max' => 300,
            'default' => 0,
            'action' => 'style',
            'selector' => '.theme-text-inner',
            'changemode' => 'input',
            'style_properity' => 'padding-top',
            'style_properity2' => 'padding-bottom',
        ],
        'ph' => [
            'type' => 'range',
            'section' => 'style',
            'title' => get_string('phlabel', 'local_mb2builder'),
            'min' => 0,
            'max' => 300,
            'default' => 30,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-text-inner',
            'style_properity' => 'padding-left',
            'style_properity2' => 'padding-right',
        ],
        'rounded' => [
            'type' => 'yesno',
            'section' => 'style',
            'title' => get_string('rounded', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'action' => 'class',
            'class_remove' => 'rounded0 rounded1',
            'class_prefix' => 'rounded',
            'selector' => '.theme-text-inner',
            'default' => 0,
        ],
        'button' => [
            'type' => 'buttons',
            'section' => 'button',
            'title' => get_string('readmorebtn', 'local_mb2builder'),
            'options' => [
                0 => get_string('none', 'local_mb2builder'),
                1 => get_string('arrowbtn', 'local_mb2builder'),
                2 => get_string('button', 'local_mb2builder'),
            ],
            'action' => 'class',
            'selector' => '.theme-text-inner',
            'class_remove' => 'linkbtn0 linkbtn1 linkbtn2',
            'class_prefix' => 'linkbtn',
            'default' => 0,
        ],
        'btext' => [
            'type' => 'text',
            'section' => 'button',
            'title' => get_string('titletext', 'local_mb2builder'),
            'action' => 'text',
            'selector' => '.theme-text-button a',
        ],
        'link' => [
            'type' => 'text',
            'section' => 'button',
            'title' => get_string('link', 'local_mb2builder'),
            'default' => '#',
        ],
        'target' => [
            'type' => 'yesno',
            'section' => 'button',
            'title' => get_string('linknewwindow', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'action' => 'none',
            'default' => 0,
        ],
        'btype' => [
            'type' => 'list',
            'section' => 'button',
            'showon' => 'button:2',
            'title' => get_string('type', 'local_mb2builder'),
            'options' => [
                'default' => get_string('default', 'local_mb2builder'),
                'primary' => get_string('primary', 'local_mb2builder'),
                'secondary' => get_string('secondary', 'local_mb2builder'),
                'success' => get_string('success', 'local_mb2builder'),
                'warning' => get_string('warning', 'local_mb2builder'),
                'info' => get_string('info', 'local_mb2builder'),
                'danger' => get_string('danger', 'local_mb2builder'),
                'inverse' => get_string('inverse', 'local_mb2builder'),
            ],
            'default' => 'primary',
            'action' => 'class',
            'selector' => '.theme-text-button a',
            'class_remove' => 'typeprimary typesecondary typesuccess typewarning typeinfo typedanger typeinverse',
            'class_prefix' => 'type',
        ],
        'bsize' => [
            'type' => 'buttons',
            'showon' => 'button:2',
            'section' => 'button',
            'title' => get_string('sizelabel', 'local_mb2builder', ''),
            'options' => [
                'sm' => get_string('small', 'local_mb2builder'),
                'normal' => get_string('medium', 'local_mb2builder'),
                'lg' => get_string('large', 'local_mb2builder'),
                'xlg' => get_string('xlarge', 'local_mb2builder'),
            ],
            'default' => 'normal',
            'action' => 'class',
            'selector' => '.theme-text-button a',
            'class_remove' => 'sizesm sizelg sizexlg sizenormal',
            'class_prefix' => 'size',
        ],
        'bfwcls' => [
            'type' => 'buttons',
            'showon' => 'button:2',
            'section' => 'button',
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
            'selector' => '.mb2-pb-btn',
            'class_remove' => 'fwglobal fwlight fwnormal fwmedium fwsemibold fwbold',
            'class_prefix' => 'fw',
        ],
        'brounded' => [
            'type' => 'buttons',
            'showon' => 'button:2',
            'section' => 'button',
            'title' => get_string('rounded', 'local_mb2builder'),
            'options' => [
                0 => get_string('global', 'local_mb2builder'),
                1 => get_string('yes', 'local_mb2builder'),
                -1 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.theme-text-button .mb2-pb-btn',
            'class_remove' => 'rounded0 rounded1 rounded-1',
            'class_prefix' => 'rounded',
        ],
        'bborder' => [
            'type' => 'yesno',
            'showon' => 'button:2',
            'section' => 'button',
            'title' => get_string('border', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.theme-text-button .mb2-pb-btn',
            'class_remove' => 'btnborder0 btnborder1',
            'class_prefix' => 'btnborder',
        ],
        'bmt' => [
            'type' => 'range',
            'section' => 'button',
            'title' => get_string('mt', 'local_mb2builder'),
            'min' => 0,
            'max' => 300,
            'default' => 0,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-text-button',
            'style_properity' => 'padding-top',
        ],
        'group_btn_start_1' => [
            'type' => 'group_start', 'section' => 'button', 'title' => get_string('normal', 'local_mb2builder')], // Group start.
            'btncolor' => [
                'type' => 'color',
                'section' => 'button',
                'title' => get_string('color', 'local_mb2builder'),
                'action' => 'color',
                'selector' => '.mb2-pb-btn,.arrowlink',
                'cssvariable' => '--mb2-pb-btn-color',
            ],
            'btnbgcolor' => [
                'type' => 'color',
                'section' => 'button',
                'title' => get_string('bgcolor', 'local_mb2builder'),
                'action' => 'color',
                'selector' => '.mb2-pb-btn,.arrowlink',
                'cssvariable' => '--mb2-pb-btn-bgcolor',
            ],
            'btnborcolor' => [
                'type' => 'color',
                'section' => 'button',
                'title' => get_string('bordercolor', 'local_mb2builder'),
                'action' => 'color',
                'selector' => '.mb2-pb-btn,.arrowlink',
                'cssvariable' => '--mb2-pb-btn-borcolor',
            ],
            'group_btn_end_1' => ['type' => 'group_end', 'section' => 'button'], // Group end.

            'group_btn_start_2' => [
            'type' => 'group_start', 'section' => 'button', 'title' =>
            get_string('hover_active', 'local_mb2builder')], // Group start.
            'btnhcolor' => [
                'type' => 'color',
                'section' => 'button',
                'title' => get_string('color', 'local_mb2builder'),
                'action' => 'color',
                'selector' => '.mb2-pb-btn,.arrowlink',
                'cssvariable' => '--mb2-pb-btn-hcolor',
            ],
            'btnbghcolor' => [
                'type' => 'color',
                'section' => 'button',
                'title' => get_string('bgcolor', 'local_mb2builder'),
                'action' => 'color',
                'selector' => '.mb2-pb-btn,.arrowlink',
                'cssvariable' => '--mb2-pb-btn-bghcolor',
            ],
            'btnborhcolor' => [
                'type' => 'color',
                'section' => 'button',
                'title' => get_string('bordercolor', 'local_mb2builder'),
                'action' => 'color',
                'selector' => '.mb2-pb-btn,.arrowlink',
                'cssvariable' => '--mb2-pb-btn-borhcolor',
            ],
            'group_btn_end_2' => ['type' => 'group_end', 'section' => 'button'], // Group end.
        'custom_class' => [
            'type' => 'text',
            'section' => 'style',
            'title' => get_string('customclasslabel', 'local_mb2builder'),
            'desc' => get_string('customclassdesc', 'local_mb2builder'),
            'action' => 'class',
            'selector' => 'theme-text',
        ],
    ],
];

define('LOCAL_MB2BUILDER_SETTINGS_TEXT', base64_encode(json_encode($mb2settings)));
