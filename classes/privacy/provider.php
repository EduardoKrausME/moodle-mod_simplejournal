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
 * provider.php
 *
 * @package   mod_simplejournal
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_simplejournal\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Class provider.
 */
class provider implements
        \core_privacy\local\metadata\provider,
        \core_privacy\local\request\plugin\provider,
        \core_privacy\local\request\core_userlist_provider {

    /**
     * Describes stored personal data.
     *
     * @param collection $collection Metadata collection.
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table("simplejournal_entries", [
            "journalid" => "privacy:metadata:simplejournal_entries:journalid",
            "userid" => "privacy:metadata:simplejournal_entries:userid",
            "textcontent" => "privacy:metadata:simplejournal_entries:textcontent",
            "textformat" => "privacy:metadata:simplejournal_entries:textformat",
            "timecreated" => "privacy:metadata:simplejournal_entries:timecreated",
            "timemodified" => "privacy:metadata:simplejournal_entries:timemodified",
        ], "privacy:metadata:simplejournal_entries");

        return $collection;
    }

    /**
     * Gets module contexts containing data for the user.
     *
     * @param int $userid User id.
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {simplejournal} j ON j.id = cm.instance
                  JOIN {simplejournal_entries} e ON e.journalid = j.id
                 WHERE ctx.contextlevel = :contextlevel
                   AND e.userid = :userid";
        $contextlist->add_from_sql($sql, [
            "modname" => "simplejournal",
            "contextlevel" => CONTEXT_MODULE,
            "userid" => $userid,
        ]);

        return $contextlist;
    }

    /**
     * Exports user data.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            $cm = get_coursemodule_from_id("simplejournal", $context->instanceid, 0, false, MUST_EXIST);
            $entry = $DB->get_record("simplejournal_entries", [
                "journalid" => $cm->instance,
                "userid" => $userid,
            ]);

            if (!$entry) {
                continue;
            }

            writer::with_context($context)->export_data([], (object) [
                "textcontent" => format_text($entry->textcontent, $entry->textformat, ["context" => $context]),
                "timecreated" => transform::datetime($entry->timecreated),
                "timemodified" => transform::datetime($entry->timemodified),
            ]);
        }
    }

    /**
     * Deletes all entries in a module context.
     *
     * @param \context $context Context.
     * @return void
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id("simplejournal", $context->instanceid);
        if ($cm) {
            $DB->delete_records("simplejournal_entries", ["journalid" => $cm->instance]);
        }
    }

    /**
     * Deletes one user's data in approved contexts.
     *
     * @param approved_contextlist $contextlist Approved contexts.
     * @return void
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof \context_module) {
                continue;
            }
            $cm = get_coursemodule_from_id("simplejournal", $context->instanceid);
            if ($cm) {
                $DB->delete_records("simplejournal_entries", [
                    "journalid" => $cm->instance,
                    "userid" => $userid,
                ]);
            }
        }
    }

    /**
     * Adds users with stored data in a context.
     *
     * @param userlist $userlist User list.
     * @return void
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }

        $sql = "SELECT e.userid
                  FROM {simplejournal_entries} e
                  JOIN {course_modules} cm ON cm.instance = e.journalid
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                 WHERE cm.id = :cmid";
        $userlist->add_from_sql("userid", $sql, [
            "modname" => "simplejournal",
            "cmid" => $context->instanceid,
        ]);
    }

    /**
     * Deletes data for approved users in one context.
     *
     * @param approved_userlist $userlist Approved user list.
     * @return void
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();
        if (!$context instanceof \context_module) {
            return;
        }

        $cm = get_coursemodule_from_id("simplejournal", $context->instanceid);
        if (!$cm) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        [$insql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params["journalid"] = $cm->instance;
        $DB->delete_records_select("simplejournal_entries", "journalid = :journalid AND userid $insql", $params);
    }
}
