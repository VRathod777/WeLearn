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
 * @package    local_mb2builder
 * @copyright  2018 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__ . '/backup.php');

if (!class_exists('Mb2builderPartsApi')) {

    /**
     *
     * Part API
     *
     */
    class Mb2builderPartsApi {

        /**
         *
         * Method to get a list of all services.
         *
         */
        public static function get_list_records($limitfrom = 0, $limitnum = 0, $count = false, $fields = '*', $sort = '',
        $dir = '') {
            global $DB, $USER;

            $contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);
            $params = ['contextid' => $contextid];

            if ($count) {
                return $DB->count_records('local_mb2builder_parts', $params);
            }

            $issort = $sort ? $sort . ' ' . $dir : 'id';
            $records = $DB->get_records('local_mb2builder_parts', $params, $issort, $fields, $limitfrom, $limitnum);

            return $records;

        }




        /**
         *
         * Method to get sindle record.
         *
         */
        public static function get_record($itemid = 0, $partid = false) {
            global $DB;

            if ($partid) {
                return $DB->get_record_sql('SELECT * FROM {local_mb2builder_parts} WHERE partid=?', [$itemid]);
            }

            return $DB->get_record('local_mb2builder_parts', ['id' => $itemid], '*', MUST_EXIST);

        }



        /**
         *
         * Method to save part during editing
         *
         */
        public static function get_form_democontent($opts = []) {
            global $CFG, $USER;
            $output = '';
            $ajaxurl = new moodle_url('/local/mb2builder/ajax/save-part.php');

            $output .= '<form id="mb2-pb-form-democontent" action="" method="" data-url="' . $ajaxurl . '">';
            $output .= '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            $output .= '<input type="hidden" name="itemid" id="democontentitemid" value="' . $opts['itemid'] . '" />';
            $output .= '<input type="hidden" name="partid" id="democontentpartid" value="' . $opts['partid'] . '" />';
            $output .= '<textarea name="democontent" id="democontent"></textarea>';
            $output .= '<input type="submit" value="Submit">';
            $output .= '</form>';

            return $output;

        }



        /**
         *
         * Method to check if part with specific partid exists
         *
         */
        public static function is_partid($id) {
            global $DB;

            if (!$id) {
                return 0;
            }

            if (!is_int($id)) { // We have to search by partid.
                $sql = 'SELECT id FROM {local_mb2builder_parts} WHERE ' . $DB->sql_like('partid', '?');
            } else { // We have to search by id.
                $sql = 'SELECT id FROM {local_mb2builder_parts} WHERE id=?';
            }

            if ($DB->record_exists_sql($sql, [$id])) {
                return $DB->get_record_sql($sql, [$id])->id;
            }

            return 0;

        }




        /**
         *
         * Method to check if part with specific partid exists
         *
         */
        public static function can_edit_part($id) {

            $context = context_system::instance();
            $viewhidden = has_capability('local/mb2builder:manageparts', context_system::instance());
            $isid = self::is_partid($id);

            if (self::is_my_part($isid) || $viewhidden) {
                return true;
            }

            return false;

        }






        /**
         *
         * Method to check if user is a part creator
         *
         */
        public static function is_my_part($partid = null) {

            global $DB, $USER;

            $params = [
                'id' => $partid,
                'createdby' => $USER->id,
            ];

            $sql = 'SELECT id FROM {local_mb2builder_parts} WHERE id=:id AND createdby=:createdby';

            if ($DB->record_exists_sql($sql, $params)) {
                return true;
            }

            return false;

        }





        /**
         *
         * Method to add new record.
         *
         */
        public static function add_record($data, $setcache = false) {
            global $DB;

            if (!$data) {
                $data = new stdClass();
            }

            $data->id = $DB->insert_record('local_mb2builder_parts', $data);

            self::update_record_data($data, $setcache);
        }





        /**
         *
         * Method to update the record in the database.
         *
         */
        public static function update_record_data($data, $setcache = false, $backup = false) {
            global $DB;

            // Update existing item.
            $DB->update_record('local_mb2builder_parts', $data);

            // Check if auto backup is enabled.
            if ($backup && get_config('local_mb2builder')->autobackup) {
                Mb2builderBackup::backup(3, $data->id, $data->content, '', 1);
            }

            if ($setcache) {
                // Update cache.
                $cache = cache::make('local_mb2builder', 'partdata');
                $data->democontent = ''; // We don't need demo content in the cache file.
                $cache->set($data->id, $data);
            }
        }



        /**
         *
         * Method to check if user can delete item.
         *
         */
        public static function can_delete($item = null) {
            global $USER;

            $contextid = optional_param('contextid', context_system::instance()->id, PARAM_INT);
            $context = context::instance_by_id($contextid, MUST_EXIST);

            // User has the system permission and can delete all content parts.
            if (has_capability('local/mb2builder:manageparts', context_system::instance())) {
                return true;
            } else if (has_capability('local/mb2builder:manageparts', $context) && $item->createdby == $USER->id) {
                // User hasn't the system permission, so he/she can delete only own part.
                return true;
            }

            return false;
        }




        /**
         *
         * Method to delete item.
         *
         */
        public static function delete($itemid, $setcache = true) {
            global $DB;

            $record = self::get_record($itemid);

            if (!self::can_delete($record)) {
                return;
            }

            if ($setcache) {
                // Update cache.
                $cache = cache::make('local_mb2builder', 'partdata');
                $cache->delete($itemid);
            }

            $DB->delete_records('local_mb2builder_parts', ['id' => $itemid]);

        }





        /**
         *
         * Method to set form data.
         *
         */
        public static function set_record_data($opts = []) {

            global $USER;
            $data = new stdClass();

            // This case should't appears but this is for safety.
            if (!$opts['itemid'] && !$opts['partid']) {
                $data->id = null;
                $data->name = null;
                $data->timecreated = null;
                $data->createdby = null;
            }

            // This is the case when user back to the part.
            if ($opts['itemid']) {
                $data = self::get_record( $opts['itemid']);
            } else if ($opts['partid']) {
                // This is the case when user editing part first time.
                // itemid doesn't appear in the url, there is only partid.
                // We need to search record by partid instead itemid.
                // This is require because if user cancel settings option iframe will be reloaded and user lost all earlier changes.

                // We have to check if part exists.
                // If yes we will get data from the existing record.
                if (self::is_partid($opts['partid'])) {
                    $data = self::get_record($opts['partid'], true);
                } else {
                    // If record doesn't exists we have to create it.
                    // For this we need to define some data parts.
                    $data->partid = $opts['partid'];
                    $data->name = $opts['name'] ? urldecode($opts['name']) : 'Part' . time();
                }
            }

            // Set context id.
            $data->contextid = isset($data->contextid) && $data->contextid ? $data->contextid : $opts['contextid'];

            // Set date created and modified.
            $data->timecreated = isset($data->timecreated) ? $data->timecreated : time();
            $data->timemodified = $data->timecreated < time() ? time() : 0;

            // Set create and modifier.
            $data->createdby = isset($data->createdby) ? $data->createdby : $USER->id;
            $data->modifiedby = $data->timecreated == time() ? 0 : $USER->id;

            // Set demo content when part is saved via AJAX request.
            // This is used in 'save-part.php' files.
            if (isset($opts['democontent'])) {
                $data->democontent = $opts['democontent'];
            }

            return $data;

        }




        /**
         *
         * Method to get form data.
         *
         */
        public static function get_form_data($form, $data) {
            global $CFG;
            require_once($CFG->libdir . '/formslib.php');

            $form->set_data($data);

            return $form->get_data();

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
         * Method to get table sort headers
         *
         */
        public static function get_table_header($columns, $tosort = []) {
            global $OUTPUT;

            $headers = [];
            $syscontext = context_system::instance();
            $contextid = optional_param('contextid', $syscontext->id, PARAM_INT);
            $sort = optional_param('sort', 'timecreated', PARAM_ALPHANUMEXT);
            $dir = optional_param('dir', 'DESC', PARAM_ALPHA);
            $page = optional_param('page', 0, PARAM_INT );
            $perpage = 20;

            foreach ($columns as $k => $column) {
                if (in_array($k, $tosort)) {
                    $isdir = $dir == 'DESC' ? 'ASC' : 'DESC';
                    $url = new moodle_url('/local/mb2builder/parts.php', ['sort' => $k, 'dir' => $isdir, 'page' => $page,
                    'perpage' => $perpage, 'contextid' => $contextid]);
                    $iconname = $isdir === 'ASC' ? 'sort_desc' : 'sort_asc';
                    $colicon = $OUTPUT->pix_icon('t/' . $iconname, get_string(strtolower($isdir)), 'core',
                    ['class' => 'iconsort']);

                    $headers[] = '<a href="' . $url . '">' . get_string($k, 'local_mb2builder') .  $colicon . '</a>';
                } else {
                    $component = $k === 'actions' ? '' : 'local_mb2builder';
                    $headers[] = get_string($k, $component);
                }
            }

            return $headers;
        }




        /**
         *
         * Method to get part filter
         *
         */
        public static function part_filter() {

            global $USER, $PAGE, $OUTPUT;
            $output = '';
            $syscontext = context_system::instance();
            $contextid = optional_param('contextid', $syscontext->id, PARAM_INT);
            $opts = [];

            $cache = cache::make('local_mb2builder', 'builder');
            $cachecoursecatscourses = 'usercats_courses_' . $USER->id;

            if (!$cache->get($cachecoursecatscourses)) {
                if (has_capability('local/mb2builder:manageparts', $syscontext)) {
                    $opts[$syscontext->id] = get_string('coresystem');
                }

                $contextfields = 'ctxid, ctxpath, ctxdepth, ctxlevel, ctxinstance, ctxlocked';
                list($categories, $courses) = get_user_capability_contexts('local/mb2builder:manageparts', true, $USER->id, true,
                'fullname, ' . $contextfields . '', 'name, ' . $contextfields . '', 'fullname', 'name');

                $catopts = [];
                if (count($categories)) {
                    foreach ($categories as $cat) {
                        $catopts[$cat->ctxid] = $cat->name;
                    }
                }

                if (!empty($catopts)) {
                    $opts['categories'] = [get_string('categories') => $catopts];
                }

                $courseopts = [];
                if (count($courses)) {
                    foreach ($courses as $course) {
                        $courseopts[$course->ctxid] = $course->fullname;
                    }
                }

                if (!empty($courseopts)) {
                    $opts['courses'] = [get_string('courses') => $courseopts];
                }

                $cache->set($cachecoursecatscourses, $opts);
            }

            $singleselect = new single_select(new moodle_url('/local/mb2builder/parts.php'), 'contextid',
            $cache->get($cachecoursecatscourses), $contextid, get_string('choosecontext', 'local_mb2builder'));
            $singleselect->set_label(get_string('choosecontext', 'local_mb2builder'), ['class' => 'sr-only']);

            $output .= '<div class="partfilter mb-3">';
            $output .= $OUTPUT->render($singleselect);
            $output .= '</div>';

            return $output;

        }

    }
}
