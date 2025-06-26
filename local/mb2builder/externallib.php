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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/classes/api.php');

/**
 * External theme API
 */
class local_mb2builder_external extends external_api {


    /**
     *
     * Method to get a tabs of all icons.
     *
     */
    public static function get_theme_icons() {

        require_login();

        $params = self::validate_parameters(self::get_theme_icons_parameters(), []);

        $results = [
            'icons' => mb2builderApi::font_icons(),
        ];

        return $results;

    }





    /**
     *
     * Method to get images preview.
     *
     */
    public static function get_images_preview($filearea, $itemid, $pageid, $footerid, $partid) {

        require_login();

        $params = self::validate_parameters(self::get_images_preview_parameters(), [
            'filearea' => $filearea,
            'itemid' => $itemid,
            'pageid' => $pageid,
            'footerid' => $footerid,
            'partid' => $partid,
        ]);

        $results = [
            'images' => mb2builderApi::get_images_preview($params['filearea'], $params),
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
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_images_preview_parameters() {
        return new external_function_parameters(
            [
                'filearea' => new external_value(PARAM_RAW, 'File area name.'),
                'itemid' => new external_value(PARAM_INT, 'Item ID.'),
                'pageid' => new external_value(PARAM_RAW, 'Page unique ID.'),
                'footerid' => new external_value(PARAM_RAW, 'Footer unique ID.'),
                'partid' => new external_value(PARAM_RAW, 'Part unique ID.'),
            ]
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


    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_images_preview_returns() {
        return new external_single_structure(
            [
                'images' => new external_value(PARAM_RAW, 'List of the images.'),
                'warnings' => new external_warnings(),
            ]
        );
    }


    /**
     *
     * Method to get images preview.
     *
     */
    public static function get_backup_list($type, $itemid) {

        require_login();

        $params = self::validate_parameters(self::get_backup_list_parameters(), [
            'type' => $type,
            'itemid' => $itemid,
        ]);

        $results = [
            'backupitems' => Mb2builderBackup::backup_list_items($params['type'], $params['itemid']),
        ];

        return $results;

    }




    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_backup_list_returns() {
        return new external_single_structure(
            [
                'backupitems' => new external_value(PARAM_RAW, 'The list of backup items.'),
                'warnings' => new external_warnings(),
            ]
        );
    }




    /**
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function get_backup_list_parameters() {
        return new external_function_parameters(
            [
                'type' => new external_value(PARAM_INT, 'Type of backup item (page, footer or part).'),
                'itemid' => new external_value(PARAM_INT, 'Item ID.'),
            ]
        );
    }



    /**
     *
     * Method to get images preview.
     *
     */
    public static function create_backup($formdata) {

        global $PAGE;

        $PAGE->set_context(context_system::instance());
        require_login();

        $params = self::validate_parameters(self::create_backup_parameters(), [
            'formdata' => $formdata,
        ]);

        // Convert serialized form string to an array.
        $data = [];
        parse_str($params['formdata'], $data);

        $data = [
            'itemid' => $data['itemid'],
            'partid' => $data['partid'],
            'footerid' => $data['footerid'],
            'pageid' => $data['pageid'],
            'name' => $data['backup_name'],
            'content' => $data['backup_content'],
        ];

        if ($data['partid']) {
            $type = 3;
        } else if ($data['footerid']) {
            $type = 2;
        } else {
            $type = 1;
        }

        if ($data['itemid']) {
            $itemid = $data['itemid'];
        } else {
            if ($data['partid']) {
                $itemid = Mb2builderPartsApi::is_partid($data['partid']);
            } else if ($data['footerid']) {
                $itemid = Mb2builderFootersApi::is_footerid($data['footerid']);
            } else {
                $itemid = Mb2builderPagesApi::is_pageid($data['pageid']);
            }
        }

        $backup = Mb2builderBackup::backup($type, $itemid, $data['content'], $data['name'], 0);

        $results = [
            'backup' => $backup,
        ];

        return $results;

    }



    /**
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function create_backup_parameters() {
        return new external_function_parameters(
            [
                'formdata' => new external_value(PARAM_RAW, 'The data from the backup form, encoded as a json array.'),
            ]
        );
    }



    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function create_backup_returns() {
        return new external_single_structure(
            [
                'backup' => new external_value(PARAM_RAW, 'Whether the backup was successfully created.'),
                'warnings' => new external_warnings(),
            ]
        );
    }



    /**
     *
     * Method to get images preview.
     *
     */
    public static function delete_backup($id) {

        global $DB, $PAGE;

        $PAGE->set_context(context_system::instance());
        require_login();

        $params = self::validate_parameters(self::delete_backup_parameters(), [
            'id' => $id,
        ]);

        // Get the backup record to delete.
        $backup = $DB->get_record('local_mb2builder_backups', ['id' => $params['id']], '*', MUST_EXIST);

        $deleted = Mb2builderBackup::delete_backup($params['id'], $backup->type, $backup->itemid, $backup->createdby,
        $backup->filename);

        $results = [
            'deleted' => $deleted,
        ];

        return $results;

    }



    /**
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function delete_backup_parameters() {
        return new external_function_parameters(
            [
                'id' => new external_value(PARAM_INT, 'The backup ID.'),
            ]
        );
    }



    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function delete_backup_returns() {
        return new external_single_structure(
            [
                'deleted' => new external_value(PARAM_BOOL, 'Whether the backup was successfully deleted.'),
                'warnings' => new external_warnings(),
            ]
        );
    }






    /**
     *
     * Method to get images preview.
     *
     */
    public static function upload_backup($formdata) {

        global $PAGE, $USER;

        $PAGE->set_context(context_system::instance());
        require_login();
        $isvalid = false;

        $params = self::validate_parameters(self::upload_backup_parameters(), [
            'formdata' => $formdata,
        ]);

        $data = [];
        parse_str($params['formdata'], $data);

        // Get uploaded file.
        $fs = get_file_storage();
        $draftfiles = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $data['backupfileid'],
        'timecreated DESC', $USER->id);
        $draftfile = Mb2builderBackup::get_draft_file($draftfiles);

        // Unzip uploaded file to check it.
        $fp = get_file_packer('application/zip');
        $dirname = time();
        $unpackdir = LOCAL_MB2BUILDER_TMP_DIR . DIRECTORY_SEPARATOR . $dirname;
        $zipcontents = $fp->extract_to_pathname($draftfile, $unpackdir);

        if (!$zipcontents) {
            throw new moodle_exception("Error Unzipping file", 1);
        }

        if ($data['partid']) {
            $type = 3;
        } else if ($data['footerid']) {
            $type = 2;
        } else {
            $type = 1;
        }

        if (Mb2builderBackup::validate_files($type, $unpackdir)) {
            $message = '<div class="mb2-pb-admin-message message-success mt-3">' . get_string('backupvalid', 'local_mb2builder',
            $draftfile->get_filename()) . '</div>';
            $isvalid = true;
        } else {
            $message = '<div class="mb2-pb-admin-message message-error mt-3">' . get_string('backupcorrupted', 'local_mb2builder',
            $draftfile->get_filename()) . '</div>';

            // Remove content of the temp dir.
            remove_dir(LOCAL_MB2BUILDER_TMP_DIR, true);
        }

        // Now we can remove draftfiles.
        foreach ($draftfiles as $file) {
            $file->delete();
        }

        $results = [
            'backupfileid' => $data['backupfileid'],
            'message' => $message,
            'dirname' => $dirname,
            'isvalid' => $isvalid,
        ];

        return $results;

    }



    /**
     * Describes the parameters for submit_grading_form webservice.
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function upload_backup_parameters() {
        return new external_function_parameters(
            [
                'formdata' => new external_value(PARAM_RAW, 'The backup file form data.'),
            ]
        );
    }



    /**
     * Describes the return for submit_grading_form
     * @return external_function_parameters
     * @since  Moodle 3.1
     */
    public static function upload_backup_returns() {
        return new external_single_structure(
            [
                'backupfileid' => new external_value(PARAM_INT, 'Uploaded file itemid'),
                'message' => new external_value(PARAM_RAW, 'Message about the backup file'),
                'dirname' => new external_value(PARAM_INT, 'Unziped directory'),
                'isvalid' => new external_value(PARAM_BOOL, 'Whether the backup file is valid or not'),
                'warnings' => new external_warnings(),
            ]
        );
    }


}
