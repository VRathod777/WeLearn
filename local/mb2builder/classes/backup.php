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

if (!class_exists('Mb2builderBackup')) {

    /**
     * Backups API
     */
    class Mb2builderBackup {

        /**
         *
         * Method to set backup record.
         * $type: 1 = PAGE, 2 = FOOTER, 3 = PART.
         *
         */
        public static function backup($type = 1, $itemid = null, $content = '', $name = '', $auto = 1) {

            global $CFG, $DB;

            // Check if temp dir is writable.
            if (!is_readable($CFG->tempdir)) {
                return '$CFG->tempdir is not writable, admin has to fix directory permissions!';
            }

            if ($content) {
                // Set the final backup content to save it.
                // In this content all URLs were changed to replace them with the new URL's during the restore process.
                $content = self::file_content_2_save($type, $itemid, $content);

                // Check if a backup with the same content exists.
                $sqlparams = ['type' => $type, 'itemid' => $itemid, 'contenthash' => sha1($content)];
                $backupssql = 'SELECT * FROM {local_mb2builder_backups} WHERE type=:type AND itemid=:itemid';
                $backupssql .= ' AND contenthash=:contenthash';

                if ($DB->record_exists_sql($backupssql, $sqlparams)) {
                    $record = $DB->get_record_sql($backupssql, $sqlparams);
                    return self::message_html('error', get_string('backupexists', 'local_mb2builder',
                    userdate($record->timecreated, '%Y-%m-%d %H:%M:%S') . ' ' . $record->name));
                }
            } else {
                return;
            }

            $filearea = self::bakup_file_area($type);

            // Make sure the temp backup directory exists.
            if (!is_dir(LOCAL_MB2BUILDER_TMP_DIR)) {
                make_writable_directory(LOCAL_MB2BUILDER_TMP_DIR);
            }

            // Set backup directory and file name.
            $filename = self::bakup_file_name($type, $itemid, $name);
            $zippath = LOCAL_MB2BUILDER_TMP_DIR . DIRECTORY_SEPARATOR . $filename . '.zip';

            // Get mediafiles and content of the item (page, footer or part) to backup it.
            $filelist = self::get_files2backup($type, $itemid, $content);

            // Create backup ZIP file and move it in the temp directory.
            $fp = get_file_packer('application/zip');
            $fp->archive_to_pathname($filelist, $zippath, false);

            // Save backup ZIP file.
            $fileinfo = [
                'contextid' => context_system::instance()->id,
                'component' => 'local_mb2builder',
                'filearea' => $filearea,
                'itemid' => $itemid,
                'filepath' => '/',
                'filename' => $filename . '.zip',
                'mimetype' => 'application/zip',
            ];

            $fs = get_file_storage();
            $file = $fs->create_file_from_pathname($fileinfo, $zippath);

            // Update backup files. If there are more auto-created backups than the limit, remove the oldest one.
            $backups = self::update_backup_records($type, $itemid, $file, $name, $auto, $content);

            // Delete JSON content file and old backup zip files.
            $files = $fs->get_area_files($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
            $fileinfo['itemid']);

            foreach ($files as $f) {
                if ($f->get_mimetype() === 'application/json') {
                    // Delete JSON file.
                    $f->delete();
                } else if ($f->get_mimetype() === 'text/plain') {
                    // Delete txt file.
                    $f->delete();
                } else if ($f->get_mimetype() === 'application/zip' && !in_array($f->get_filename(), $backups)) {
                    // Delete old backup files.
                    $f->delete();
                }
            }

            // Delete content of the temp dir.
            remove_dir(LOCAL_MB2BUILDER_TMP_DIR, true);
            return self::message_html('success', get_string('backupcreated', 'local_mb2builder'));

        }






        /**
         *
         * Method to delete backup.
         * @param string The backup file name without extension.
         *
         */
        public static function delete_backup($id, $type, $itemid, $createdby, $filename) {

            global $DB, $USER;

            // Check capability.
            // User always can delete own backup.
            if (has_capability('local/mb2builder:managebackups', context_system::instance()) || $USER->id == $createdby) {

                // Delete backup database record.
                $DB->delete_records('local_mb2builder_backups', ['id' => $id]);

                // Delete backup file.
                $filearea = self::bakup_file_area($type);
                $fs = get_file_storage();
                $files = $fs->get_area_files(context_system::instance()->id, 'local_mb2builder', $filearea, $itemid);

                foreach ($files as $f) {
                    if ($f->get_filename() === $filename) {
                        $f->delete();
                        return true;
                    }
                }

            }

            return false;

        }




        /**
         *
         * Method to set backup record.
         *
         */
        public static function restore_backup($opts) {
            global $USER;

            $file = null;
            $tmpdir = LOCAL_MB2BUILDER_TMP_DIR . DIRECTORY_SEPARATOR;
            $content = '';

            // Make sure the temp backup directory exists.
            if (!is_dir(LOCAL_MB2BUILDER_TMP_DIR)) {
                make_writable_directory(LOCAL_MB2BUILDER_TMP_DIR);
            }

            // This is the case when the user uploads the backup file.
            if ($opts['dirname']) {
                $unpackdir = $tmpdir . $opts['dirname'];
                $backupvalid = true; // Backup was validated in the externallib.
            } else {
                // This is the case when the user restores the backup from the stored file.
                // Unpack backup file in the temp directory.
                $fp = get_file_packer('application/zip');
                $unpackdir = $tmpdir . time();
                $file = self::get_backup_file($opts['type'], $opts['itemid'], $opts['filename']);
                $zipcontents = $fp->extract_to_pathname($file, $unpackdir);

                if (!$zipcontents) {
                    throw new moodle_exception("Error Unzipping file", 1);
                }

                // We have to validate the backup file.
                $backupvalid = self::validate_files($opts['type'], $unpackdir);
            }

            // Validate files.
            if ($backupvalid) {
                // The backup file is valid.
                // Restore the item content from the unziped directory.
                $content = self::get_backup_content($opts['type'], $opts['itemid'], $unpackdir);

                // Restore media files form the unziped directory.
                self::restore_media($opts['type'], $opts['itemid'], $unpackdir);
            } else {
                $content = get_string('backupcorrupted', 'local_mb2builder');
            }

            // Remove the content of the temp dir.
            remove_dir(LOCAL_MB2BUILDER_TMP_DIR, true);

            return $content;

        }


        /**
         *
         * Method to get item demo content to campare it with the import content.
         *
         */
        public static function item_democontent_hash($type, $itemid) {

            global $DB;

            if ($type == 3) {
                $table = 'local_mb2builder_parts';
            } else if ($type == 2) {
                $table = 'local_mb2builder_footers';
            } else {
                $table = 'local_mb2builder_pages';
            }

            $sqlparams = ['id' => $itemid];
            $backupssql = 'SELECT id, democontent FROM {' . $table . '} WHERE id=:id';
            $democontent = $DB->get_record_sql($backupssql, $sqlparams)->democontent;

            return sha1($democontent);

        }




        /**
         *
         * Method to get files to backup.
         *
         */
        public static function get_files2backup($type, $itemid, $content, $sha = false) {

            $filelist = [];
            $shalist = [];

            if ($type == 3) {
                $filearea = 'partimages';
            } else if ($type == 2) {
                $filearea = 'footerimages';
            } else {
                $filearea = 'pageimages';
            }

            $sysctxid = context_system::instance()->id;
            $fs = get_file_storage();

            // Get files from the item file area.
            $files = $fs->get_area_files($sysctxid, 'local_mb2builder', $filearea, $itemid);
            foreach ($files as $f) {
                if ($f->is_directory()) {
                    continue;
                }

                $file = $fs->get_file($f->get_contextid(), $f->get_component(), $f->get_filearea(), $f->get_itemid(),
                $f->get_filepath(), $f->get_filename());

                if ($file) {
                    $filelist[$f->get_filename()] = $file;
                    $shalist[] = $f->get_contenthash();
                }
            }

            // Get files from global file area.
            $globalfiles = $fs->get_area_files($sysctxid, 'local_mb2builder', 'images', 0);
            foreach ($globalfiles as $f) {
                if ($f->is_directory()) {
                    continue;
                }

                $file = $fs->get_file($f->get_contextid(), $f->get_component(), $f->get_filearea(), $f->get_itemid(),
                $f->get_filepath(), $f->get_filename());

                // Get image from the global file area only if is contained in the content.
                // We have to use the 'rawurlencode' to get correct file name, the same as in the URL.
                $getglobal = preg_match('@' . rawurlencode($f->get_filename()) . '@', $content);
                if ($getglobal) {
                    $filelist[$f->get_filename()] = $file;
                    $shalist[] = $f->get_contenthash();
                    $getglobal = 0;
                }
            }

            // Set content backup file.
            if ($contentfile = self::create_content_file($type, $itemid, $content)) {
                $filelist[$contentfile->get_filename()] = $contentfile;
                $shalist[] = $contentfile->get_contenthash();
            }

            // Create text file.
            $txtfilecontent = implode(PHP_EOL, $shalist);
            $fileinfo = [
                'contextid' => context_system::instance()->id,
                'component' => 'local_mb2builder',
                'filearea' => self::bakup_file_area($type),
                'itemid' => $itemid,
                'filepath' => '/',
                'filename' => self::contenthash($type, $txtfilecontent) . '.txt',
                'mimetype' => 'text/plain',
            ];

            // Check if the text file exists, if yes, remove it and then create the new one.
            $oldfile = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'],
            $fileinfo['filepath'], $fileinfo['filename']);

            if ($oldfile) {
                $oldfile->delete();
            }

            $txtfile = $fs->create_file_from_string($fileinfo, $txtfilecontent);

            if ($txtfile) {
                $filelist[$txtfile->get_filename()] = $txtfile;
            }

            if ($sha) {
                return $shalist;
            } else {
                return $filelist;
            }

        }




        /**
         *
         * Method to restore media from backup file.
         *
         */
        public static function restore_media($type, $itemid, $unpackdir) {

            $allmedia = [];

            // Get all media items from backup unpacked directory.
            if (is_dir($unpackdir)) {
                $allmedia = scandir($unpackdir);

                foreach ($allmedia as $k => $file) {
                    if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) === 'json' ||
                    pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                        unset($allmedia[$k]);
                    }
                }
            }

            if (!count($allmedia)) {
                return;
            }

            // Remove existing media with the same name before restore media from the backup file.
            if ($type == 3) {
                $filearea = 'partimages';
            } else if ($type == 2) {
                $filearea = 'footerimages';
            } else {
                $filearea = 'pageimages';
            }

            $fs = get_file_storage();
            $files = $fs->get_area_files(context_system::instance()->id, 'local_mb2builder', $filearea, $itemid);

            // If file exists we have to delete it before restore previous version.
            foreach ($files as $f) {
                if (in_array($f->get_filename(), $allmedia)) {
                    $f->delete();
                }
            }

            // Now create the new media files from the backup file.
            foreach ($allmedia as $newfile) {
                $extension = strtolower(pathinfo($newfile, PATHINFO_EXTENSION));

                if (!in_array($extension, self::media_allowed())) {
                    continue;
                }

                $filerecord = [
                    'contextid' => context_system::instance()->id,
                    'component' => 'local_mb2builder',
                    'filearea' => $filearea,
                    'itemid' => $itemid,
                    'filepath' => '/',
                    'filename' => $newfile,
                ];

                $filepath = $unpackdir . DIRECTORY_SEPARATOR . $newfile;

                if (file_exists($filepath)) {
                    $fs->create_file_from_pathname($filerecord, $filepath);
                }
            }

            return true;

        }



        /**
         *
         * Method to update backup records.
         *
         */
        public static function update_backup_records($type, $itemid, $file, $name, $auto, $content) {

            global $DB, $USER;

            // Set a limit on the number of automatically created backups.
            $backups = [];
            $i = 0;
            $limit = get_config('local_mb2builder')->backupnum;
            $fs = get_file_storage();

            // Now we have to add new backup record.
            // Set backup record data.
            $data = new stdClass();
            $data->name = $name ? $name : '';
            $data->type = $type;
            $data->itemid = $itemid;
            $data->auto = $auto;
            $data->filename = $file->get_filename();
            $data->filesize = $file->get_filesize();
            $data->createdby = $auto ? 0 : $USER->id;
            // We need to make sure the 'timecreated' field is the same as the backup file's 'timecreated' field.
            $data->timecreated = $file->get_timecreated();
            $data->contenthash = sha1($content);

            // Add new record.
            $DB->insert_record('local_mb2builder_backups', $data);

            // Now we need to update backups.
            $sqlparams = ['type' => $type, 'itemid' => $itemid];
            $backupssql = 'SELECT * FROM {local_mb2builder_backups} WHERE type=:type AND itemid=:itemid ORDER BY timecreated DESC';
            $records = $DB->get_records_sql($backupssql, $sqlparams);

            if (!count($records)) {
                return [];
            }

            foreach ($records as $backup) {
                // Check for the backup limit.
                if ($backup->auto) {
                    $i++;
                    if ($i > $limit) {
                        // Delete the backup record.
                        $DB->delete_records('local_mb2builder_backups', ['id' => $backup->id]);
                    } else {
                        $backups[] = $backup->filename;
                    }
                } else {
                    $backups[] = $backup->filename;
                }
            }

            return $backups;

        }




        /**
         *
         * Method to set backup file name.
         *
         */
        public static function bakup_file_name($type, $itemid, $name) {

            switch ($type) {
                case 3:
                    $istype = 'part';
                    break;
                case 2:
                    $istype = 'footer';
                    break;
                default:
                    $istype = 'page';
            }

            $backupname = '';

            if ($name) {
                $invalidchars = [
                    '$',
                    '%',
                    '#',
                    '<',
                    '>',
                    '|',
                    '/',
                    '~',
                ];

                $backupname = '_' . $name;
                $backupname = str_replace($invalidchars, '', $backupname);
                $backupname = preg_replace('/\s+/', '-', $backupname);
                $backupname = str_replace(' ', '-', $backupname);
            }

            $name = 'Backup-' . $istype . '-' . $itemid . '_' . userdate(time(), '%Y-%m-%d_%H%M%S') . $backupname;

            return $name;
        }





        /**
         *
         * Method to set backup file name.
         *
         */
        public static function bakup_file_area($type) {

            switch ($type) {
                case 3:
                    return 'backuppart';
                    break;
                case 2:
                    return 'backupfooter';
                    break;
                default:
                    return 'backuppage';
            }

        }



        /**
         *
         * Method to set item content to save in the backup file.
         *
         */
        public static function file_content_2_save($type, $itemid, $content) {

            // Change media URLs.
            // During the restore process we have to set the new URLs of the media files.
            $strtoreplaceitem = self::media_url_2replace($type, $itemid);
            $strtoreplaceglobal = self::media_url_2replace($type, 0);
            $content = str_replace($strtoreplaceitem, 'backupmediaurl/', $content);
            $content = str_replace($strtoreplaceglobal, 'backupmediaurl/', $content);

            // Replace the sampledata images.
            $content = self::media_samledata_replace($content);

            return $content;

        }




        /**
         *
         * Method to set backup record.
         *
         */
        public static function create_content_file($type, $itemid, $content) {

            $filearea = self::bakup_file_area($type);
            $fs = get_file_storage();
            $fileinfo = [
                'contextid' => context_system::instance()->id,
                'component' => 'local_mb2builder',
                'filearea' => $filearea,
                'itemid' => $itemid,
                'filepath' => '/',
                'filename' => 'content.json',
                'mimetype' => 'application/json',
            ];

            $oldfile = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'],
            $fileinfo['filepath'], $fileinfo['filename']);

            // Delete the old file if exist.
            if ($oldfile) {
                $oldfile->delete();
            }

            // Create a new file containing the backup content.
            return $fs->create_file_from_string($fileinfo, $content);

        }


        /**
         *
         * Method to set backup record.
         *
         */
        public static function get_backup_file($type, $itemid, $filename, $url = false) {

            $filearea = self::bakup_file_area($type);
            $fs = get_file_storage();
            $files = $fs->get_area_files(context_system::instance()->id, 'local_mb2builder', $filearea, $itemid);

            foreach ($files as $f) {
                if ($f->get_filename() === $filename) {
                    if ($url) {
                        return moodle_url::make_pluginfile_url($f->get_contextid(), $f->get_component(), $f->get_filearea(),
                        $f->get_itemid(), $f->get_filepath(), $f->get_filename());
                    } else {
                        return $fs->get_file($f->get_contextid(), $f->get_component(), $f->get_filearea(), $f->get_itemid(),
                        $f->get_filepath(), $f->get_filename());
                    }
                }
            }

        }




        /**
         *
         * Method to get backup files to show in the page builder backend.
         *
         */
        public static function backup_files($type, $itemid) {

            global $DB;

            $sqlparams = ['type' => $type, 'itemid' => $itemid];
            $backupssql = 'SELECT * FROM {local_mb2builder_backups} WHERE type=:type AND itemid=:itemid';
            $backupssql .= ' ORDER BY timecreated DESC';
            return $DB->get_records_sql($backupssql, $sqlparams);

        }



        /**
         *
         * Method to set backup record.
         *
         */
        public static function restore_form($type, $itemid) {

            require_once(__DIR__ . '/../form-backup.php');
            $mform = new backup_edit_form('index.php');

            $output = '';

            $output .= '<div class="restore-backup mb2-pb-tabsform mb-5">';
            $output .= '<h5 class="mb-2">' . get_string('restorefromzip', 'local_mb2builder') . '</h5>';
            $output .= '<div class="mb2-pb-uploadbackup-wrap">';
            $mform = new backup_edit_form('index.php');
            $output .= $mform->render();
            $output .= '</div>';
            $output .= '<div class="form-filed"><button type="button" class="mb2-pb-upload-backup mb2-pb-btn typeprimary">' .
            get_string('uploadfile', 'local_mb2builder') . '</button></div>';

            $output .= '<div class="mb2-pb-uploadbackup-restore">';
            $output .= '<div class="file-info"></div>';
            $output .= '<button type="button" class="mb2-pb-restore-backup mb2-pb-btn typeprimary mt-3 d-none"';
            $output .= ' data-part="layouts" data-type="' . $type . '"';
            $output .= ' data-itemid="' . $itemid . '"';
            $output .= ' data-dismiss="mb2modal"';
            $output .= '>';
            $output .= get_string('restore');
            $output .= '</button> ';
            $output .= '</div>';
            $output .= '</div>';

            return $output;

        }



        /**
         *
         * Method to set backup record.
         *
         */
        public static function backup_form($type, $itemid, $auto) {

            $output = '';

            $itemid = optional_param('itemid', '', PARAM_INT);
            $partid = optional_param('partid', '', PARAM_TEXT);
            $footerid = optional_param('footerid', '', PARAM_TEXT);
            $pageid = optional_param('pageid', '', PARAM_TEXT);

            $output .= '<div class="new-backup-form mb2-pb-tabsform mb-5">';
            $output .= '<form action="index.php" method="post" id="' . uniqid('backup_') . '">';
            $output .= '<div class="hidden">';
            $output .= '<input type="hidden" name="sesskey" value="' . sesskey(). '">';
            $output .= '<input type="hidden" name="itemid" value="' . $itemid . '">';
            $output .= '<input type="hidden" name="partid" value="' . $partid . '">';
            $output .= '<input type="hidden" name="footerid" value="' . $footerid . '">';
            $output .= '<input type="hidden" name="pageid" value="' . $pageid . '">';
            $output .= '<textarea name="backup_content"></textarea>';
            $output .= '</div>';
            $output .= '<div class="formfield field-formel">';
            $output .= '<h5 class="mb-2">' . get_string('createnewbackup', 'local_mb2builder') . '</h5>';
            $output .= '<div class="form-label mb-2"><label for="backup_name" class="mb-0">' .
            get_string('backupname', 'local_mb2builder') . '</label></div>';
            $output .= '<div class="form-filed mb-2"><input type="text" id="backup_name" name="backup_name" value=""></div>';
            $output .= '</div>';
            $output .= '</form>';
            $output .= '<div class="form-filed"><button type="button" class="new-backup mb2-pb-btn typeprimary" value="' .
            get_string('create', 'local_mb2builder') . '">' . get_string('create', 'local_mb2builder') . '</button></div>';
            $output .= '<div class="form-message"></div>';
            $output .= '</div>';

            return $output;

        }


        /**
         *
         * Method to set backup record.
         *
         */
        public static function backup_list($type, $itemid) {

            $output = '';

            $output .= '<div class="backup-table position-relative hidden">';
            $output .= '<table class="flexible table mb-5">';
            $output .= '<thead>';
            $output .= '<th>' . get_string('backups', 'local_mb2builder') . '</th>';
            $output .= '<th>' . get_string('type', 'local_mb2builder') . '</th>';
            $output .= '<th>' . get_string('filesize', 'local_mb2builder') . '</th>';
            $output .= '<th class="align-right">' . get_string('actions') . '</th>';
            $output .= '</thead>';
            $output .= '<tbody class="mb2-pb-backup-list" data-type="' . $type . '" data-itemid="' . $itemid . '"></tbody>';
            $output .= '</table>';
            $output .= '<div class="mb2-pb-actions-loading"></div>';
            $output .= '</div>';

            return $output;

        }



        /**
         *
         * Method to set backup record.
         *
         */
        public static function backup_list_items($type, $itemid) {

            global $USER;
            $output = '';
            $filearea = self::bakup_file_area($type);
            $items = self::backup_files($type, $itemid);
            $kb = 1024;
            $mb = ($kb * 1024);

            foreach ($items as $k => $item) {
                $istype = $item->auto ? get_string('automatic', 'local_mb2builder') : get_string('manual', 'local_mb2builder');

                $output .= '<tr>';
                $output .= '<td class="align-middle"><div class="d-flex align-items-center" style="column-gap:.45rem;">
                <div class="text-nowrap">' . userdate($item->timecreated, '%Y-%m-%d %H:%M:%S') . '</div> <div>' . $item->name .
                '</div></div></td>';
                $output .= '<td class="text-nowrap align-middle">' . $istype . '</td>';
                $output .= '<td class="text-nowrap align-middle">' . round($item->filesize / $mb, 1) . ' MB</td>';
                $output .= '<td class="align-right align-middle">';
                $output .= '<button type="button" class="mb2-pb-restore-backup themereset text-nowrap p-0 mr-2"';
                $output .= ' data-part="layouts" data-filename="' . $item->filename . '" data-type="' . $item->type . '"';
                $output .= ' data-itemid="' . $item->itemid . '"';
                $output .= ' data-dismiss="mb2modal"';
                $output .= '>';
                $output .= '<span class="btn-icon"><i class="bi bi-arrow-repeat"></i></span> ';
                $output .= '<span class="btn-intext">' . get_string('restore') . '</span>';
                $output .= '</button> ';

                $downloadurl = moodle_url::make_pluginfile_url(context_system::instance()->id, 'local_mb2builder', $filearea,
                $item->itemid, '/', $item->filename, true);
                $output .= '<a class="text-nowrap" href="' . $downloadurl . '">';
                $output .= '<span class="btn-icon"><i class="bi bi-download"></i></span> ';
                $output .= '<span class="btn-intext">' . get_string('download') . '</span>';
                $output .= '</a> ';

                if (has_capability('local/mb2builder:managebackups', context_system::instance()) || $USER->id == $createdby) {
                    $output .= '<button type="button" class="mb2-pb-delete-backup themereset text-nowrap p-0 ml-2"';
                    $output .= ' data-id="' . $item->id . '"';
                    $output .= '>';
                    $output .= '<span class="btn-icon"><i class="bi bi-trash"></i></span> ';
                    $output .= '<span class="btn-intext">' . get_string('delete') . '</span>';
                    $output .= '</button>';
                }

                $output .= '</td>';
                $output .= '</tr>';
            }

            return $output;

        }




        /**
         *
         * Method to validate files before retsore.
         *
         */
        public static function validate_files($type, $unpackdir) {

            $dirpath = $unpackdir . DIRECTORY_SEPARATOR;
            $hashes = [];
            $scandir = scandir($unpackdir);

            if (!in_array('content.json', $scandir)) {
                return;
            }

            // Get files hashses.
            foreach ($scandir as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                    $filecontent = file_get_contents($dirpath . $file);

                    // Validate the text file name.
                    // This also check if user restore correct type of content -
                    // page to page, footer to footer and part to part.
                    if (explode('.', $file)[0] === sha1($type . $filecontent)) {
                        $hashes = explode(PHP_EOL, $filecontent);
                    } else {
                        return;
                    }
                }
            }

            // Check if there is text file and files hashes array.
            if (empty($hashes)) {
                return;
            }

            // Check files.
            foreach ($scandir as $file) {
                if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                    continue;
                }

                if (!in_array(sha1_file(($dirpath . $file)), $hashes)) {
                    return;
                }
            }

            return true;

        }


        /**
         *
         * Method to get backup content.
         *
         */
        public static function get_backup_content($type, $itemid, $unpackdir) {

            global $OUTPUT;
            $iscontent = '';
            $dirpath = $unpackdir . DIRECTORY_SEPARATOR;

            // Get json file from backup directory.
            foreach (scandir($unpackdir) as $file) {
                if ($file === 'content.json') {
                    $content = file_get_contents($dirpath . $file);
                    $iscontent = $content;
                }
            }

            if ($iscontent) {
                // Now we have to replace all the 'backupmediaurl/' parts with the new URLs.
                $replaceto = self::media_url_2replace($type, $itemid);
                $iscontent = str_replace('backupmediaurl/', $replaceto, $iscontent);

                // Replace the 'mb2sampledata:' to the real URL,
                // e.g.: mb2sampledata:2023/01/logo_small.
                $sampledatadir = $OUTPUT->image_url('sample-data', 'local_mb2builder') . '/';
                $iscontent = str_replace('mb2sampledata:', $sampledatadir, $iscontent);

                return $iscontent;
            }

            return;

        }



        /**
         *
         * Method to set sample data url to replace.
         *
         */
        public static function media_samledata_replace($content, $replacement = 'mb2sampledata:') {

            global $CFG, $OUTPUT;

            // Prepare the pattern.
            // Get the sampledata directory URL '/theme/image.php/mb2nl/local_mb2builder/0123456789/sample-data'
            // and change it to an array.
            $sampleurl = explode('/', $OUTPUT->image_url('sample-data', 'local_mb2builder'));
            $count = count($sampleurl);
            $sampleurl = array_slice($sampleurl, ($count - 6), 6);

            // Now we need to replace the number to the '(\d+)'.
            $sampleurl[4] = '(\d+)';
            $pattern = '@' . $CFG->wwwroot . '/' . implode('/', $sampleurl) . '@';

            return preg_replace($pattern, $replacement, $content);

        }



        /**
         *
         * Method to set part of media url to replace.
         *
         */
        public static function media_url_2replace($type, $itemid) {
            global $CFG, $OUTPUT;

            $ctxid = context_system::instance()->id;

            if ($type == 3) {
                $filearea = 'partimages';
            } else if ($type == 2) {
                $filearea = 'footerimages';
            } else {
                $filearea = 'pageimages';
            }

            if (!$itemid) {
                // Replace global media URLs.
                return $CFG->wwwroot . '/pluginfile.php/' . $ctxid . '/local_mb2builder/images/';
            } else {
                // Replace item media URLs.
                return $CFG->wwwroot . '/pluginfile.php/' . $ctxid . '/local_mb2builder/' . $filearea . '/' . $itemid . '/';
            }

        }




        /**
         *
         * Method to set text file name based on type and content.
         *
         */
        public static function contenthash($type, $content) {

            if (!$content) {
                return;
            }

            // We don't want to allow importing page content into the footer and footer content into the page.
            $str = $type;
            $str .= $content;

            return sha1($str);

        }




        /**
         *
         * Method to set allowed media extensions.
         *
         */
        public static function media_allowed() {

            return [
                // Images.
                'apng',
                'avif',
                'gif',
                'jpg',
                'jpeg',
                'jfif',
                'pjpeg',
                'pjp',
                'png',
                'svg',
                'webp',
                // Videos.
                'webm',
                'mpg',
                'mp2',
                'mpeg',
                'mpe',
                'mpv',
                'ogg',
                'mp4',
                'm4p',
                'm4v',
                'avi',
                'wmv',
                'mov',
                'qt',
                'avchd',
            ];

        }




        /**
         *
         * Method to set message html content
         *
         */
        public static function message_html($type, $message) {

            $output = '<div class="mb2-pb-admin-message message-' . $type . ' mt-3">';
            $output .= $message;
            $output .= '</div>';

            return $output;

        }




        /**
         *
         * Method to get a list of all services.
         *
         */
        public static function get_list_records($limitfrom = 0, $limitnum = 0, $count = false, $fields = '*', $sort = '',
        $dir = '') {
            global $DB;

            $params = [];
            $itemtypeid = optional_param('itemtypeid', '', PARAM_RAW);
            $type = $itemtypeid ? explode('.', $itemtypeid)[0] : '';
            $itemid = $itemtypeid ? explode('.', $itemtypeid)[1] : '';

            if ($type && $itemid) {
                $params['type'] = $type;
                $params['itemid'] = $itemid;
            }

            if ($count) {
                return $DB->count_records('local_mb2builder_backups', $params);
            }

            $issort = $sort ? $sort . ' ' . $dir : 'id';
            $records = $DB->get_records('local_mb2builder_backups', $params, $issort, $fields, $limitfrom, $limitnum);

            return $records;

        }



        /**
         *
         * Method to get table sort headers
         *
         */
        public static function get_table_header($columns, $tosort = []) {
            global $OUTPUT;

            $headers = [];
            $sort = optional_param('sort', 'timecreated', PARAM_ALPHANUMEXT);
            $dir = optional_param('dir', 'DESC', PARAM_ALPHA);
            $page = optional_param('page', 0, PARAM_INT);
            $itemtypeid = optional_param('itemtypeid', '', PARAM_RAW);
            $perpage = 20;

            foreach ($columns as $k => $column) {
                $str = $k;

                if ($k === 'auto') {
                    $str = 'type';
                } else if ($k === 'timecreated') {
                    $str = 'backup';
                }

                if (in_array($k, $tosort)) {
                    $isdir = $dir == 'DESC' ? 'ASC' : 'DESC';
                    $url = new moodle_url('/local/mb2builder/backups.php', ['itemtypeid' => $itemtypeid, 'sort' => $k, 'dir' =>
                    $isdir, 'page' => $page, 'perpage' => $perpage]);
                    $iconname = $isdir === 'ASC' ? 'sort_desc' : 'sort_asc';
                    $colicon = $OUTPUT->pix_icon('t/' . $iconname, get_string(strtolower($isdir)), 'core', ['class' => 'iconsort']);

                    $headers[] = '<a href="' . $url . '">' . get_string($str, 'local_mb2builder') .  $colicon . '</a>';
                } else {
                    $component = $k === 'actions' ? '' : 'local_mb2builder';
                    $headers[] = get_string($str, $component);
                }
            }

            return $headers;
        }


        /**
         *
         * Method to get user
         *
         */
        public static function get_user($id) {
            global $DB;

            if (!$id) {
                return;
            }

            return $DB->get_record('user', ['id' => $id]);
        }




        /**
         *
         * Method to get part filter
         *
         */
        public static function filter() {

            global $DB, $OUTPUT, $USER;
            $output = '';
            $opts = [];

            $itemtypeid = optional_param('itemtypeid', '', PARAM_RAW);
            $cache = cache::make('local_mb2builder', 'builder');
            $cachebackups = 'backups_' . $USER->id;

            $opts[] = get_string('all');

            if (!$cache->get($cachebackups)) {

                $pages = $DB->get_records_sql('SELECT id, title FROM {local_mb2builder_pages}');
                $pageopts = [];
                if (count($pages)) {
                    foreach ($pages as $page) {
                        $pageopts[1 . '.' . $page->id] = $page->title;
                    }
                }

                if (!empty($pageopts)) {
                    $opts['pages'] = [get_string('managepages', 'local_mb2builder') => $pageopts];
                }

                $footers = $DB->get_records_sql('SELECT id, name FROM {local_mb2builder_footers}');
                $footeropts = [];
                if (count($footers)) {
                    foreach ($footers as $footer) {
                        $footeropts[2 . '.' . $footer->id] = $footer->name;
                    }
                }

                if (!empty($footeropts)) {
                    $opts['footers'] = [get_string('footers', 'local_mb2builder') => $footeropts];
                }

                $parts = $DB->get_records_sql('SELECT id, name FROM {local_mb2builder_parts}');
                $partopts = [];
                if (count($parts)) {
                    foreach ($parts as $part) {
                        $partopts[3 . '.' . $part->id] = $part->name;
                    }
                }

                if (!empty($partopts)) {
                    $opts['parts'] = [get_string('parts', 'local_mb2builder') => $partopts];
                }

                $cache->set($cachebackups, $opts);
            }

            $singleselect = new single_select(new moodle_url('/local/mb2builder/backups.php'), 'itemtypeid',
            $cache->get($cachebackups), $itemtypeid, get_string('chooseitem', 'local_mb2builder'));
            $singleselect->set_label(get_string('chooseitem', 'local_mb2builder'), ['class' => 'sr-only']);

            $output .= '<div class="partfilter mb-3">';
            $output .= $OUTPUT->render($singleselect);
            $output .= '</div>';

            return $output;

        }




        /**
         *
         * Method to get part filter
         *
         */
        public static function get_draft_file($draftfiles) {

            foreach ($draftfiles as $f) {
                if ($f->get_mimetype() === 'application/zip') {
                    return $f;
                }
            }

            return;
        }



    }


}
