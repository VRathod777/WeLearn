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
 * Defines forms.
 *
 * @package    local_mb2megamenu
 * @copyright  2019 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

if (!class_exists('Mb2megamenuSettings')) {

    /**
     *
     * Settings class
     */
    class Mb2megamenuSettings {

        /**
         *
         * Method to set menu item settings form in modal window
         *
         */
        public static function menuitem_settings_form() {
            $output = '';
            $settings = self::menuitem_settings();

            foreach ($settings as $setting) {
                $levelcls = $setting['level'] == -1 ? 'all' : $setting['level'];

                $output .= '<div class="mb2megamenu-modalform-item level-' . $levelcls . '">';

                switch( $setting['type'] ){
                    case 'list' :
                        $output .= self::field_list($setting);
                    break;

                    case 'checkbox' :
                        $output .= self::field_checkbox($setting);
                    break;

                    case 'icon' :
                        $output .= self::field_icon($setting);
                    break;

                    case 'image' :
                        $output .= self::field_image($setting);
                    break;

                    case 'color' :
                        $output .= self::field_color($setting);
                    break;

                    case 'textarea' :
                        $output .= self::field_textarea($setting);
                    break;

                    case 'number' :
                        $output .= self::field_number($setting);
                    break;

                    default :
                        $output .= self::field_text($setting);
                    break;
                }

                $output .= '</div>'; // ...mb2megamenu-modalform-item
            }

            return $output;

        }




        /**
         *
         * Method to define menu item settings
         *
         */
        public static function menuitem_settings() {

            // Level visible:
            // -1 - all menu items,
            // 1 - mega menu parent item,
            // 2 - mega menu parent item AND mega menu columns,
            // 3 - mega menu columns items.

            $settings = [
                [
                    'id' => 'hidetext',
                    'type' => 'checkbox',
                    'label' => get_string('hidetext', 'local_mb2megamenu'),
                    'desc' => get_string('hidetextdesc', 'local_mb2megamenu'),
                    'options' => [
                        1 => get_string('yes'),
                        0 => get_string('no'),
                    ],
                    'default' => 0,
                    'level' => 3,
                ],
                [
                    'id' => 'hcolor',
                    'type' => 'color',
                    'label' => get_string('textcolor', 'local_mb2megamenu'),
                    'twinlabel1' => get_string('normalstate', 'local_mb2megamenu'),
                    'twinlabel2' => get_string('hoverstate', 'local_mb2megamenu'),
                    'desc' => '',
                    'default' => '',
                    'twin' => [
                        'id' => 'hhcolor',
                        'type' => 'color',
                        'label' => '',
                        'desc' => '',
                        'default' => '',
                    ],
                    'level' => 3,
                ],
                [
                    'id' => 'sublabel',
                    'type' => 'textarea',
                    'label' => get_string('sublabel', 'local_mb2megamenu'),
                    'desc' => get_string('sublabeldesc', 'local_mb2megamenu'),
                    'default' => '',
                    'children' => [
                        [
                            'id' => 'childlabel',
                            'content' => get_string('textcolor', 'local_mb2megamenu'),
                        ],
                        [
                            'id' => 'hsubcolor',
                            'type' => 'color',
                            'label' => get_string('normalstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        [
                            'id' => 'hsubhcolor',
                            'type' => 'color',
                            'label' => get_string('hoverstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        'level' => 3,
                    ],
                    'level' => -1,
                ],
                [
                    'id' => 'sublabel2',
                    'type' => 'text',
                    'label' => get_string('sublabel2', 'local_mb2megamenu'),
                    'desc' => get_string('sublabel2desc', 'local_mb2megamenu'),
                    'default' => '',
                    'children' => [
                        [
                            'id' => 'childlabel',
                            'content' => get_string('textcolor', 'local_mb2megamenu'),
                        ],
                        [
                            'id' => 'hsub2color',
                            'type' => 'color',
                            'label' => get_string('normalstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        [
                            'id' => 'hsub2hcolor',
                            'type' => 'color',
                            'label' => get_string('hoverstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        'level' => 3,
                    ],
                    'level' => 3,
                ],
                [
                    'id' => 'hlabel',
                    'type' => 'text',
                    'label' => get_string('hlabel', 'local_mb2megamenu'),
                    'desc' => get_string('hlabeldesc', 'local_mb2megamenu'),
                    'default' => '',
                    'children' => [
                        [
                            'id' => 'hlabelcolor',
                            'type' => 'color',
                            'label' => get_string('textcolor', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        [
                            'id' => 'hlabelbgcolor',
                            'type' => 'color',
                            'label' => get_string('bgcolor', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        'level' => -1,
                    ],
                    'level' => -1,
                ],
                [
                    'id' => 'icon',
                    'type' => 'icon',
                    'label' => get_string('icon'),
                    'desc' => '',
                    'default' => '',
                    'children' => [
                        [
                            'id' => 'childlabel',
                            'content' => get_string('hiconcolor', 'local_mb2megamenu'),
                        ],
                        [
                            'id' => 'hiconcolor',
                            'type' => 'color',
                            'label' => get_string('normalstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        [
                            'id' => 'hiconhcolor',
                            'type' => 'color',
                            'label' => get_string('hoverstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        [
                            'id' => 'childlabel',
                            'content' => get_string('hiconbgcolor', 'local_mb2megamenu'),
                        ],
                        [
                            'id' => 'hiconbgcolor',
                            'type' => 'color',
                            'label' => get_string('normalstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        [
                            'id' => 'hiconhbgcolor',
                            'type' => 'color',
                            'label' => get_string('hoverstate', 'local_mb2megamenu'),
                            'desc' => '',
                            'default' => '',
                        ],
                        'level' => 3,
                    ],
                    'level' => -1,
                ],
                [
                    'id' => 'image',
                    'type' => 'image',
                    'label' => get_string('bgimage', 'local_mb2megamenu'),
                    'desc' => get_string('bgimagedesc', 'local_mb2megamenu'),
                    'default' => '',
                    'level' => 2,
                ],
                [
                    'id' => 'bgcolor',
                    'type' => 'color',
                    'label' => get_string('bgcolor', 'local_mb2megamenu'),
                    'desc' => get_string('bgcolordesc', 'local_mb2megamenu'),
                    'twinlabel1' => get_string('normalstate', 'local_mb2megamenu'),
                    'twinlabel2' => get_string('hoverstate', 'local_mb2megamenu'),
                    'default' => '',
                    'twin' => [
                        'id' => 'bghcolor',
                        'type' => 'color',
                        'label' => '',
                        'desc' => '',
                        'default' => '',
                    ],
                    'level' => 2,
                ],
                [
                    'id' => 'cwidth',
                    'type' => 'number',
                    'min' => 100,
                    'max' => 900,
                    'label' => get_string('cwidth', 'local_mb2megamenu'),
                    'desc' => get_string('cwidthdesc', 'local_mb2megamenu'),
                    'default' => '',
                    'level' => 3,
                ],
                [
                    'id' => 'mega',
                    'type' => 'checkbox',
                    'label' => get_string('megamenu', 'local_mb2megamenu'),
                    'desc' => get_string('megamenudesc', 'local_mb2megamenu'),
                    'options' => [
                        1 => get_string('yes'),
                        0 => get_string('no'),
                    ],
                    'default' => 0,
                    'level' => 1,
                ],
                [
                    'id' => 'columns',
                    'type' => 'list',
                    'label' => get_string('columns', 'local_mb2megamenu'),
                    'desc' => get_string('columnsdesc', 'local_mb2megamenu'),
                    'options' => [
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ],
                    'default' => 3,
                    'level' => 1,
                ],
                [
                    'id' => 'distcols',
                    'type' => 'checkbox',
                    'label' => get_string('distcols', 'local_mb2megamenu'),
                    'desc' => get_string('distcolsdesc', 'local_mb2megamenu'),
                    'options' => [
                        1 => get_string('yes'),
                        0 => get_string('no'),
                    ],
                    'default' => 0,
                    'level' => 1,
                ],
                [
                    'id' => 'width',
                    'type' => 'list',
                    'label' => get_string('megawidth', 'local_mb2megamenu'),
                    'desc' => get_string('megawidthdesc', 'local_mb2megamenu'),
                    'options' => [
                        'aw' => get_string('auto', 'local_mb2megamenu'),
                        'fw' => get_string('maxwidth', 'local_mb2megamenu'),
                        'cw' => get_string('contentwidth', 'local_mb2megamenu'),
                    ],
                    'default' => 'aw',
                    'level' => 1,
                ],
                [
                    'id' => 'cssclass',
                    'type' => 'text',
                    'label' => get_string('cssclass', 'local_mb2megamenu'),
                    'desc' => get_string('cssclassdesc', 'local_mb2megamenu'),
                    'default' => '',
                    'level' => -1,
                ],
            ];

            return $settings;

        }




        /**
         *
         * Method to set list form filed
         *
         */
        public static function field_list($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<select name="' . $setting['id'] . '" class="mb2megamenu-input input-'. $setting['type'] .
            ' form-control" data-value="' . $setting['default'] . '">';

            foreach ($setting['options'] as $k => $opt) {
                $output .= '<option value="' . $k . '">' . $opt . '</option>';
            }

            $output .= '</select>';
            $output .= '</div>'; // ...form-controls

            return $output;

        }





        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_checkbox($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<input type="checkbox" class="mb2megamenu-input input-'. $setting['type'] .'" data-value="' .
            $setting['default'] . '" name="' . $setting['id'] . '">';
            $output .= self::field_children($setting);
            $output .= '</div>'; // ...form-controls

            return $output;

        }





        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_icon($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<div class="form-preview"></div>';
            $output .= '<div class="form-buttons">';
            $output .= '<button type="button" class="mb2megamenu-icon-select mb2-pb-btn typesuccess sizexs
            mb2megamenu-activate-option" data-modal="#mb2megamenu-modal-icons">' .
            get_string('select') . '</button>';
            $output .= ' <button type="button" class="mb2megamenu-fieldremove mb2-pb-btn typedanger sizexs">' .
            get_string('remove') . '</button>';
            $output .= '</div>'; // ...form-buttons
            $output .= '<input type="hidden" class="mb2megamenu-input input-'. $setting['type'] .'" name="' . $setting['id'] .
            '" data-value="' . $setting['default'] . '">';
            $output .= self::field_children($setting);
            $output .= '</div>'; // ...form-controls

            return $output;

        }




        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_image($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<div class="form-preview"></div>';
            $output .= '<div class="form-buttons">';
            $output .= '<button type="button" class="mb2-pb-btn typesuccess sizexs mb2megamenu-activate-option"
            data-modal="#mb2megamenu-modal-images">' .
            get_string('select') . '</button>';
            $output .= ' <button type="button" class="mb2megamenu-fieldremove mb2-pb-btn typedanger sizexs">' .
            get_string('remove') . '</button>';
            $output .= '</div>'; // ...form-buttons
            $output .= '<input type="hidden" class="mb2megamenu-input input-'. $setting['type'] .'" name="' . $setting['id'] .
            '" data-value="' . $setting['default'] . '">';
            $output .= '</div>'; // ...form-controls

            return $output;

        }




        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_text($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<input type="text" class="mb2megamenu-input input-'. $setting['type'] .'" name="' . $setting['id'] .
            '" data-value="' . $setting['default'] . '">';
            $output .= self::field_children($setting);
            $output .= '</div>'; // ...form-controls

            return $output;

        }





        /**
         *
         * Method to set number form filed
         *
         */
        public static function field_number($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<input type="number" class="mb2megamenu-input input-'. $setting['type'] .'" name="' . $setting['id'] .
            '" min="'. $setting['min'] .'" max="'. $setting['max'] .'" data-value="' . $setting['default'] . '">';
            $output .= self::field_children($setting);
            $output .= '</div>'; // ...form-controls
            return $output;

        }






        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_textarea($setting) {

            $output = '';
            $output .= self::field_item_label($setting);

            $output .= '<div class="form-controls">';
            $output .= '<textarea class="mb2megamenu-input input-'. $setting['type'] .'" name="' . $setting['id'] . '">' .
            $setting['default'] . '</textarea>';
            $output .= self::field_children($setting);
            $output .= '</div>'; // ...form-controls

            return $output;

        }




        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_color($setting) {

            $output = '';
            $output .= self::field_item_label($setting);
            $twin = isset( $setting['twin'] );
            $cls = $twin ? ' istwin' : '';

            $output .= '<div class="form-controls' . $cls . '">';

            $output .= $twin ? '<div class="form-twin-control">' : '';
            $output .= $twin ? '<span class="form-twin-label">' . $setting['twinlabel1'] . ':</span>' : '';
            $output .= '<input type="text" class="mb2megamenu-input mb2color input-'. $setting['type'] .'" name="' . $setting['id']
            . '" data-value="' . $setting['default'] . '">';
            $output .= $twin ? '</div>' : '';

            if ( isset( $setting['twin'] ) ) {
                $twin = $setting['twin'];
                $output .= '<div class="form-twin-control">';
                $output .= '<span class="form-twin-label">' . $setting['twinlabel2'] . ':</span>';
                $output .= '<input type="text" class="mb2megamenu-input mb2color input-'. $twin['type'] .'" name="' . $twin['id'] .
                '" data-value="' . $twin['default'] . '">';
                $output .= '</div>';
            }

            $output .= '</div>'; // ...form-controls

            return $output;

        }







        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_item_label($setting) {

            $output = '';

            $output .= '<div class="form-label">';
            $output .= '<label>' . $setting['label'] . '</label>';
            $output .= '<div class="desc">' . $setting['desc'] . '</div>';
            $output .= '</div>'; // ...form-label

            return $output;

        }





        /**
         *
         * Method to set checkbox form filed
         *
         */
        public static function field_children($setting) {

            $output = '';

            if (!isset($setting['children'])) {
                return;
            }

            $levelcls = $setting['children']['level'] == -1 ? 'all' : $setting['children']['level'];

            $output .= '<div class="form-children-controls level-' . $levelcls . '">';

            foreach ($setting['children'] as $child) {

                if (!is_array($child)) {
                    continue;
                }

                // String case for labels.
                if ($child['id'] === 'childlabel') {
                    $output .= '<div class="form-child-label">' . $child['content'] . ':</div>';
                } else {
                    $output .= '<div class="form-twin-control">';
                    $output .= '<span class="form-twin-label">' . $child['label'] . ':</span>';
                    $output .= '<input type="text" class="mb2megamenu-input mb2color input-'. $child['type'] .'" name="' .
                    $child['id'] . '" data-value="' . $child['default'] . '">';
                    $output .= '</div>';
                }

            }

            $output .= '</div>'; // ...form-children-controls

            return $output;

        }


        /**
         *
         * Method to to get font icons
         *
         */
        public static function get_icons() {

            if (!file_exists(LOCAL_MB2MEGAMENU_PATH_THEME . '/lib.php')) {
                return [];
            }

            require_once(LOCAL_MB2MEGAMENU_PATH_THEME . '/lib.php');

            if (!function_exists('theme_mb2nl_get_icons4plugins')) {
                return [];
            }

            $iconsarr = [];

            // Get cache.
            $cache = cache::make('local_mb2megamenu', 'icons');
            $cacheid = 'fonticons';

            if ($cache->get($cacheid)) {
                return $cache->get($cacheid);
            }

            $themeicons = theme_mb2nl_get_icons4plugins();

            foreach ($themeicons as $iconskey => $icons) {
                $csspath = LOCAL_MB2MEGAMENU_PATH_THEME_ASSETS . '/' . $icons['folder'] . '/' . $icons['css'] . '.css';
                if (!file_exists($csspath)) {
                    continue;
                }

                $iconsarr[$iconskey]['tabid'] = $icons['tabid'];
                $iconsarr[$iconskey]['name'] = $icons['name'];

                $iconsfont = self::icons($csspath);
                foreach ($iconsfont as $iconkey => $icon) {
                    $iconsarr[$iconskey]['icons'][] = $icons['prefhtml'] . $iconkey;
                }
            }

            $cache->set($cacheid, $iconsarr);
            return $iconsarr;

        }


        /**
         *
         * Method to set icons tab
         *
         */
        public static function font_icon_tabs() {

            $themeicons = self::get_icons();

            if (!count($themeicons)) {
                return;
            }

            $output = '';

            $i = 0;
            $x = 0;

            $output .= '<div class="mb2megamenu-icon-tabs theme-tabs tabs-sm">';
            $output .= '<ul class="nav nav-tabs">';

            foreach ($themeicons as $icons) {
                $i++;
                $activecls = $i == 1 ? ' active' : '';
                $output .= '<li class="nav-item' .  $activecls . '"><a class="nav-link' .  $activecls .
                '" data-toggle="tab" data-bs-toggle="tab" href="#' . $icons['tabid'] . '">' . $icons['name'] . '</a></li>';
            }

            $output .= '</ul>';
            $output .= '<div class="tab-content">';

            foreach ($themeicons as $icons) {
                $x++;
                $active = $x == 1 ? ' in active' : '';

                $output .= '<div id="' . $icons['tabid'] . '" class="tab-pane fade' . $active . '">';
                $output .= '<div class="search-modal-item"><input type="text" class="mb2megamenu-search-icon" placeholder="' .
                get_string('searchiconfor', 'local_mb2megamenu') . '" /></div>';
                $output .= '<div class="mb2megamenu-icons">';

                foreach ($icons['icons'] as $icon) {
                    $output .= '<button type="button" class="mb2megamenu-icon-toinsert themereset" data-iconname="' . $icon .
                    '" title="' . $icon . '" data-dismiss="mb2modal">';
                    $output .= '<i class="' . $icon . '"></i>';
                    $output .= '</button>';
                }

                $output .= '</div>';
                $output .= '</div>';

            }

            $output .= '</div>';
            $output .= '</div>';

            return $output;

        }







        /**
         *
         * Method to get icons array from icon css file
         *
         */
        public static function icons($path) {

            $icons = [];

            if (!file_exists($path)) {
                return [];
            }

            $css = file_get_contents($path);
            $pattern = '/\.((?:\w+(?:-)?)+):before\s+{\s*content:\s*"(.+)";\s+}/';

            preg_match_all($pattern, $css, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $icons[$match[1]] = $match[2];
            }

            return $icons;

        }




        /**
         *
         * Method to get list of images
         *
         */
        public static function images() {
            global $CFG;
            $output = '';
            $ajaxurl = new moodle_url($CFG->wwwroot . '/local/mb2megamenu/ajax/image-delete.php', []);

            $output .= '<div class="search-modal-item"><input type="text" class="mb2megamenu-search-image" placeholder="' .
            get_string( 'searchimagefor', 'local_mb2megamenu' ) . '" /></div>';
            $output .= '<div class="mb2megamenu-images" data-url="' . $ajaxurl . '">';
            $output .= self::image_preview();
            $output .= '</div>';
            $output .= '<div class="mb2megamenu-overlay"></div>';

            return $output;

        }


        /**
         *
         * Method to get images preview
         *
         */
        public static function image_preview() {
            $output = '';
            $fs = get_file_storage();
            $context = context_system::instance();
            $files = $fs->get_area_files($context->id, 'local_mb2megamenu', 'mb2megamenumedia');

            foreach ($files as $f) {
                if ($f->get_filename() === '.') {
                    continue;
                }

                $url = moodle_url::make_pluginfile_url( $f->get_contextid(), $f->get_component(), $f->get_filearea(), 0,
                $f->get_filepath(), $f->get_filename() );

                // For search in modal window we need  the 'simgname' (lowercase image name).
                $output .= '<div class="mb2megamenu-image-toinsert" data-imgname="' . $f->get_filename() . '" data-simgname="' .
                strtolower($f->get_filename()) . '">';
                $output .= '<button type="button" class="themereset imgremove">&times;</button>';
                $output .= '<button type="button" class="themereset mb2megamenu-insert-image" data-imgurl="' . $url . '">';
                $output .= '<img src="' . $url . '?preview=thumb" alt="' . $f->get_filename() . '" />';
                $output .= '<span class="imgdesc">' . $f->get_filename() . '</span>';
                $output .= '</button>';
                $output .= '</div>';
            }

            return $output;

        }

    }

}
