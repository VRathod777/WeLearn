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

define( 'AJAX_SCRIPT', true );

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get builder files.
require_once(__DIR__ . '/../lib.php');
require_once(__DIR__ . '/../classes/api.php');

$itemid = optional_param('itemid', 0, PARAM_INT);
$footer = optional_param('footer', 0, PARAM_INT);
$part = optional_param('part', 0, PARAM_INT);

$filearea = optional_param('filearea', '', PARAM_TEXT);
$pageid = optional_param('pageid', '', PARAM_TEXT);
$footerid = optional_param('footerid', '', PARAM_TEXT);
$partid = optional_param('partid', '', PARAM_TEXT);
$imgname = optional_param('imgname', '', PARAM_TEXT);

$PAGE->set_url('/local/mb2builder/ajax/image-delete.php');
$PAGE->set_context(context_system::instance());

require_login();
require_sesskey();
$opts = ['itemid' => $itemid, 'filearea' => $filearea, 'pageid' => $pageid, 'footerid' => $footerid,
'partid' => $partid];
mb2builderApi::delete_file($imgname, $opts);

echo mb2builderApi::get_images_preview($filearea, $opts);
die();
