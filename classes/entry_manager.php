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
 * entry_manager.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_simplejournal;

/**
 * Class entry_manager.
 */
class entry_manager {
    /**
     * Gets one user's entry for one journal.
     *
     * @param int $journalid Journal id.
     * @param int $userid User id.
     * @return \stdClass|null
     */
    public static function get_user_entry(int $journalid, int $userid): ?\stdClass {
        global $DB;

        $entry = $DB->get_record("simplejournal_entries", [
            "journalid" => $journalid,
            "userid" => $userid,
        ]);

        return $entry ?: null;
    }

    /**
     * Inserts or updates one user's journal entry.
     *
     * @param int $journalid Journal id.
     * @param int $userid User id.
     * @param string $textcontent Entry text.
     * @param int $textformat Moodle text format.
     * @return int Entry id.
     */
    public static function save_user_entry(int $journalid, int $userid, string $textcontent, int $textformat): int {
        global $DB;

        $now = time();
        $entry = self::get_user_entry($journalid, $userid);

        if ($entry) {
            $entry->textcontent = $textcontent;
            $entry->textformat = $textformat;
            $entry->timemodified = $now;
            $DB->update_record("simplejournal_entries", $entry);
            return (int) $entry->id;
        }

        return (int) $DB->insert_record("simplejournal_entries", (object) [
            "journalid" => $journalid,
            "userid" => $userid,
            "textcontent" => $textcontent,
            "textformat" => $textformat,
            "timecreated" => $now,
            "timemodified" => $now,
        ]);
    }
}
