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
 * entry_form.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_simplejournal\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

/**
 * Class entry_form.
 */
class entry_form extends \moodleform {
    /**
     * Defines the reflection editor.
     *
     * @return void
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement("hidden", "id", $this->_customdata["cmid"]);
        $mform->setType("id", PARAM_INT);

        $mform->addElement("editor", "textcontent", get_string("yourreflection", "mod_simplejournal"), [
            "rows" => 14,
        ], [
            "maxfiles" => 0,
            "noclean" => false,
            "context" => $this->_customdata["context"],
        ]);
        $mform->setType("textcontent", PARAM_RAW);
        $mform->addRule("textcontent", get_string("required"), "required", null, "client");

        $this->add_action_buttons(false, get_string("saveentry", "mod_simplejournal"));
    }
}
