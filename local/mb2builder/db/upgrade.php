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

 /**
  * Upgrade method.
  */
function xmldb_local_mb2builder_upgrade($oldversion) {
    global $DB, $SITE;
    $dbman = $DB->get_manager();
    $pagedata = new stdClass();

    require_once(__DIR__ . '/../classes/pages_api.php');

    if ($oldversion < 2020090916) {
        $tablepages = new xmldb_table( 'local_mb2builder_pages' );
        $tablepages->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tablepages->add_field('pageid', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablepages->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablepages->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablepages->add_field('title', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablepages->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablepages->add_field('democontent', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablepages->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablepages->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablepages->add_key('primary', XMLDB_KEY_PRIMARY, ['id'] );

        if (!$dbman->table_exists($tablepages)) {
            $dbman->create_table($tablepages);
        }

        $tablelayouts = new xmldb_table( 'local_mb2builder_layouts' );
        $tablelayouts->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tablelayouts->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablelayouts->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablelayouts->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablelayouts->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablelayouts->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablelayouts->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablelayouts->add_key('primary', XMLDB_KEY_PRIMARY, ['id'] );

        if (!$dbman->table_exists($tablelayouts)) {
            $dbman->create_table($tablelayouts);
        }

        // Now we have to check if user has page built with the old page builder
        // If yes we have to:
        // 1. Add new page
        // 2. Set the 'mpage' filed to -1.
        $oldpage = isset(get_config('local_mb2builder')->builderfptext) ? get_config('local_mb2builder')->builderfptext : '';

        if ($oldpage) {
            // Set content and demo content for the new page.
            $pagedata->content = $oldpage;
            $pagedata->democontent = $oldpage;
            $pagedata->title = get_string('sitehome');
            $pagedata->mpage = -1;

            // Add new page.
            Mb2builderPagesApi::add_record($pagedata);
        }

    }

    if ($oldversion < 2021033025) {
        $tablepages = new xmldb_table( 'local_mb2builder_pages' );
        $headingfield = new xmldb_field( 'heading', XMLDB_TYPE_INTEGER, '10', null, null, null, '0' );

        if (!$dbman->field_exists( $tablepages, $headingfield )) {
            $dbman->add_field( $tablepages, $headingfield );
        }

        upgrade_plugin_savepoint( true, 2021033025, 'local' , 'mb2builder' );
    }

    if ($oldversion < 2022060616) {
        $tablepages = new xmldb_table( 'local_mb2builder_pages' );
        $footerfield = new xmldb_field( 'footer', XMLDB_TYPE_INTEGER, '10', null, null, null, '0' );

        if (!$dbman->field_exists( $tablepages, $footerfield )) {
            $dbman->add_field( $tablepages, $footerfield );
        }

        upgrade_plugin_savepoint( true, 2022060616, 'local' , 'mb2builder' );
    }

    // Add footer table.
    if ($oldversion < 2022060616) {
        $tablefooters = new xmldb_table( 'local_mb2builder_footers' );
        $tablefooters->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tablefooters->add_field('footerid', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablefooters->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablefooters->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablefooters->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablefooters->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablefooters->add_field('democontent', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablefooters->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablefooters->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablefooters->add_key('primary', XMLDB_KEY_PRIMARY, ['id'] );

        if (!$dbman->table_exists($tablefooters)) {
            $dbman->create_table($tablefooters);
        }
    }

    // Add header style field.
    if ($oldversion < 2022111412) {
        $tablepages = new xmldb_table( 'local_mb2builder_pages' );
        $headerstylefield = new xmldb_field( 'headerstyle', XMLDB_TYPE_TEXT, null, null, null, null );

        if (!$dbman->field_exists( $tablepages, $headerstylefield )) {
            $dbman->add_field( $tablepages, $headerstylefield );
        }

        upgrade_plugin_savepoint( true, 2022111412, 'local' , 'mb2builder' );
    }

    // Add page style.
    if ($oldversion < 2022121521) {
        $tablepages = new xmldb_table('local_mb2builder_pages');
        $pagecssfield = new xmldb_field('pagecss', XMLDB_TYPE_TEXT, null, null, null, null);

        if (!$dbman->field_exists($tablepages, $pagecssfield)) {
            $dbman->add_field($tablepages, $pagecssfield);
        }

        upgrade_plugin_savepoint(true, 2022121521, 'local' , 'mb2builder');
    }

    // Add menu.
    if ($oldversion < 2023032919 ) {
        $tablepages = new xmldb_table('local_mb2builder_pages');
        $menufield = new xmldb_field('menu', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        if (!$dbman->field_exists($tablepages, $menufield)) {
            $dbman->add_field($tablepages, $menufield);
        }

        upgrade_plugin_savepoint(true, 2023032919, 'local' , 'mb2builder');
    }

    // Add toggle sidebar.
    if ($oldversion < 2024012514 ) {
        $tablepages = new xmldb_table('local_mb2builder_pages');
        $tgsdbfield = new xmldb_field('tgsdb', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        if (!$dbman->field_exists( $tablepages, $tgsdbfield)) {
            $dbman->add_field( $tablepages, $tgsdbfield);
        }

        upgrade_plugin_savepoint(true, 2024012514, 'local' , 'mb2builder');
    }

    // Add page ID.
    if ($oldversion < 2024090412) {
        // Add mpage.
        $table = new xmldb_table('local_mb2builder_pages');
        $field = new xmldb_field('mpage', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set mpage value for existing pages.
        $pages = Mb2builderPagesApi::get_list_records();

        // Get page module ID.
        $pagemodsql = 'SELECT id FROM {modules} WHERE name=:name';
        $pagemodid = $DB->get_record_sql($pagemodsql, ['name' => 'page'])->id;

        foreach ($pages as $page) {
            // Get page to update.
            $itemtoupdate = Mb2builderPagesApi::get_record($page->id);
            $itemtoupdate->mpage = 0;

            // Define search query.
            $search = '%[mb2page pageid="'.  $page->id .'"%';
            $params = ['course' => $SITE->id, 'summary' => $search];

            // Search query for the front page.
            $fpsql = 'SELECT id FROM {course_sections} WHERE course=:course AND ' . $DB->sql_like('summary', ':summary');

            // Search for the page modules.
            $psql = 'SELECT id FROM {page} WHERE ' . $DB->sql_like('content', ':summary');

            if ($DB->record_exists_sql($fpsql, $params)) {
                $itemtoupdate->mpage = -1; // ...-1 = front page.
            } else if ($DB->record_exists_sql($psql, $params)) { // Search page modules.
                // Maybe user insert the same shortcode to more than one Moodle pages.
                // So We have to get the first Moodle page.
                $mpages = $DB->get_records_sql($psql, $params);
                $mpage = array_shift($mpages);

                // Get the URL page ID.
                $urlidsql = 'SELECT id FROM {course_modules} WHERE module=' . $pagemodid . ' AND instance=' . $mpage->id;

                if ($DB->record_exists_sql($urlidsql)) {
                    $itemtoupdate->mpage = $DB->get_record_sql($urlidsql)->id; // ...page module ID from URL: ?id=...
                }
            }

            Mb2builderPagesApi::update_record_data($itemtoupdate);
        }

        upgrade_plugin_savepoint(true, 2024090412, 'local' , 'mb2builder');
    }

    // Content parts.
    if ($oldversion < 2025010911) {
        $tablectparts = new xmldb_table('local_mb2builder_parts');
        $tablectparts->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tablectparts->add_field('partid', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablectparts->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablectparts->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablectparts->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablectparts->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablectparts->add_field('democontent', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablectparts->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablectparts->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablectparts->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($tablectparts)) {
            $dbman->create_table($tablectparts);
        }

        upgrade_plugin_savepoint(true, 2025010911, 'local' , 'mb2builder');
    }

    if ($oldversion < 2025012414) {
        $table = new xmldb_table('local_mb2builder_parts');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, 0);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025012414, 'local' , 'mb2builder');
    }

    if ($oldversion < 2025032818) {
        // Change default value of the 'contextid' field.
        $table = new xmldb_table('local_mb2builder_parts');
        $field = new xmldb_field('contextid', XMLDB_TYPE_INTEGER, '10', null, null, null, 0);
        $dbman->change_field_type($table, $field);

        // Create the 'backups' table.
        $tablebkps = new xmldb_table('local_mb2builder_backups');
        $tablebkps->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tablebkps->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablebkps->add_field('type', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('itemid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('auto', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('filename', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablebkps->add_field('filesize', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $tablebkps->add_field('contenthash', XMLDB_TYPE_TEXT, null, null, null, null);
        $tablebkps->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        if (!$dbman->table_exists($tablebkps)) {
            $dbman->create_table($tablebkps);
        }

        upgrade_plugin_savepoint(true, 2025032818, 'local' , 'mb2builder');
    }

    return true;
}
