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
 * index.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

$id = required_param("id", PARAM_INT);
$course = get_course($id);

require_course_login($course);
$coursecontext = context_course::instance($course->id);

$PAGE->set_url("/mod/simplejournal/", ["id" => $course->id]);
$PAGE->set_title(get_string("modulenameplural", "mod_simplejournal"));
$PAGE->set_heading(format_string($course->fullname));

$instances = get_all_instances_in_course("simplejournal", $course);

$table = new html_table();
$table->head = [
    get_string("name"),
    get_string("status", "mod_simplejournal"),
];

foreach ($instances as $instance) {
    $cmcontext = context_module::instance($instance->coursemodule);
    if (!has_capability("mod/simplejournal:view", $cmcontext)) {
        continue;
    }

    $status = "";
    if (has_capability("mod/simplejournal:submit", $cmcontext)) {
        $exists = $DB->record_exists("simplejournal_entries", [
            "journalid" => $instance->id,
            "userid" => $USER->id,
        ]);
        $status = $exists ? get_string("submitted", "mod_simplejournal") : get_string("notsubmitted", "mod_simplejournal");
    } else if (has_capability("mod/simplejournal:viewall", $cmcontext)) {
        $count = $DB->count_records("simplejournal_entries", ["journalid" => $instance->id]);
        $status = get_string("entrycount", "mod_simplejournal", $count);
    }

    $url = new moodle_url("/mod/simplejournal/view.php", ["id" => $instance->coursemodule]);
    $table->data[] = [html_writer::link($url, format_string($instance->name)), $status];
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("modulenameplural", "mod_simplejournal"));

if (empty($table->data)) {
    echo $OUTPUT->notification(get_string("noinstances", "mod_simplejournal"), "info");
} else {
    echo html_writer::table($table);
}

echo $OUTPUT->footer();
