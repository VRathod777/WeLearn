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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Item media form class
 */
class media_edit_form extends moodleform {

    /**
     * Defines the standard structure of the form.
     *
     * @throws \coding_exception
     */
    protected function definition() {
        global $PAGE, $CFG;
        $mform = $this->_form;
        $maxbytes = $CFG->maxbytes;

        $footerid = optional_param('footerid', '', PARAM_TEXT);
        $partid = optional_param('partid', '', PARAM_TEXT);
        $itemid = optional_param('itemid', 0, PARAM_INT);
        $pageid = optional_param('pageid', '', PARAM_TEXT);

        $mform->addElement('hidden', 'mediatype');
        $mform->setType('mediatype', PARAM_INT);
        $mform->setDefault('mediatype', 1);

        $mform->addElement('hidden', 'footerid');
        $mform->setType('footerid', PARAM_TEXT);
        $mform->setDefault('footerid', $footerid);

        $mform->addElement('hidden', 'partid');
        $mform->setType('partid', PARAM_TEXT);
        $mform->setDefault('partid', $partid);

        $mform->addElement('hidden', 'itemid');
        $mform->setType('itemid', PARAM_INT);
        $mform->setDefault('itemid', $itemid);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_TEXT);
        $mform->setDefault('pageid', $pageid);

        $mform->addElement('filepicker', 'mediafile', '', null, ['maxbytes' => $maxbytes, 'accepted_types' => '*']);
    }
}


/**
 * Global media form class
 */
class globalmedia_edit_form extends moodleform {

    /**
     * Defines the standard structure of the form.
     *
     * @throws \coding_exception
     */
    protected function definition() {
        global $PAGE, $CFG;
        $mform = $this->_form;
        $maxbytes = $CFG->maxbytes;

        $mform->addElement('hidden', 'mediatype');
        $mform->setType('mediatype', PARAM_INT);
        $mform->setDefault('mediatype', 2);

        $mform->addElement('filepicker', 'mediafileglobal', '', null, ['maxbytes' => $maxbytes, 'accepted_types' => '*']);
    }
}
