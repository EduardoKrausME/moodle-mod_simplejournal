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
 * restore_simplejournal_stepslib.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_simplejournal_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines restore paths.
     *
     * @return array
     */
    protected function define_structure(): array {
        $paths = [];
        $paths[] = new restore_path_element("simplejournal", "/activity/simplejournal");

        if ($this->get_setting_value("userinfo")) {
            $paths[] = new restore_path_element("simplejournal_entry", "/activity/simplejournal/entries/entry");
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restores the activity record.
     *
     * @param array $data Restore data.
     * @return void
     */
    protected function process_simplejournal($data): void {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record("simplejournal", $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restores one user entry.
     *
     * @param array $data Restore data.
     * @return void
     */
    protected function process_simplejournal_entry($data): void {
        global $DB;

        $data = (object) $data;
        $data->journalid = $this->get_new_parentid("simplejournal");
        $data->userid = $this->get_mappingid("user", $data->userid);

        if (!$data->userid) {
            return;
        }

        $DB->insert_record("simplejournal_entries", $data);
    }

    /**
     * Restores files associated with the activity introduction.
     *
     * @return void
     */
    protected function after_execute(): void {
        $this->add_related_files("mod_simplejournal", "intro", null);
    }
}
