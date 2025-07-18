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
    'id' => 'boxesimg',
    'subid' => 'boxesimg_item',
    'title' => get_string('elboxesimg', 'local_mb2builder'),
    'icon' => 'fa fa-object-group',
    'type' => 'general',
    'tabs' => [
        'general' => get_string('generaltab', 'local_mb2builder'),
        'typo' => get_string('typotab', 'local_mb2builder'),
        'button' => get_string('button', 'local_mb2builder'),
        'colors' => get_string('colorstab', 'local_mb2builder'),
        'style' => get_string('styletab', 'local_mb2builder'),
    ],
    'attr' => [
        'type' => [
            'type' => 'list',
            'section' => 'general',
            'title' => get_string('type', 'local_mb2builder'),
            'options' => [
                0 => get_string('none', 'local_mb2builder'),
                1 => get_string('typen', 'local_mb2builder', ['type' => 1]),
                2 => get_string('typen', 'local_mb2builder', ['type' => 2]),
                3 => get_string('typen', 'local_mb2builder', ['type' => 3]),
                4 => get_string('typen', 'local_mb2builder', ['type' => 4]),
                5 => get_string('typen', 'local_mb2builder', ['type' => 5]),
                6 => get_string('typen', 'local_mb2builder', ['type' => 6]),
                7 => get_string('typen', 'local_mb2builder', ['type' => 7]),
            ],
            'default' => 1,
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'type-0 type-1 type-2 type-3 type-4 type-5 type-6 type-7',
            'class_prefix' => 'type-',
        ],
        'columns' => [
            'type' => 'range',
            'min' => 1,
            'max' => 5,
            'section' => 'general',
            'title' => get_string('columns', 'local_mb2builder'),
            'default' => 4,
            'label_suffix' => 'num',
            'action' => 'class',
            'changemode' => 'input',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'theme-col-1 theme-col-2 theme-col-3 theme-col-4 theme-col-5',
            'class_prefix' => 'theme-col-',
        ],
        'gutter' => [
            'type' => 'buttons',
            'section' => 'general',
            'title' => get_string('grdwidth', 'local_mb2builder'),
            'options' => [
                'normal' => get_string('normal', 'local_mb2builder'),
                'thin' => get_string('thin', 'local_mb2builder'),
                'none' => get_string('none', 'local_mb2builder'),
            ],
            'default' => 'normal',
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'gutter-thin gutter-normal gutter-none',
            'class_prefix' => 'gutter-',
        ],
        'rowgap' => [
            'type' => 'range',
            'section' => 'general',
            'title' => get_string('vgap', 'local_mb2builder'),
            'min' => 0,
            'max' => 100,
            'default' => 0,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.theme-boxes',
            'style_properity' => '--mb2-pb-bxrowgap',
        ],
        'imgwidth' => [
            'type' => 'range',
            'section' => 'general',
            'title' => get_string('imgwidth', 'local_mb2builder'),
            'min' => 20,
            'max' => 1200,
            'default' => 800,
            'action' => 'style',
            'selector' => '.theme-boximg-img',
            'changemode' => 'input',
            'style_properity' => 'max-width',
        ],
        'tfs' => [
            'type' => 'range',
            'section' => 'typo',
            'title' => get_string('titlefs', 'local_mb2builder'),
            'min' => 1,
            'max' => 10,
            'step' => 0.01,
            'default' => 1.4,
            'action' => 'style',
            'changemode' => 'input',
            'selector' => '.box-title',
            'style_properity' => 'font-size',
            'style_suffix' => 'rem',
            'numclass' => 1,
            'sizepref' => 'pbtsize',
        ],
        'tfw' => [
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
            'selector' => '.box-title',
            'class_remove' => 'fwglobal fwlight fwnormal fwmedium fwsemibold fwbold',
            'class_prefix' => 'fw',
        ],
        'tlh' => [
            'type' => 'buttons',
            'section' => 'typo',
            'title' => get_string('lh', 'local_mb2builder'),
            'options' => [
                'global' => get_string('global', 'local_mb2builder'),
                'small' => get_string('wsmall', 'local_mb2builder'),
                'normal' => get_string('normal', 'local_mb2builder'),
            ],
            'default' => 'global',
            'action' => 'class',
            'selector' => '.box-title',
            'class_remove' => 'lhglobal lhsmall lhnormal',
            'class_prefix' => 'lh',
        ],
        'center' => [
            'type' => 'yesno',
            'section' => 'typo',
            'title' => get_string('tcenter', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'center0 center1',
            'class_prefix' => 'center',
            'default' => 0,
        ],
        'desc' => [
            'type' => 'yesno',
            'section' => 'general',
            'title' => get_string('content', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'desc0 desc1',
            'class_prefix' => 'desc',
            'default' => 0,
        ],
        'rounded' => [
            'type' => 'yesno',
            'section' => 'general',
            'title' => get_string('rounded', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'rounded0 rounded1',
            'class_prefix' => 'rounded',
            'default' => 0,
        ],
        'shadow' => [
            'type' => 'yesno',
            'section' => 'general',
            'title' => get_string('shadow', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'shadow0 shadow1',
            'class_prefix' => 'shadow',
            'default' => 0,
        ],
        'border' => [
            'type' => 'range',
            'section' => 'general',
            'title' => get_string('borderw', 'local_mb2builder'),
            'min' => 0,
            'max' => 10,
            'default' => 0,
            'action' => 'style',
            'selector' => '.theme-boximg',
            'changemode' => 'input',
            'style_properity' => '--mb2-pb-bxborder',
        ],
        'linkbtn' => [
            'type' => 'buttons',
            'section' => 'button',
            'title' => get_string('readmorebtn', 'local_mb2builder'),
            'options' => [
                0 => get_string('none', 'local_mb2builder'),
                1 => get_string('arrowbtn', 'local_mb2builder'),
                2 => get_string('button', 'local_mb2builder'),
            ],
            'action' => 'class',
            'selector' => '.mb2-pb-element-inner',
            'class_remove' => 'linkbtn0 linkbtn1 linkbtn2',
            'class_prefix' => 'linkbtn',
            'default' => 0,
        ],
        'btntext' => [
            'type' => 'text',
            'section' => 'button',
            'showon' => 'linkbtn:1|2',
            'title' => get_string('btntext', 'local_mb2builder'),
            'action' => 'text',
            'selector' => '.box-readmore a',
            'default' => get_string('readmorefp', 'local_mb2builder'),
        ],
        'btntype' => [
            'type' => 'list',
            'section' => 'button',
            'showon' => 'linkbtn:2',
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
            'selector' => '.box-readmore .mb2-pb-btn',
            'class_remove' => 'typeprimary typesecondary typesuccess typewarning typeinfo typedanger typeinverse',
            'class_prefix' => 'type',
        ],
        'btnsize' => [
            'type' => 'buttons',
            'section' => 'button',
            'showon' => 'linkbtn:2',
            'title' => get_string('sizelabel', 'local_mb2builder', ''),
            'options' => [
                'sm' => get_string('small', 'local_mb2builder'),
                'normal' => get_string('medium', 'local_mb2builder'),
                'lg' => get_string('large', 'local_mb2builder'),
                'xlg' => get_string('xlarge', 'local_mb2builder'),
            ],
            'default' => 'normal',
            'action' => 'class',
            'selector' => '.box-readmore .mb2-pb-btn',
            'class_remove' => 'sizesm sizelg sizexlg sizenormal',
            'class_prefix' => 'size',
        ],
        'btnfwcls' => [
            'type' => 'buttons',
            'showon' => 'linkbtn:2',
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
        'btnrounded' => [
            'type' => 'buttons',
            'section' => 'button',
            'showon' => 'linkbtn:2',
            'title' => get_string('rounded', 'local_mb2builder'),
            'options' => [
                0 => get_string('global', 'local_mb2builder'),
                1 => get_string('yes', 'local_mb2builder'),
                -1 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.box-readmore .mb2-pb-btn',
            'class_remove' => 'rounded0 rounded1 rounded-1',
            'class_prefix' => 'rounded',
        ],
        'btnborder' => [
            'type' => 'yesno',
            'section' => 'button',
            'showon' => 'linkbtn:2',
            'title' => get_string('border', 'local_mb2builder'),
            'options' => [
                1 => get_string('yes', 'local_mb2builder'),
                0 => get_string('no', 'local_mb2builder'),
            ],
            'default' => 0,
            'action' => 'class',
            'selector' => '.box-readmore .mb2-pb-btn',
            'class_remove' => 'btnborder0 btnborder1',
            'class_prefix' => 'btnborder',
        ],
        'ccolor' => [
            'type' => 'color',
            'section' => 'colors',
            'title' => get_string('accentcolor', 'local_mb2builder'),
            'action' => 'color',
            'changemode' => 'input',
            'selector' => '.theme-boxes',
            'style_properity' => '--mb2-pb-bxaccolor',
        ],

        'bgcolor' => [
            'type' => 'color',
            'section' => 'colors',
            'title' => get_string('bgcolor', 'local_mb2builder'),
            'action' => 'color',
            'changemode' => 'input',
            'selector' => '.theme-boxes',
            'style_properity' => '--mb2-pb-bxbgcolor',
        ],
        'tcolor' => [
            'type' => 'color',
            'section' => 'colors',
            'title' => get_string('titlecolor', 'local_mb2builder'),
            'action' => 'color',
            'changemode' => 'input',
            'selector' => '.theme-boxes',
            'style_properity' => '--mb2-pb-bxtcolor',
        ],
        'txcolor' => [
            'type' => 'color',
            'section' => 'colors',
            'title' => get_string('textcolor', 'local_mb2builder'),
            'action' => 'color',
            'changemode' => 'input',
            'selector' => '.theme-boxes',
            'style_properity' => '--mb2-pb-bxtxcolor',
        ],
        'bocolor' => [
            'type' => 'color',
            'section' => 'colors',
            'title' => get_string('bordercolor', 'local_mb2builder'),
            'action' => 'color',
            'changemode' => 'input',
            'selector' => '.theme-boxes',
            'style_properity' => '--mb2-pb-bxbocolor',
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
            'default' => 0,
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
            'button' => get_string('button', 'local_mb2builder'),
            'colors' => get_string('colorstab', 'local_mb2builder'),
            'style' => get_string('styletab', 'local_mb2builder'),
        ],
        'attr' => [
            'image' => [
                'type' => 'image',
                'section' => 'general',
                'title' => get_string('image', 'local_mb2builder'),
                'action' => 'image',
                'selectorbg' => '.theme-boximg-imgel',
                'selector' => '.theme-boximg-img',
            ],
            'text' => [
                'type' => 'text',
                'section' => 'general',
                'title' => get_string('title', 'local_mb2builder'),
                'action' => 'text',
                'selector' => '.box-title-text',
            ],
            'description' => [
                'type' => 'textarea',
                'section' => 'general',
                'title' => get_string('text', 'local_mb2builder'),
                'action' => 'text',
                'selector' => '.box-desc',
                'default' => 'Box content here.',
            ],
            'el_onmobile' => [
                'type' => 'yesno',
                'section' => 'style',
                'title' => get_string('onmobile', 'local_mb2builder'),
                'options' => [
                    1 => get_string('yes', 'local_mb2builder'),
                    0 => get_string('no', 'local_mb2builder'),
                ],
                'default' => 1,
                'action' => 'class',
                'class_remove' => 'el_onmobile0 el_onmobile1',
                'class_prefix' => 'el_onmobile',
            ],

            'ccolor' => [
                'type' => 'color',
                'section' => 'colors',
                'title' => get_string('accentcolor', 'local_mb2builder'),
                'action' => 'color',
                'changemode' => 'input',
                'selector' => '.theme-boximg',
                'style_properity' => '--mb2-pb-bxaccolor',
            ],
            'color' => [
                'type' => 'color',
                'section' => 'colors',
                'title' => get_string('bgcolor', 'local_mb2builder'),
                'action' => 'color',
                'changemode' => 'input',
                'selector' => '.theme-boximg',
                'style_properity' => '--mb2-pb-bxbgcolor',
            ],
            'tcolor' => [
                'type' => 'color',
                'section' => 'colors',
                'title' => get_string('titlecolor', 'local_mb2builder'),
                'action' => 'color',
                'changemode' => 'input',
                'selector' => '.theme-boximg',
                'style_properity' => '--mb2-pb-bxtcolor',
            ],
            'txcolor' => [
                'type' => 'color',
                'section' => 'colors',
                'title' => get_string('textcolor', 'local_mb2builder'),
                'action' => 'color',
                'changemode' => 'input',
                'selector' => '.theme-boximg',
                'style_properity' => '--mb2-pb-bxtxcolor',
            ],
            'bocolor' => [
                'type' => 'color',
                'section' => 'colors',
                'title' => get_string('bordercolor', 'local_mb2builder'),
                'action' => 'color',
                'changemode' => 'input',
                'selector' => '.theme-boximg',
                'style_properity' => '--mb2-pb-bxbocolor',
            ],

            'link' => [
                'type' => 'text',
                'section' => 'general',
                'title' => get_string('link', 'local_mb2builder'),
            ],
            'link_target' => [
                'type' => 'yesno',
                'section' => 'general',
                'title' => get_string('linknewwindow', 'local_mb2builder'),
                'options' => [
                    1 => get_string('yes', 'local_mb2builder'),
                    0 => get_string('no', 'local_mb2builder'),
                ],
                'action' => 'none',
                'default' => 0,
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
        ],
    ],
];

define('LOCAL_MB2BUILDER_SETTINGS_BOXESIMG', base64_encode(json_encode($mb2settings)));
