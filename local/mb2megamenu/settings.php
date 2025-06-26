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
 * @package    local_mb2megamenu
 * @copyright  2019 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license    PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/classes/fields/mb2color.php');

if (has_capability('local/mb2megamenu:manageitems', context_system::instance())) {

    $ADMIN->add('root', new admin_category('local_mb2megamenu', get_string('pluginname', 'local_mb2megamenu', null, true)));
    $page = new admin_externalpage('local_mb2megamenu_managemenus', get_string('managemenus', 'local_mb2megamenu'),
    new moodle_url('/local/mb2megamenu/index.php'));
    $ADMIN->add('local_mb2megamenu', $page);

    $page = new admin_settingpage('local_mb2megamenu_options', get_string('options', 'local_mb2megamenu', null, true));

    $name = 'local_mb2megamenu/enablemenu';
    $title = get_string('enablemenu', 'local_mb2megamenu');
    $setting = new admin_setting_configcheckbox($name, $title, '', 0);
    $page->add($setting);

    $name = 'local_mb2megamenu/exticon';
    $title = get_string('exticon', 'local_mb2megamenu');
    $setting = new admin_setting_configcheckbox($name, $title, '', 1);
    $page->add($setting);

    $ADMIN->add('local_mb2megamenu', $page);
}
