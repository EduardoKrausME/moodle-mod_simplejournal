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
 * backup_simplejournal_activity_task.class.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/mod/simplejournal/backup/moodle2/backup_simplejournal_stepslib.php");

/**
 * Class backup_simplejournal_activity_task.
 */
class backup_simplejournal_activity_task extends backup_activity_task {
    /**
     * define_my_settings
     *
     * @return void
     */
    protected function define_my_settings(): void {
    }

    /**
     * define_my_steps
     *
     * @return void
     */
    protected function define_my_steps(): void {
        $this->add_step(new backup_simplejournal_activity_structure_step("simplejournal_structure", "simplejournal.xml"));
    }

    /**
     * No custom activity links are encoded.
     *
     * @param string $content Content.
     * @return string
     */
    public static function encode_content_links($content): string {
        return $content;
    }
}
