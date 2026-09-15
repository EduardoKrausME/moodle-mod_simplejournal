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
 * view.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");
require_once($CFG->libdir . "/completionlib.php");

use mod_simplejournal\entry_manager;
use mod_simplejournal\form\entry_form;

$id = required_param("id", PARAM_INT);
$saved = optional_param("saved", 0, PARAM_BOOL);

$cm = get_coursemodule_from_id("simplejournal", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$journal = $DB->get_record("simplejournal", ["id" => $cm->instance], "*", MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/simplejournal:view", $context);

$PAGE->set_url("/mod/simplejournal/view.php", ["id" => $cm->id]);
$PAGE->set_title(format_string($journal->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$event = \mod_simplejournal\event\course_module_viewed::create([
    "objectid" => $journal->id,
    "context" => $context,
]);
$event->add_record_snapshot("course", $course);
$event->add_record_snapshot("simplejournal", $journal);
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

if (has_capability("mod/simplejournal:submit", $context)) {
    $entry = entry_manager::get_user_entry($journal->id, $USER->id);
    $form = new entry_form(new moodle_url("/mod/simplejournal/view.php", ["id" => $cm->id]), [
        "cmid" => $cm->id,
        "context" => $context,
    ]);

    if ($data = $form->get_data()) {
        entry_manager::save_user_entry(
            $journal->id,
            $USER->id,
            $data->textcontent["text"],
            $data->textcontent["format"]
        );

        redirect(new moodle_url("/mod/simplejournal/view.php", ["id" => $cm->id, "saved" => 1]));
    }

    if ($entry) {
        $form->set_data((object) [
            "id" => $cm->id,
            "textcontent" => [
                "text" => $entry->textcontent,
                "format" => $entry->textformat,
            ],
        ]);
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($journal->name));

$intro = format_module_intro("simplejournal", $journal, $cm->id, false);
if ($intro !== "") {
    echo $OUTPUT->render_from_template("mod_simplejournal/instructions", ["content" => $intro]);
}

if (has_capability("mod/simplejournal:viewall", $context)) {
    $reporturl = new moodle_url("/mod/simplejournal/report.php", ["id" => $cm->id]);
    echo $OUTPUT->single_button($reporturl, get_string("viewreport", "mod_simplejournal"), "get");
}

if (has_capability("mod/simplejournal:submit", $context)) {
    if ($saved) {
        echo $OUTPUT->notification(get_string("entrysaved", "mod_simplejournal"), "success");
    }

    if ($entry) {
        echo $OUTPUT->render_from_template("mod_simplejournal/entry_meta", [
            "label" => get_string("lastmodified", "mod_simplejournal"),
            "date" => userdate($entry->timemodified),
        ]);
    }

    $form->display();
} else if (!has_capability("mod/simplejournal:viewall", $context)) {
    echo $OUTPUT->notification(get_string("nosubmitpermission", "mod_simplejournal"), "info");
}

echo $OUTPUT->footer();
