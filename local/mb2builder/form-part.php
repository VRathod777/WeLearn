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
require_once(__DIR__ . '/classes/builder.php');

/**
 * Form class
 */
class local_mb2builder_part_edit_form extends moodleform {

    /**
     * Defines the standard structure of the form.
     *
     * @throws \coding_exception
     */
    protected function definition() {
        global $PAGE;
        $mform = $this->_form;

        $contextid = optional_param('contextid', \context_system::instance()->id, PARAM_INT);

        // Hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->addElement('hidden', 'timecreated');
        $mform->addElement('hidden', 'timemodified');
        $mform->addElement('hidden', 'createdby');
        $mform->addElement('hidden', 'modifiedby');
        $mform->addElement('hidden', 'partid');
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->setType('id', PARAM_INT);
        $mform->setType('timecreated', PARAM_INT);
        $mform->setType('timemodified', PARAM_INT);
        $mform->setType('createdby', PARAM_INT);
        $mform->setType('modifiedby', PARAM_INT);
        $mform->setType('partid', PARAM_TEXT);
        $mform->setType('contextid', PARAM_INT);

        $mform->addElement('html', '<div class="mb2builder-form">');
        $mform->addElement('text', 'name', get_string('name', 'local_mb2builder'), ['size' => 60]);
        $mform->addRule('name', null, 'required');
        $mform->setType('name', PARAM_NOTAGS);
        $mform->setDefault('name', 'part');
        $mform->addElement('html', mb2builderBuilder::responsive_buttons_html());
        $mform->addElement('html', '</div>');
        $mform->addElement('textarea', 'content', get_string('content', 'moodle'));
        $mform->setType('content', PARAM_RAW);
        $mform->addElement('textarea', 'democontent', 'democontent');
        $mform->setType('democontent', PARAM_RAW);

        $this->add_action_buttons();

    }
}
