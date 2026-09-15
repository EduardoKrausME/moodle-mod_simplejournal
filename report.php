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
 * report.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

use mod_simplejournal\entry_manager;

$id = required_param("id", PARAM_INT);
$userid = optional_param("userid", 0, PARAM_INT);

$cm = get_coursemodule_from_id("simplejournal", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$journal = $DB->get_record("simplejournal", ["id" => $cm->instance], "*", MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/simplejournal:viewall", $context);

$PAGE->set_url("/mod/simplejournal/report.php", ["id" => $cm->id, "userid" => $userid]);
$PAGE->set_title(get_string("report", "mod_simplejournal"));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$users = get_enrolled_users(
    $context,
    "mod/simplejournal:submit",
    0,
    "u.*",
    "u.lastname ASC, u.firstname ASC"
);

$options = [0 => get_string("selectstudent", "mod_simplejournal")];
foreach ($users as $user) {
    $options[$user->id] = fullname($user);
}

if ($userid !== 0 && !isset($users[$userid])) {
    throw new moodle_exception("invaliduser", "error");
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string("reportfor", "mod_simplejournal", format_string($journal->name)));

echo $OUTPUT->single_select(
    new moodle_url("/mod/simplejournal/report.php", ["id" => $cm->id]),
    "userid",
    $options,
    $userid,
    null,
    "simplejournal-user-select"
);

if ($userid !== 0) {
    $selecteduser = $users[$userid];
    $entry = entry_manager::get_user_entry($journal->id, $userid);

    $templatedata = [
        "student" => fullname($selecteduser),
        "hasentry" => (bool) $entry,
        "noentry" => get_string("noentry", "mod_simplejournal"),
    ];

    if ($entry) {
        $templatedata["content"] = format_text($entry->textcontent, $entry->textformat, [
            "context" => $context,
            "para" => false,
        ]);
        $templatedata["createdlabel"] = get_string("created", "mod_simplejournal");
        $templatedata["created"] = userdate($entry->timecreated);
        $templatedata["modifiedlabel"] = get_string("lastmodified", "mod_simplejournal");
        $templatedata["modified"] = userdate($entry->timemodified);
    }

    echo $OUTPUT->render_from_template("mod_simplejournal/report_entry", $templatedata);
}

echo $OUTPUT->footer();
