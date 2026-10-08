# Simple Journal — Marketplace listing text

## Short description

Private reflective journals for Moodle: each student maintains one editable reflection per activity, while authorised teachers review entries individually.

## Full description

Simple Journal adds a lightweight reflection activity to Moodle courses. Teachers invite learners to write personal journals, learning reflections or short self-assessments without using graded assignments.

Each activity stores one private entry per student. Learners write and revise their reflection with Moodle's built-in rich-text editor; returning to the activity opens their saved entry. Other students cannot view it.

Teachers with permission to read student reflections open the report, select an enrolled student, and review the text and its creation and last-modified dates.

### Main features

- One private, editable reflection per student per activity.
- Moodle rich-text editor without file attachments.
- Individual teacher report with a student selector.
- Creation and modification timestamps.
- Activity-view completion tracking.
- Backup and restore of entries, when user data is included.
- Moodle Privacy API support.
- No grades, rubrics or file submissions.

### Settings and permissions

A teacher provides the activity name and optional instructions using standard Moodle course-module settings. There are no additional journal-specific settings. Capabilities control creation (`mod/simplejournal:addinstance`), activity access (`mod/simplejournal:view`), writing (`mod/simplejournal:submit`) and teacher reports (`mod/simplejournal:viewall`).

### Requirements

Moodle 4.5 or later.

## Images already prepared for review

- [Image 1](https://github.com/EduardoKrausME/marketplace-plugins/blob/master/screenshots/mod_simplejournal/new-1.png)
- [Image 2](https://github.com/EduardoKrausME/marketplace-plugins/blob/master/screenshots/mod_simplejournal/new-2.png)

Check that these existing assets accurately show the plugin before using them. The Marketplace needs actual in-use screenshots uploaded to the listing; image links in the source repository do not count as published screenshots.

## Listing actions still required

1. Replace the Marketplace short and full descriptions with the English text above.
2. Upload suitable screenshots of the student journal and teacher report to the Marketplace listing.
3. Use the `simplejournal.zip` GitHub Release asset for the published ZIP package, rather than GitHub's automatically generated source-code archive; the release asset has the required `simplejournal/` root directory.
4. Verify the Marketplace listing and published ZIP before closing publication-related issues.
