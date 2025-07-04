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
 *
 */

define('AJAX_SCRIPT', true);

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_sesskey();

if (!confirm_sesskey() || !isloggedin() || isguestuser()) {
    die;
}

$themedir = '/theme';

if (isset($CFG->themedir)) {
    $themedir = $CFG->themedir;
    $themedir = str_replace($CFG->dirroot, '', $CFG->themedir);
}

$context = context_system::instance();
$PAGE->set_url($themedir . '/mb2nl/lib/lib_ajax_bookmarks_manage.php');
$PAGE->set_context($context);
$bookmarks = [];
$bkupdate = 0;
$bkdelete = 0;
$bklimit = 999;

$bkurl = $_GET["mb2bkurl"];
$bktitle = $_GET["mb2bktitle"];
$bkdelete = $_GET["bkdelete"];
$bkupdate = $_GET["bkupdate"];
$bklimit = $_GET["bklimit"];

if (htmlspecialchars_decode($bkurl) && $bktitle && confirm_sesskey()) {

    if (get_user_preferences('user_bookmarks')) {
        // Get user bookmarks.
        $bookmarks = explode(',', get_user_preferences('user_bookmarks'));

        foreach ($bookmarks as $b) {
            $barr = explode(';', $b);

            if ($barr[0] === $bkurl) {
                $bkupdate = 1;

                // Find key and update or remove it.
                $ktoupdate = array_search($b, $bookmarks);

                if ($bkdelete == 1) {
                    unset($bookmarks[$ktoupdate]);
                } else {
                    // Create new bookmark record.
                    $newbookmark = $bkurl . ";" . $bktitle;
                    $bookmarks[$ktoupdate] = $newbookmark;
                }
            }
        }
    }

    // Add only new bookmark element to array.
    if ($bkupdate == 0 && $bkdelete == 0) {
        $bookmarks[] = $bkurl . ";" . $bktitle;

        // Check bookmarks limit.
        if (count($bookmarks) > $bklimit) {
             echo get_string('bkmarklimit', 'theme_mb2nl', ['limit' => $bklimit]);
             die;
        }
    }

    $bookmarks = implode(',', $bookmarks);

    set_user_preference('user_bookmarks', $bookmarks);
    echo json_encode(explode(',', $bookmarks));

    die;
} else {
     echo get_string('wentwrong', 'theme_mb2nl');
     die;
}
die;
