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

require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/layouts_api.php');
require_once(__DIR__ . '/pages_api.php');
require_once(__DIR__ . '/footers_api.php');
require_once(__DIR__ . '/parts_api.php');
require_once(__DIR__ . '/backup.php');

if (!class_exists('mb2builderApi')) {

    /**
     * Builder API
     *
     */
    class mb2builderApi {



        /**
         *
         * Method to get required fields
         *
         */
        public static function get_fields() {
            global $CFG;

            $fieldsdir = $CFG->dirroot . '/local/mb2builder/builder/fields/';

            foreach (scandir($fieldsdir) as $type) {
                $filetype = pathinfo($type, PATHINFO_EXTENSION);

                if ($filetype === 'php') {
                    require_once($fieldsdir . basename($type));
                }
            }
        }






        /**
         *
         * Method to get settings of bulder elements
         *
         */
        public static function get_element_settings() {

            foreach (self::get_elements() as $el) {
                if (file_exists(LOCAL_MB2BUILDER_PATH_THEME_ELEMENTS . $el . '/settings.php')) {
                    require_once(LOCAL_MB2BUILDER_PATH_THEME_ELEMENTS . $el . '/settings.php');
                }
            }

        }








        /**
         *
         * Method to get settings of staic elements. Secion, row, column
         *
         */
        public static function get_static_elements_settings() {
            global $CFG;

            $elements = ['section', 'row', 'column'];

            foreach ($elements as $e) {
                if (file_exists( LOCAL_MB2BUILDER_PATH_THEME_SETTINGS . $e . '/settings.php' )) {
                    require_once( LOCAL_MB2BUILDER_PATH_THEME_SETTINGS . $e . '/settings.php' );
                }
            }

        }



        /**
         *
         * Method to get import layouts
         *
         */
        public static function get_import_layouts() {
            $layouts = [];

            if (!is_dir(LOCAL_MB2BUILDER_PATH_THEME_IMPORT_LAYOUTS)) {
                return [];
            }

            foreach (scandir(LOCAL_MB2BUILDER_PATH_THEME_IMPORT_LAYOUTS) as $layout) {
                $type = pathinfo($layout, PATHINFO_EXTENSION );

                if ($type !== 'php') {
                    continue;
                }

                $layouts[] = str_replace( '.php', '', $layout );
            }

            return $layouts;

        }








        /**
         *
         * Method to get import block
         *
         */
        public static function get_import_blocks() {
            $blocks = [];

            if (!is_dir( LOCAL_MB2BUILDER_PATH_THEME_IMPORT_BLOCKS )) {
                return [];
            }

            foreach (scandir( LOCAL_MB2BUILDER_PATH_THEME_IMPORT_BLOCKS ) as $block) {
                $type = pathinfo($block, PATHINFO_EXTENSION );

                if ($type !== 'php') {
                    continue;
                }

                $blocks[] = str_replace('.php', '', $block);
            }

            return $blocks;

        }






        /**
         *
         * Method to include import blocks settings
         *
         */
        public static function include_import_blocks() {
            // Include blocks settings.
            foreach (self::get_import_blocks() as $block) {
                require_once(LOCAL_MB2BUILDER_PATH_THEME_IMPORT_BLOCKS . $block . '.php');
            }

            // Include layouts settings.
            foreach (self::get_import_layouts() as $layout) {
                require_once(LOCAL_MB2BUILDER_PATH_THEME_IMPORT_LAYOUTS . $layout . '.php');
            }
        }



        /**
         *
         * Method to get settings of bulder elements
         *
         */
        public static function non_part_elements() {

            if (!has_capability('local/mb2builder:manageparts', context_system::instance())) {
                $config = get_config('local_mb2builder');
                return preg_split('/\s*\n\s*/', trim($config->nopartelements));
            }

            return [];

        }





        /**
         *
         * Method to get settings of bulder elements
         *
         */
        public static function get_elements() {

            $elements = [];

            if (is_dir(LOCAL_MB2BUILDER_PATH_THEME_ELEMENTS)) {
                foreach (scandir(LOCAL_MB2BUILDER_PATH_THEME_ELEMENTS) as $element) {
                    if ($element === '..' || $element === '.') {
                        continue;
                    }

                    // Skip non-part elements.
                    if (in_array($element, self::non_part_elements())) {
                        continue;
                    }

                    if (is_dir(LOCAL_MB2BUILDER_PATH_THEME_ELEMENTS . $element)) {
                        $elements[] = $element;
                    }
                }
            }

            sort($elements );

            return $elements;

        }






        /**
         *
         * Method to get builder settings
         *
         */
        public static function get_builder_settings($opts = []) {
              global $CFG, $USER;
              $output = '';

              // Get elements settings.
              self::get_static_elements_settings();
              self::get_element_settings();

              // Get fields.
              self::get_fields();

              // Include import blocks.
              self::include_import_blocks();

              // Get settings from static elements.
              $sectionconfig = json_decode(base64_decode(LOCAL_MB2BUILDER_SETTINGS_SECTION), true);
              $rowconfig = json_decode(base64_decode(LOCAL_MB2BUILDER_SETTINGS_ROW), true);
              $colconfig = json_decode(base64_decode(LOCAL_MB2BUILDER_SETTINGS_COL), true);

              $output .= '<div class="mb2-pb-settings mb2-pb-settings-section" data-baseurl="' .$CFG->wwwroot. '" data-themeurl="' .
              $CFG->wwwroot . '/' . local_mb2builder_themedir() . '/' . local_mb2builder_get_theme_name() . '" data-sesskey="' .
              $USER->sesskey . '">';
              $output .= '<div class="hidden">';
              $output .= self::languege_strings();
              $output .= '<textarea id="buildertextimport"></textarea>';

              $output .= '<div class="template-settings-section">';
              $output .= self::settings_template($sectionconfig, 'settings-section' );
              $output .= self::settings_template($rowconfig, 'settings-row' );
              $output .= self::settings_template($colconfig, 'settings-column' );
              $output .= self::get_settings_template_elements();
              $output .= '</div>';
              $output .= '<div class="template-elements-section">';
              $output .= format_text('[mb2pb_row template="1"][/mb2pb_row]', FORMAT_HTML);
              $output .= self::get_element_templates();
              $output .= '</div>';
              $output .= '</div>';
              $output .= self::modal_template('import-export', $opts);
              $output .= self::modal_template('settings-section');
              $output .= self::modal_template('settings-row');
              $output .= self::modal_template('settings-column');
              $output .= self::modal_template('settings-element');
              $output .= self::modal_template('settings-subelement');
              $output .= self::modal_template('row-layout');
              $output .= self::modal_template('elements', $opts);
              $output .= self::modal_template('images');
              $output .= self::modal_template('sample-images');
              $output .= self::modal_template('global-images');
              $output .= self::modal_template('file-manager');
              $output .= self::modal_template('globalfile-manager');
              $output .= self::modal_template('font-icons');
              $output .= self::modal_template('backupfile-manager');
              $output .= '</div>';

              return $output;

        }



        /**
         *
         * Method to export page to json file
         *
         */
        public static function get_element_templates() {
            $output = '';

            $elements = self::get_elements();

            foreach ($elements as $e) {
                $output .= format_text('[mb2pb_' . $e . ' template="1" ][/mb2pb_' . $e . ']', FORMAT_HTML);
            }

            return $output;

        }





        /**
         *
         * Method to get images to insert
         *
         */
        public static function get_images_preview($filearea = 'images', $opts = []) {
            global $CFG, $USER;

            require_once($CFG->libdir . '/filelib.php');
            $output = '';

            $fs = get_file_storage();
            $itemid = 0;

            if ($filearea !== 'images') {
                if ($opts['itemid']) {
                    $itemid = $opts['itemid'];
                } else if ($opts['partid']) {
                    $itemid = Mb2builderPartsApi::is_partid($opts['partid']);
                } else if ($opts['footerid']) {
                    $itemid = Mb2builderFootersApi::is_footerid($opts['footerid']);
                } else {
                    $itemid = Mb2builderPagesApi::is_pageid($opts['pageid']);
                }
            }

            $files = $fs->get_area_files(context_system::instance()->id, 'local_mb2builder', $filearea, $itemid);

            foreach ($files as $f) {
                if ($f->get_filename() === '.') {
                    continue;
                }

                $fitemid = $filearea === 'images' ? null : $f->get_itemid();
                $url = moodle_url::make_pluginfile_url($f->get_contextid(), $f->get_component(), $f->get_filearea(), $fitemid,
                $f->get_filepath(), $f->get_filename());

                $output .= '<div class="mb2-pb-image-toinsert">';
                $output .= self::remove_button($f->get_filename(), $filearea, $opts);
                $output .= '<a href="#" class="mb2-pb-insert-image" data-imgurl="' . $url . '" data-imgname="' .
                strtolower($f->get_filename()) . '" data-dismiss="mb2modal">';
                $output .= '<img src="' . $url . '?preview=thumb&oid=' . $f->get_timemodified() . '" alt="' .
                $f->get_filename() . '" />';
                $output .= '<span class="imgdesc">' . $f->get_filename() . '</span>';
                $output .= '</a>';
                $output .= '</div>';
            }

            return $output;

        }





        /**
         *
         * Method to get button to remove image
         *
         */
        public static function remove_button($imgname, $filearea, $opts) {

            $output = '';

            // Don't allow users to remove the 'global' media.
            if ($filearea === 'images' && !has_capability('moodle/course:viewhiddenactivities', context_system::instance())) {
                return;
            }

            $contextid = isset($opts['contextid']) ? $opts['contextid'] : '';

            $output .= '<button type="button" class="imgremove themereset"';
            $output .= ' data-imgname="' . $imgname. '"';
            $output .= ' data-filearea="' . $filearea. '"';
            $output .= ' data-itemid="' . $opts['itemid'] . '"';
            $output .= ' data-contextid="' . $contextid . '"';
            $output .= ' data-pageid="' . $opts['pageid'] . '"';
            $output .= ' data-footerid="' . $opts['footerid'] . '"';
            $output .= ' data-partid="' . $opts['partid'] . '"';
            $output .= '>';
            $output .= '&times;';
            $output .= '</button>';

            return $output;

        }








        /**
         *
         * Method to get sample images to insert
         *
         */
        public static function get_sample_images_preview() {
            $output = '';
            $images = self::get_sample_images_list();

            foreach ($images as $i) {
                $url = self::get_image_path($i['url']);

                $output .= '<div class="mb2-pb-image-toinsert">';
                $output .= '<a href="#" class="mb2-pb-insert-image" data-imgurl="' . $url . '" data-imgname="' .
                strtolower($i['name']) . '" data-dismiss="mb2modal">';
                $output .= '<img src="' . $url . '" alt="' . $i['name'] . '" />';
                $output .= '<span class="imgdesc">' . $i['name'] . '</span>';
                $output .= '</a>';
                $output .= '</div>';
            }

            return $output;

        }







        /**
         *
         * Method to get samle images
         *
         */
        public static function get_sample_images_list() {

            global $CFG;

            $imgdir = $CFG->dirroot . '/local/mb2builder/pix/sample-data/';

            $dirs = scandir($imgdir );
            $images = [];

            if (! is_dir($imgdir )) {
                return $images;
            }

            foreach ($dirs as $i) {
                if ($i === '.' || $i === '..') {
                    continue;
                }

                if (is_dir($imgdir . $i )) {
                    foreach (scandir($imgdir . $i ) as $ii) {
                        if ($ii === '.' || $ii === '..') {
                            continue;
                        }

                        if (is_dir($imgdir . $i . '/' . $ii )) {
                            foreach (scandir($imgdir . $i . '/' . $ii) as $iii) {

                                if ($iii === '.' || $iii === '..') {
                                    continue;
                                }

                                $images[] = ['name' => $iii, 'url' => 'sample-data/' . $i . '/' . $ii . '/' .
                                self::get_basename($iii) ];
                            }
                        }
                    }
                }
            }

            return $images;

        }






        /**
         *
         * Method to remove file extension
         *
         */
        public static function get_basename($filename) {

            // Convert file name into array.
            $filenamearr = explode( '.', $filename );

            // Get last element from the array.
            // We need last element because file name may contains more tha one dot (file.name.jpg).
            $lastitem = end($filenamearr );

            // Remove file extension from the filename.
            $filename = str_replace( '.' . $lastitem, '',  $filename );

            return $filename;

        }






        /**
         *
         * Method to save file in filearea
         *
         */
        public static function delete_file($filename, $opts = []) {

            if (!$filename) {
                return;
            }

            $itemid = 0;
            $filearea = $opts['filearea'];

            if ($filearea !== 'images') {
                // Get new item id.
                if ($opts['itemid']) {
                    $itemid = $opts['itemid'];
                } else if ($opts['partid']) {
                    $itemid = Mb2builderPartsApi::is_partid($opts['partid']);
                } else if ($opts['footerid']) {
                    $itemid = Mb2builderFootersApi::is_footerid($opts['footerid']);
                } else {
                    $itemid = Mb2builderPagesApi::is_pageid($opts['pageid']);
                }
            }

            $fs = get_file_storage();
            $file = $fs->get_file(context_system::instance()->id, 'local_mb2builder', $filearea, $itemid, '/', $filename);

            if ($file) {
                $file->delete();
            }

        }








        /**
         *
         * Method to save file in filearea
         *
         */
        public static function save_file($itemid, $opts = []) {
            global $USER;

            if (!$itemid) {
                return;
            }

            $filearea = 'images';
            $newitemid = 0;

            if ($opts['mediatype'] == 1) {

                // Set filearea name.
                if ($opts['partid']) {
                    $filearea = 'partimages';
                } else if ($opts['footerid']) {
                    $filearea = 'footerimages';
                } else {
                    $filearea = 'pageimages';
                }

                // Now We have to get the itemid.
                if ($opts['itemid']) {
                    $newitemid = $opts['itemid'];
                } else {
                    if ($opts['partid']) {
                        $newitemid = Mb2builderPartsApi::is_partid($opts['partid']);
                    } else if ($opts['footerid']) {
                        $newitemid = Mb2builderFootersApi::is_footerid($opts['footerid']);
                    } else {
                        $newitemid = Mb2builderPagesApi::is_pageid($opts['pageid']);
                    }
                }
            }

            // Get file from draft area.
            $fs = get_file_storage();
            $draftfiles = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $itemid, 'id DESC', false);

            if (!$draftfiles) {
                return;
            }

            $draftfile = reset($draftfiles);

            // Define options for new file record.
            $filerecord = [
                'contextid' => context_system::instance()->id,
                'component' => 'local_mb2builder',
                'filearea' => $filearea,
                'itemid' => $newitemid,
                'filepath' => '/',
                'filename' => $draftfile->get_filename(),
            ];

            $oldfile = $fs->get_file($filerecord['contextid'], $filerecord['component'],  $filerecord['filearea'],
            $filerecord['itemid'], $filerecord['filepath'], $filerecord['filename']);

            // Check if file with the same name key_exists.
            // We don't want delete any files, so we have to change image name.
            if ($oldfile) {
                $newname = explode('.', $draftfile->get_filename());
                $filetype = end($newname);
                $newname = str_replace( '.' . $filetype, '',  $draftfile->get_filename()) . uniqid('_');
                $filerecord['filename'] = $newname . '.' . $filetype;
            }

            $fs->create_file_from_storedfile($filerecord, $draftfile);

            // Delete draft file.
            if ($draftfile) {
                $draftfile->delete();
            }

        }





        /**
         *
         * Method to files from database
         *
         */
        public static function get_db_pages() {

            global $CFG, $DB;
            $results = [];
            $context = \context_system::instance();

            $query = 'SELECT * FROM ' . $CFG->prefix . 'files WHERE component=\'local_mb2builder\' AND contextid=' . $context->id;
            $row = $DB->get_records_sql($query);

            foreach ($row as $el) {
                $results[] = $el->filename;
            }

            return $results;
        }




        /**
         *
         * Method to get inputs
         *
         */
        public static function get_input_items($key, $attr) {
             return call_user_func(['LocalMb2builder' . ucfirst($attr['type']), 'local_mb2builder_get_input'], $key, $attr );
        }






        /**
         *
         * Method to get settings from builder elements
         *
         */
        public static function get_settings_template_elements() {

            $output = '';

            foreach (self::get_elements() as $element) {
                $consfieldsname = 'LOCAL_MB2BUILDER_SETTINGS_' . strtoupper($element);

                $configfields = json_decode(base64_decode(constant($consfieldsname)), true);
                $type = 'settings-element-' . $element;

                $output .= self::settings_template($configfields, $type );

                if (isset($configfields['subelement'])) {
                    $type = 'settings-subelement-' . $element;
                    $output .= self::settings_template($configfields['subelement'], $type );
                }
            }

            return $output;

        }





        /**
         *
         * Method to get import blocks settings
         *
         */
        public static function get_import_block_settings($block, $layout=false) {
            if ($layout) {
                $blocks = self::get_import_layouts();
            } else {
                $blocks = self::get_import_blocks();
            }

            $arrayk = array_search($block, $blocks);

            if (!isset($blocks[$arrayk])) {
                return;
            }

            $blocksettings = $blocks[$arrayk];

            if ($layout) {
                $blocksettings = 'LOCAL_MB2BUILDER_IMPORT_LAYOUTS_' . strtoupper($blocksettings);
            } else {
                $blocksettings = 'LOCAL_MB2BUILDER_IMPORT_BLOCKS_' . strtoupper($blocksettings);
            }

            $blocksettings = json_decode(base64_decode(constant($blocksettings)), true);

            return $blocksettings;

        }





        /**
         *
         * Method to get import blocks settings
         *
         */
        public static function get_import_block_data($part, $id) {

            $path = LOCAL_MB2BUILDER_PATH_THEME_IMPORT_BLOCKS;

            if ($part === 'layouts') {
                $path = LOCAL_MB2BUILDER_PATH_THEME_IMPORT_LAYOUTS;
            }

            $file = $path . 'data/' . $id . '.json';

            if (!file_exists($file)) {
                return;
            }

            return file_get_contents($file);

        }





        /**
         *
         * Method to get settings template
         *
         */
        public static function settings_template($configfields, $type) {

            $output = '';

            $output .= '<div id="tab-' . $type . '" class="mb2-pb-tabs">';
            $output .= '<ul class="mb2-pb-tabs-list d-flex flex-row align-items-center p-0 m-0">';

            $configtabs = $configfields['tabs'];

            foreach ($configtabs as $tab => $tname) {
                $isactive = $tab === 'general' ? ' active' : '';
                $output .= '<li class="mb2-pb-tabs-item' . $isactive . '"><button type="button" class="themereset mb2-pb-tabs-link'.
                $isactive . '" aria-controls="' . $type . '-' . $tab . '">' . $tname . '</button></li>';
            }

            $output .= '</ul>';

            $output .= '<div class="mb2-pb-tabs-content px-4">';

            foreach ($configtabs as $tab => $tname) {

                $isactive = $tab === 'general' ? ' active' : '';
                $output .= '<div id="' . $type . '-' . $tab . '" class="mb2-pb-tabs-pane' . $isactive . '">';

                foreach ($configfields['attr'] as $filename => $attr) {
                    if ($attr['section'] === $tab) {
                        $output .= self::get_input_items($filename, $attr);
                    }
                }

                $output .= '</div>';
            }

            $output .= '</div>';
            $output .= '</div>';

            return $output;

        }




        /**
         *
         * Method to get modal template
         *
         */
        public static function modal_template($type = '', $opts = []) {

            global $CFG;

            $output = '';
            $modalcls2 = '';
            $modalcls = 'mb2-pb-modal';
            $cancelcls = '';
            $bodypx = ' p-4';

            if (preg_match('@settings-@', $type)) {
                $modalcls .= ' mb2-pb-modal-settings';
                $cancelcls = ' mb2-pb-page-cancel';
                $bodypx = '';
            }

            if ($type === 'images' || $type === 'sample-images' || $type === 'global-images' || $type === 'font-icons' ||
            $type === 'elements' || $type === 'import-export') {
                $modalcls2 = ' modal-md';
            }

            if ($type === 'import-export' || $type === 'font-icons') {
                $bodypx = '';
            }

            $imagesdata = $type === 'images' ? ' data-images_baseurl="' . self::set_images_base_url() . '"' : '';

            $modaltitle = 'Modal';

            if ($type === 'row-layout') {
                $modaltitle = get_string('columns', 'local_mb2builder');
            } else if ($type === 'font-icons') {
                $modaltitle = get_string('icons', 'local_mb2builder');
            } else if ($type === 'images') {
                $modaltitle = get_string('customimages', 'local_mb2builder');
            } else if ($type === 'sample-images') {
                $modaltitle = get_string('sampleimages', 'local_mb2builder');
            } else if ($type === 'global-images') {
                $modaltitle = get_string('globalimages', 'local_mb2builder');
            } else if ($type === 'file-manager') {
                $modaltitle = get_string('uploadimages', 'local_mb2builder');
            } else if ($type === 'elements') {
                $modaltitle = get_string('addelement', 'local_mb2builder');
            } else if ($type === 'import-export') {
                $modaltitle = get_string('importexport', 'local_mb2builder');
            }

            $output .= '<div id="mb2-pb-modal-' . $type . '" class="' . $modalcls . '" data-type="' .
            $type . '" role="dialog"' . $imagesdata . '>';
            $output .= '<div class="mb2-pb-modal-dialog' . $modalcls2 . '" role="document">';
            $output .= '<div class="mb2-pb-modal-content">';
            $output .= '<div class="mb2-pb-modal-header">';
            $output .= '<button type="button" class="close" data-dismiss="mb2modal">&times;</button>';
            $output .= '<h4 class="modal-title">' . $modaltitle . '</h4>';
            $output .= '</div>';
            $output .= '<div class="mb2-pb-modal-body position-relative' . $bodypx . '">';
            $output .= $type === 'row-layout' ? self::get_row_layout() : '';
            $output .= $type === 'elements' ? self::get_layout_elements($opts) : '';
            $output .= $type === 'images' ? self::get_images() : '';
            $output .= $type === 'sample-images' ? self::get_sample_images() : '';
            $output .= $type === 'global-images' ? self::get_images('images') : '';
            $output .= $type === 'font-icons' ? '' : ''; // Icons loaded via javascript.
            $output .= $type === 'import-export' ? self::import_export($opts) : '';
            $output .= $type === 'file-manager' ? self::get_file_manager_iframe() : '';
            $output .= $type === 'globalfile-manager' ? self::get_file_manager_iframe(true) : '';
            $output .= $type === 'file-manager' ? '<div class="mb2-pb-overlay"></div>' : '';
            $output .= '</div>';

            if ($type !== 'row-layout' && $type !== 'elements') {
                $output .= '<div class="mb2-pb-modal-footer d-flex flex-row justify-content-between align-items-center px-4 py-2">';

                $savebtn = 1;
                $btnid = 'save-' . $type;
                $canceltext = get_string('cancel');

                if ($type === 'file-manager' || $type === 'globalfile-manager') {
                    $btnid = 'applay-' . $type;
                    $canceltext = get_string('close', 'local_mb2builder');
                }

                $output .= '<button type="button" class="btn btn-sm btn-secondary' . $cancelcls . '" data-dismiss="mb2modal">' .
                $canceltext . '</button>';

                if ($type === 'images') {
                    $savebtn = 0;
                    $output .= '<div>';
                    $output .= self::media_buttons('global');
                    $output .= self::media_buttons('sample');
                    $output .= self::media_buttons('upload-item');
                    $output .= '</div>';
                }

                if ($type === 'sample-images') {
                    $savebtn = 0;
                    $output .= '<div>';
                    $output .= self::media_buttons('itemimages');
                    $output .= self::media_buttons('global');
                    $output .= '</div>';
                }

                if ($type === 'global-images') {
                    $savebtn = 0;
                    $output .= '<div>';
                    $output .= self::media_buttons('itemimages');
                    $output .= self::media_buttons('sample');
                    $output .= self::media_buttons('upload-global');
                    $output .= '</div>';
                }

                if ($type === 'font-icons') {
                    $savebtn = 0;
                }

                if ($type === 'import-export') {
                    $savebtn = 0;
                    $canceltext = get_string('close', 'local_mb2builder');
                }

                $output .= $savebtn ? '<button type="button" id="' . $btnid . '" class="btn btn-sm btn-success mb2-pb-page-apply">'.
                get_string('save', 'admin') . '</button>' : '';
                $output .= '</div>';
            }

            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            return $output;

        }



        /**
         *
         * Method to set media buttons
         *
         */
        public static function media_buttons($type = '') {

            $viewhidden = has_capability('moodle/course:viewhiddenactivities', context_system::instance());

            switch ($type) {
                case 'sample':
                    return '<button class="mb2-pb-upload-images btn btn-info btn-sm" data-toggle="mb2modal"
                    data-target="#mb2-pb-modal-sample-images" data-dismiss="mb2modal">' .
                    get_string('sampleimages', 'local_mb2builder') . '</button>';
                    break;
                case 'global':
                    return '<button class="mb2-pb-upload-images btn btn-info btn-sm" data-toggle="mb2modal"
                    data-target="#mb2-pb-modal-global-images" data-dismiss="mb2modal">' .
                    get_string('globalimages', 'local_mb2builder') . '</button>';
                    break;
                case 'itemimages':
                    return '<button class="btn btn-info btn-sm" data-toggle="mb2modal"
                    data-target="#mb2-pb-modal-images" data-dismiss="mb2modal">' .
                    get_string('customimages', 'local_mb2builder') . '</button>';
                    break;
                case 'upload-global':
                    return $viewhidden ?
                    '<button class="mb2-pb-upload-images btn btn-success btn-sm" data-item="0" data-toggle="mb2modal"
                    data-target="#mb2-pb-modal-globalfile-manager">' . get_string('uploadimages', 'local_mb2builder') .
                    '</button>'
                    : '';
                    break;
                case 'upload-item':
                    return '<button class="mb2-pb-upload-images btn btn-success btn-sm" data-item="1" data-toggle="mb2modal"
                    data-target="#mb2-pb-modal-file-manager">' . get_string('uploadimages', 'local_mb2builder') . '</button>';
                    break;
            }

        }







        /**
         *
         * Method to set builder base urls
         *
         */
        public static function set_images_base_url() {
            global $CFG;
            $context = \context_system::instance();

            if ($CFG->slasharguments) {
                return new moodle_url('/pluginfile.php/' . $context->id . '/local_mb2builder/images/');
            } else {
                return new moodle_url('/pluginfile.php', ['file' => '/' . $context->id . '/local_mb2builder/images/']);
            }

        }



        /**
         *
         * Method to set builder base urls
         *
         */
        public static function get_icons_arr($path) {

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
         * Method to get language stirngs in js files
         *
         */
        public static function languege_strings() {

            $output = '';

            $output .= '<span id="mb2-pb-lang"';
            $output .= ' data-addrow="' . get_string('addrow', 'local_mb2builder') . '"';
            $output .= ' data-remove="' . get_string('remove', 'local_mb2builder') . '"';
            $output .= ' data-settings="' . get_string('settings', 'local_mb2builder') . '"';
            $output .= ' data-section="' . get_string('section', 'local_mb2builder') . '"';
            $output .= ' data-row="' . get_string('row', 'local_mb2builder') . '"';
            $output .= ' data-duplicate="' . get_string('duplicate', 'local_mb2builder') . '"';
            $output .= ' data-create="' . get_string('create', 'local_mb2builder') . '"';
            $output .= ' data-col="' . get_string('column', 'local_mb2builder') . '"';
            $output .= ' data-columns="' . get_string('columns', 'local_mb2builder') . '"';
            $output .= ' data-addelement="' . get_string('addelement', 'local_mb2builder') . '"';
            $output .= ' data-element="' . get_string('element', 'local_mb2builder') . '"';
            $output .= ' data-copy="' . get_string('copy', 'local_mb2builder') . '"';
            $output .= ' data-confirmdeletebackup="' . get_string('confirmdeletebackupjs', 'local_mb2builder') . '"';
            $output .= ' data-confirmreplacecontent="' . get_string('confirmreplacecontent', 'local_mb2builder') . '"';
            $output .= ' data-item="' . get_string('item', 'local_mb2builder') . '"';
            $output .= ' data-move="' . get_string('move', 'local_mb2builder') . '"';
            $output .= ' data-importtextempty="' . get_string('importtextempty', 'local_mb2builder') . '"';
            $output .= ' data-importtextnotvalidjson="' . get_string('importtextnotvalidjson', 'local_mb2builder') . '"';
            $output .= ' data-importsuccess="' . get_string('importsuccess', 'local_mb2builder') . '"';
            $output .= ' data-cannotremove="' . get_string('cannotremove', 'local_mb2builder') . '"';
            $output .= ' data-cantopenmodal="' . get_string('cantopenmodal', 'local_mb2builder') . '"';
            $output .= ' data-requirefield="' . get_string('requirefield', 'local_mb2builder') . '"';
            $output .= ' data-processing="' . get_string('processing', 'local_mb2builder') . '"';
            $output .= ' data-savelayoutbtn="' . get_string('savelayoutbtn', 'local_mb2builder') . '"';
            $output .= ' data-layoutcreated="' . get_string('layoutcreated', 'local_mb2builder') . '"';
            $output .= ' data-importlayoutbtn="' . get_string('importlayoutbtn', 'local_mb2builder') . '"';
            $output .= ' data-selectlayout="' . get_string('selectlayout', 'local_mb2builder') . '"';
            $output .= ' data-shortcodereplaced="' . get_string('shortcodereplaced', 'local_mb2builder') . '"';
            $output .= ' data-htmlerror="' . get_string('htmlerror', 'local_mb2builder') . '"';
            $output .= ' data-uploadfile="' . get_string('uploadfile', 'local_mb2builder') . '"';
            $output .= '></span>';

            return $output;

        }


        /**
         *
         * Method to set layout columns
         *
         */
        public static function get_row_layout() {

            $output = '';

            $layoutarr = [
                '12',
                '6,6',
                '4,4,4',
                '3,3,3,3',
                '3,6,3',
                '3,3,6',
                '6,3,3',
                '9,3',
                '3,9',
                '8,4',
                '4,8',
                '7,5',
                '5,7',
                '10,2',
                '2,10',
            ];

            $output .= '<div class="mb2-pb-row-variants">';

            foreach ($layoutarr as $l) {
                $output .= '<a href="#" class="mb2-pb-row-variant row-' . str_replace(',', '', $l) . '" data-row_variant="' .
                $l . '" title="' . str_replace(',', '-', $l) . '">';

                $elarr = explode(',', $l);
                foreach ($elarr as $e) {
                    $output .= '<span class="rowel-' . $e . '">' . $e . '</span>';
                }

                $output .= '</a>';
            }

            $output .= '</div>';

            return $output;

        }





        /**
         *
         * Method to get elements settings
         *
         */
        public static function get_layout_elements($options = []) {

            $output = '';
            $elements = self::get_elements();

            $output .= '<div class="mb2-pb-elements">';

            foreach ($elements as $element) {
                $consfieldsname = 'LOCAL_MB2BUILDER_SETTINGS_' . strtoupper($element);
                $configfields = json_decode(base64_decode(constant($consfieldsname)), true);
                $subel = isset($configfields['subelement']) ? 1 : 0;

                // Do not show footer elements on normal page builder.
                if (isset($configfields['footer']) && !$options['footer']) {
                    continue;
                }

                $output .= '<a href="#" class="mb2-pb-modal-el ' . $configfields['id'] . '" data-id="' . $configfields['id'] .
                '" data-subelement="' . $subel . '" data-subelement_name="' . $configfields['subid'] . '" data-dismiss="mb2modal">';
                $output .= '<i class="' . $configfields['icon'] . '"></i>';
                $output .= '<span>' . $configfields['title'] . '</span>';
                $output .= '</a>';
            }

            $output .= '</div>';

            return $output;

        }




        /**
         *
         * Method to get images
         *
         */
        public static function get_images($filearea = '') {
            global $CFG;
            $output = '';
            $itemid = optional_param('itemid', '', PARAM_INT);
            $footer = optional_param('footer', '', PARAM_INT);
            $part = optional_param('part', '', PARAM_INT);

            $pageid = optional_param('pageid', '', PARAM_TEXT);
            $footerid = optional_param('footerid', '', PARAM_TEXT);
            $partid = optional_param('partid', '', PARAM_TEXT);
            $ajaxurl = new moodle_url('/local/mb2builder/ajax/image-delete.php');

            if ($filearea) {
                $filearea = $filearea;
            } else if ($part) {
                $filearea = 'partimages';
            } else if ($footer) {
                $filearea = 'footerimages';
            } else {
                $filearea = 'pageimages';
            }

            $opts = ['itemid' => $itemid, 'partid' => $partid, 'footerid' => $footerid, 'pageid' => $pageid];

            $output .= '<div class="icons-search"><input type="text" class="mb2-pb-search-image" placeholder="' .
            get_string( 'searchimagefor', 'local_mb2builder' ) . '" /></div>';
            // Content loaded via javascript.
            $output .= '<div class="mb2-pb-images" data-url="' . $ajaxurl . '" data-filearea="' . $filearea . '"></div>';
            $output .= '<div class="mb2-pb-overlay"></div>';

            return $output;

        }





        /**
         *
         * Method to get sample images
         *
         */
        public static function get_sample_images() {

            $output = '';

            $output .= '<div class="icons-search"><input type="text" class="mb2-pb-search-image" placeholder="' .
            get_string( 'searchimagefor', 'local_mb2builder' ) . '" /></div>';
            $output .= '<div class="mb2-pb-images">';
            $output .= self::get_sample_images_preview();
            $output .= '</div>';

            return $output;

        }



        /**
         *
         * Method to get file manager iframe
         *
         */
        public static function get_file_manager_iframe($globalmedia = false) {

            global $CFG;
            $output = '';

            require_once($CFG->dirroot . '/local/mb2builder/form-media.php');
            $ajaxurl = new moodle_url('/local/mb2builder/ajax/image-upload.php');

            $output .= '<div class="mb2-pb-uploadmedia-wrap" data-url="' . $ajaxurl . '">';
            if ($globalmedia) {
                $mform = new globalmedia_edit_form('index.php');
            } else {
                $mform = new media_edit_form('index.php');
            }
            $output .= $mform->render();
            $output .= '</div>';

            return $output;

        }



        /**
         *
         * Method to to get font icons
         *
         */
        public static function get_icons() {

            if (!file_exists(LOCAL_MB2BUILDER_PATH_THEME . '/lib.php')) {
                return [];
            }

            require_once(LOCAL_MB2BUILDER_PATH_THEME . '/lib.php');

            if (!function_exists('theme_mb2nl_get_icons4plugins')) {
                return [];
            }

            $iconsarr = [];

            // Get cache.
            $cache = cache::make('local_mb2builder', 'builder');
            $cacheid = 'fonticons';

            if ($cache->get($cacheid)) {
                return $cache->get($cacheid);
            }

            $themeicons = theme_mb2nl_get_icons4plugins();

            foreach ($themeicons as $iconskey => $icons) {
                $csspath = LOCAL_MB2BUILDER_PATH_THEME_ASSETS . '/' . $icons['folder'] . '/' . $icons['css'] . '.css';
                if (!file_exists($csspath)) {
                    continue;
                }

                $iconsarr[$iconskey]['tabid'] = $icons['tabid'];
                $iconsarr[$iconskey]['name'] = $icons['name'];

                $iconsfont = self::get_icons_arr($csspath);
                foreach ($iconsfont as $iconkey => $icon) {
                    $iconsarr[$iconskey]['icons'][] = $icons['prefhtml'] . $iconkey;
                }
            }

            $cache->set($cacheid, $iconsarr);
            return $iconsarr;

        }


        /**
         *
         * Method to get fon icons
         *
         */
        public static function font_icons() {

            $themeicons = self::get_icons();

            if (!count($themeicons)) {
                return;
            }

            $output = '';
            $i = 0;
            $x = 0;

            $output .= '<div id="tab-font-icons" class="mb2-pb-tabs">';
            $output .= '<ul class="mb2-pb-tabs-list d-flex flex-row align-items-center p-0 m-0">';

            foreach ($themeicons as $icons) {
                $i++;
                $activecls = $i == 1 ? ' active' : '';
                $output .= '<li class="mb2-pb-tabs-item' .$activecls. '"><button type="button" class="themereset mb2-pb-tabs-link' .
                $activecls . '" aria-controls="' . $icons['tabid'] . '">' . $icons['name'] . '</button></li>';
            }

            $output .= '</ul>';
            $output .= '<div class="mb2-pb-tabs-content px-4">';

            foreach ($themeicons as $icons) {
                $x++;
                $active = $x == 1 ? ' active' : '';

                $output .= '<div id="' . $icons['tabid'] . '" class="mb2-pb-tabs-pane' . $active . '">';
                $output .= '<div class="icons-search"><input type="text" class="mb2-pb-search-icon" placeholder="' .
                get_string('searchiconfor', 'local_mb2builder') . '" /></div>';
                $output .= '<div class="mb2-pb-icons d-flex flex-wrap">';

                foreach ($icons['icons'] as $icon) {
                    $output .= '<button type="button" class="mb2-pb-choose-icon themereset justify-content-center';
                    $output .= ' align-items-center" data-iconname="' . $icon . '" title="' . $icon . '" data-dismiss="mb2modal">';
                    $output .= '<i class="' . $icon. '"></i>';
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
         * Method to import/export page parts
         *
         */
        public static function import_export($opts = []) {

            $itemid = optional_param('itemid', 0, PARAM_INT);
            $partid = optional_param('partid', '', PARAM_RAW);
            $footerid = optional_param('footerid', '', PARAM_RAW);
            $pageid = optional_param('pageid', '', PARAM_RAW);

            $output = '';
            $config = get_config('local_mb2builder');
            $blockactive = $opts['footer'] || $opts['part'] ? ' active' : '';
            $navblockactive = $opts['footer'] || $opts['part'] ? ' active' : '';

            $output .= '<div id="tab-import-export" class="mb2-pb-tabs">';
            $output .= '<ul class="mb2-pb-tabs-list d-flex flex-row align-items-center p-0 m-0">';

            if (!$opts['footer'] && !$opts['part']) {
                $output .= '<li class="mb2-pb-tabs-item active"><button type="button" class="themereset mb2-pb-tabs-link active"';
                $output .= ' aria-controls="tab-importtemplates">' .
                get_string('layouts', 'local_mb2builder') . '</button></li>';
            }

            $output .= '<li class="mb2-pb-tabs-item"><button type="button" class="themereset mb2-pb-tabs-link' . $navblockactive .'"
            aria-controls="tab-importrows">' . get_string('importrows', 'local_mb2builder').'</button></li>';

            if ($config->customlayouts) {
                $output .= '<li class="mb2-pb-tabs-item"><button type="button" class="themereset mb2-pb-tabs-link"
                aria-controls="tab-import">' . get_string('import', 'local_mb2builder') . '</button></li>';
                $output .= '<li class="mb2-pb-tabs-item"><button type="button" class="themereset mb2-pb-tabs-link"
                aria-controls="tab-export">' . get_string('export', 'local_mb2builder') . '</button></li>';
            }

            $output .= '<li class="mb2-pb-tabs-item"><button type="button" class="themereset mb2-pb-tabs-link"
            aria-controls="tab-backup">'. get_string('backups', 'local_mb2builder') . '</button></li>';
            $output .= '</ul>';

            $output .= '<div class="mb2-pb-tabs-content px-4">';

            if (!$opts['footer'] && !$opts['part']) {
                $output .= '<div id="tab-importtemplates" class="mb2-pb-tabs-pane active">';
                $output .= self::get_import_blocks_list(true);
                $output .= '</div>';
            }

            $output .= '<div id="tab-importrows" class="mb2-pb-tabs-pane' . $blockactive . '">';
            $output .= self::get_import_blocks_list();
            $output .= '</div>';

            if ($config->customlayouts) {
                $output .= '<div id="tab-import" class="mb2-pb-tabs-pane">';
                $output .= self::get_latout_form();
                $output .= '</div>';

                $output .= '<div id="tab-export" class="mb2-pb-tabs-pane">';
                $output .= self::get_export_form();
                $output .= '</div>';
            }

            $output .= '<div id="tab-backup" class="mb2-pb-tabs-pane">';

            if ($partid) {
                $type = 3;
            } else if ($footerid) {
                $type = 2;
            } else {
                $type = 1;
            }

            $output .= Mb2builderBackup::backup_form($type, $itemid, 0);
            $output .= Mb2builderBackup::backup_list($type, $itemid);
            $output .= Mb2builderBackup::restore_form($type, $itemid);

            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            return $output;

        }






        /**
         *
         * Method to get layout export form
         *
         */
        public static function get_export_form() {

            global $CFG, $USER;
            $output = '';
            $layouts = Mb2builderLayoutsApi::get_list_records();
            $ajaxurl = new moodle_url('/local/mb2builder/ajax/save-layout.php');

            $output .= '<form id="mb2-pb-form-savelayout" class="mb2-pb-tabsform" action="" method="" data-url="' . $ajaxurl . '">';
            // We always needs to create new layout record.
            // Se we have to set empty 'itemid' field.
            $output .= '<input type="hidden" name="itemid" id="savelayoutitemid" value="" />';
            $output .= '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            $output .= '<textarea name="content" id="savelayoutcontent" class="hidden"></textarea>';
            $output .= '<div class="formfield mb-2">';
            $output .= '<div class="update-layout">';
            $output .= '<label for="editlayoutid"> ' . get_string( 'overridelayout', 'local_mb2builder' ) . '</label>';
            $output .= '<select name="layoutid" id="editlayoutid" class="mb2-pb-layout-list">';
            $output .= '<option value="0">' . get_string( 'none', 'local_mb2builder' ) . '</option>';

            if (count($layouts)) {
                foreach ($layouts as $layout) {
                    $output .= '<option value="' . $layout->id . '">' . $layout->name . '</option>';
                }
            }

            $output .= '</select>';
            $output .= '</div>';
            $output .= '<div style="position:relative;">';
            $output .= '<input type="text" name="name" id="savelayoutname" value="" placeholder="' .
            get_string( 'savelayoutplh', 'local_mb2builder' ) . '" />';
            $output .= '<span class="mb2-pb-error">' . get_string( 'requirefield', 'local_mb2builder' ) . '</span>';
            $output .= '</div>';
            $output .= '</div>';

            $output .= '<div class="formfield button-formel mb-2">';
            $output .= '<input class="btn btn-success" type="submit" value="' .
            get_string( 'savelayoutbtn', 'local_mb2builder' ) . '" />';
            $output .= '</div>';

            $output .= '<div class="mb2-pb-success">' . get_string( 'layoutcreated', 'local_mb2builder' ) . '!</div>';

            $output .= '</form>';

            return $output;

        }





        /**
         *
         * Method to layouts list
         *
         */
        public static function get_latout_form() {

            global $CFG, $USER;

            $output = '';
            $layouts = Mb2builderLayoutsApi::get_list_records();

            $output = '';
            $ajaxurl = new moodle_url('/local/mb2builder/ajax/import-layout-custom.php');

            $output .= '<form id="mb2-pb-form-importlayout" class="mb2-pb-tabsform" action="" method="" data-url="' .$ajaxurl. '">';
            $output .= '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            $output .= '<div class="formfield field-formel">';
            $output .= '<div class="form-filed mb-2">';
            $output .= '<input type="checkbox" id="layoutkeep" name="importlayoutkeep" value="0">';
            $output .= '<label for="layoutkeep"> ' . get_string( 'importkeep', 'local_mb2builder' ) . '</label>';
            $output .= '</div>';

            $output .= '<div class="form-filed mb-2">';
            $output .= '<select name="layoutid" id="importlayoutid" class="mb2-pb-layout-list">';
            $output .= '<option value="0">' . get_string( 'selectlayout', 'local_mb2builder' ) . '</option>';

            if (count($layouts)) {
                foreach ($layouts as $layout) {
                    $output .= '<option value="' . $layout->id . '">' . $layout->name . '</option>';
                }
            }

            $output .= '</select>';
            $output .= '<span class="mb2-pb-error">' . get_string( 'requirefield', 'local_mb2builder' ) . '</span>';
            $output .= '</div>';

            $output .= '</div>';

            $output .= '<div class="formfield button-formel mb-2"><input class="btn btn-success" type="submit" value="' .
            get_string( 'importlayoutbtn', 'local_mb2builder' ) . '" /></div>';

            $output .= '<div class="mb2-pb-success">' . get_string( 'layoutimported', 'local_mb2builder' ) . '!</div>';
            $output .= '</form>';

            return $output;

        }





        /**
         *
         * Method to get import blocks list
         *
         */
        public static function get_import_blocks_list($layout = false) {
            $output = '';

            // Check if is footer builder.
            // We need it to set the 'isfooter' column's attribute.
            $fbuilder = optional_param('footer', 0, PARAM_INT);

            if ($layout) {
                $blocks = self::get_import_layouts();
                $part = 'layouts';
            } else {
                $blocks = self::get_import_blocks();
                $part = 'blocks';
            }

            $output .= '<div class="mb2-pb-import-select">';
            $output .= '<label for="mb2pb_select_block_' . $part . '">' . get_string( 'category', 'local_mb2builder' ) .
            '</label> <select name="mb2pb_select_block_' . $part . '" id="mb2pb_select_block_' . $part . '">';
            $output .= '<option value="">' . get_string( 'all', 'local_mb2builder' ) . '</option>';

            foreach ($blocks as $block) {
                if (!$fbuilder && $block === 'footer') {
                    continue;
                }

                $output .= '<option value="' . $block . '">' . get_string($block, 'local_mb2builder' ) . '</option>';
            }

            $output .= '</select>';
            $output .= '</div>';

            $output .= '<div class="mb2-pb-import-blocks-wrap">';
            $output .= '<div class="mb2-pb-import-blocks">';

            foreach ($blocks as $block) {
                $blocksettings = self::get_import_block_settings($block, $layout);

                foreach ($blocksettings['items'] as $k => $item) {
                    if (!$fbuilder && $blocksettings['id'] === 'footer') {
                        continue;
                    }

                    $output .= '<div class="block-item" data-category="' . $blocksettings['id'] . '">';
                    $output .= '<div class="block-item-inner">';
                    $output .= '<img src="' . self::get_import_thumbs($part, $item['thumb']) . '" alt="' . $item['name'] . '" />';
                    $output .= '<a class="mb2-pb-import-part" href="#" data-fbuilder="' . $fbuilder . '" data-part="' .
                    $part . '" data-partid="' . $item['id'] . '" data-dismiss="mb2modal" title="' .
                    get_string('import', 'local_mb2builder') . '">';
                    $output .= '<i class="fa fa-upload"></i>';
                    $output .= '</a>';
                    $output .= '</div>';
                    $output .= '</div>';
                }
            }

            $output .= '</div>';
            $output .= '</div>';

            return $output;

        }






        /**
         *
         * Method to get blocks images
         *
         */
        public static function get_import_thumbs($type = 'blocks', $image = '') {

            global $CFG, $OUTPUT;
            $theme = self::get_theme_name();
            $path = 'import/' . $theme . '/' . $type . '/' . $image;

            return self::get_image_path($path );

        }




        /**
         *
         * Method to get theme name
         *
         */
        public static function get_theme_name() {

            global $CFG;

            if (isset( get_config('local_mb2builder')->theme ) && get_config('local_mb2builder')->theme) {
                return get_config('local_mb2builder')->theme;
            }

            return $CFG->theme;

        }





        /**
         *
         * Method to get plugin image path
         *
         */
        public static function get_image_path($path) {

            global $OUTPUT;

            if (!$path) {
                return;
            }

            return $OUTPUT->image_url($path, 'local_mb2builder');

        }





        /**
         *
         * Method to get dummy image
         *
         */
        public static function get_dummy_image($value) {

            if (!preg_match('@mb2dummyimg@', $value)) {
                return $value;
            }

            // Get image attribute
            // mb2dummyimg:593x65/1F2C51/fff.jpg.
            $imgattr = explode(':', $value);

            return get_string('mb2dummyimg', 'local_mb2builder', $imgattr[1]);

        }




        /**
         *
         * Method to get sample image
         *
         */
        public static function get_sample_image($value) {

            $imagename = '';

            // Always replace the 'mb2sampledata' in the URLs.
            if (!preg_match('@mb2sampledata@', $value)) {
                return $value;
            }

            // The image data in elemnt settings is the following:
            // 'mb2sampledata:/year/month/imagename'.
            $imagename = explode(':', $value);
            $imagename = $imagename[1];
            // Get sample data image.
            $path = 'sample-data/' . $imagename;

            return self::get_image_path($path);

        }


        /**
         *
         * Method to get content options
         *
         */
        public static function get_options($options, $urloptions) {
            $opts = [];

            foreach ($options as $k => $v) {
                $v = $v;

                if (isset($urloptions[$k])) {
                    $v = $urloptions[$k];
                }

                $opts[$k] = $v;
            }

            return $opts;

        }



    }


}
