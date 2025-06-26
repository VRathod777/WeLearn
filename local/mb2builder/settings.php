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

global $CFG, $PAGE;

$context = context_system::instance();
$managepages = has_capability('local/mb2builder:managepages', $context);
$managefooters = has_capability('local/mb2builder:managefooters', $context);
$managebackups = has_capability('local/mb2builder:managebackups', $context);

if ($managepages || $managefooters || $managebackups) {

    $ADMIN->add('root', new admin_category('local_mb2builder', get_string('pluginname', 'local_mb2builder', null, true)));

    if ($managepages) {
        $page = new admin_externalpage('local_mb2builder_managepages', get_string('managepages', 'local_mb2builder'),
        new moodle_url( '/local/mb2builder/index.php' ), 'local/mb2builder:managepages');
        $ADMIN->add('local_mb2builder', $page);
    }

    if ($managefooters) {
        $page = new admin_externalpage('local_mb2builder_managefooters', get_string('managefooters', 'local_mb2builder'),
        new moodle_url('/local/mb2builder/footers.php'), 'local/mb2builder:managefooters');
        $ADMIN->add('local_mb2builder', $page);
    }

    if ($managebackups) {
        $page = new admin_externalpage('local_mb2builder_managebackups', get_string('backups', 'local_mb2builder'),
        new moodle_url('/local/mb2builder/backups.php'), 'local/mb2builder:managebackups');
        $ADMIN->add('local_mb2builder', $page);
    }

    $page = new admin_settingpage('local_mb2builder_options', get_string('options', 'local_mb2builder', null, true));

    $name = 'local_mb2builder/theme';
    $title = get_string('theme', 'local_mb2builder');
    $setting = new admin_setting_configtext($name, $title, '', '');
    $page->add($setting);

    $name = 'local_mb2builder/autobackup';
    $title = get_string('autobackup', 'local_mb2builder');
    $setting = new admin_setting_configcheckbox($name, $title, '', 1);
    $page->add($setting);

    $name = 'local_mb2builder/backupnum';
    $title = get_string('backupnum', 'local_mb2builder');
    $setting = new admin_setting_configselect($name, $title, '', 3, [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
    ]);
    $page->add($setting);

    $name = 'local_mb2builder/customlayouts';
    $title = get_string('layoutscustom', 'local_mb2builder');
    $setting = new admin_setting_configcheckbox($name, $title, '', 0);
    $page->add($setting);

    $name = 'local_mb2builder/nopartelements';
    $title = get_string('nopartelements', 'local_mb2builder');
    $setting = new admin_setting_configtextarea($name, $title, '', 'announcements
categories
courses
coursetabs
events
login
mycourses
reviews
search
teachers');
    $page->add($setting);

    $ADMIN->add('local_mb2builder', $page);

}
