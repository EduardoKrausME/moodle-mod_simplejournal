# Simple Journal (mod_simplejournal)

Simple Journal is a private reflective-writing activity for Moodle. Students maintain one editable entry per activity, and authorised teachers can read individual reflections.

## How it works

A teacher adds a Simple Journal activity and provides its name and optional instructions. Each student writes and saves a reflection using Moodle's built-in text editor; returning to the activity opens the same entry for editing rather than creating a second submission.

Teachers with `mod/simplejournal:viewall` open the student-reflections report, select an enrolled student, and see the saved text and its creation and last-modified dates. Students cannot access one another's journal entries.

## Features

- One private, editable reflection per student per activity.
- Moodle rich-text editor with file attachments disabled.
- Per-student teacher report.
- Entry creation and last-modified timestamps.
- Activity view tracking for Moodle completion.
- Backup and restore including user entries when included in the backup.
- Moodle Privacy API support.
- No grades, file submissions or grading workflows.

## Permissions and configuration

The activity uses standard Moodle course-module settings for its name, description and course completion; it has no additional journal-specific configuration fields.

- `mod/simplejournal:addinstance`: add a journal activity.
- `mod/simplejournal:view`: open the activity.
- `mod/simplejournal:submit`: write or edit one's own reflection.
- `mod/simplejournal:viewall`: read entries in the teacher report.

## Requirements

Moodle 4.5 or later.

## Screenshot resources

Two existing image assets for the Marketplace listing are available in the [Simple Journal screenshot folder](https://github.com/EduardoKrausME/marketplace-plugins/tree/master/screenshots/mod_simplejournal). Screenshots must also be uploaded to the actual Marketplace listing; displaying them in a GitHub repository does not populate the listing.
