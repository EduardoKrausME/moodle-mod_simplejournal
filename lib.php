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
 * lib.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Declares the features supported by the activity.
 *
 * @param string $feature Feature constant.
 * @return mixed
 */
function simplejournal_supports(string $feature) {
    return match ($feature) {
        FEATURE_MOD_INTRO => true,
        FEATURE_SHOW_DESCRIPTION => true,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_COMMUNICATION,
        default => null,
    };
}

/**
 * Creates a journal instance.
 *
 * @param stdClass $data Form data.
 * @param mod_simplejournal_mod_form|null $mform Form instance.
 * @return int
 */
function simplejournal_add_instance(stdClass $data, ?mod_simplejournal_mod_form $mform = null): int {
    global $DB;

    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    return $DB->insert_record("simplejournal", $data);
}

/**
 * Updates a journal instance.
 *
 * @param stdClass $data Form data.
 * @param mod_simplejournal_mod_form|null $mform Form instance.
 * @return bool
 */
function simplejournal_update_instance(stdClass $data, ?mod_simplejournal_mod_form $mform = null): bool {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();

    return $DB->update_record("simplejournal", $data);
}

/**
 * Deletes a journal instance.
 *
 * @param int $id Instance id.
 * @return bool
 */
function simplejournal_delete_instance(int $id): bool {
    global $DB;

    if (!$journal = $DB->get_record("simplejournal", ["id" => $id])) {
        return false;
    }

    $DB->delete_records("simplejournal_entries", ["journalid" => $journal->id]);
    $DB->delete_records("simplejournal", ["id" => $journal->id]);

    return true;
}

/**
 * Adds the report link to the activity settings navigation.
 *
 * @param settings_navigation $settings Navigation object.
 * @param navigation_node $node Activity node.
 * @return void
 */
function simplejournal_extend_settings_navigation(settings_navigation $settings, navigation_node $node): void {
    if (!$settings->get_page()->cm) {
        return;
    }

    $context = $settings->get_page()->cm->context;
    if (!has_capability("mod/simplejournal:viewall", $context)) {
        return;
    }

    $url = new moodle_url("/mod/simplejournal/report.php", ["id" => $settings->get_page()->cm->id]);
    $node->add(get_string("report", "mod_simplejournal"), $url, navigation_node::TYPE_SETTING);
}
