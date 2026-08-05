<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration page
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

$pageurl = new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php');
$PAGE->set_url($pageurl);
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'tiny_styles'));
$PAGE->set_heading(get_string('pluginname', 'tiny_styles'));

$PAGE->requires->js_call_amd('tiny_styles/sortcategories', 'init');
$PAGE->requires->js_call_amd('tiny_styles/toggle_category', 'init');
$PAGE->requires->js_call_amd('tiny_styles/confirm_delete', 'init');

$tinystylesaction = optional_param('tiny_styles_action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Handle delete action.
if ($tinystylesaction === 'delete' && $id > 0) {
    require_sesskey();
    require_capability('moodle/site:config', context_system::instance());

    try {
        tiny_styles_delete_category_with_cleanup($id);
        redirect(
            $pageurl,
            get_string('categorydeleted', 'tiny_styles'),
            1
        );
    } catch (Exception $e) {
        redirect(
            $pageurl,
            get_string('error') . ': ' . $e->getMessage(),
            1,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    exit;
}

// Handle export action.
if ($tinystylesaction === 'export') {
    require_once(__DIR__ . '/exporthandler.php');
    exit;
}

global $OUTPUT;

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('categories', 'tiny_styles'));

// Get all categories from database.
$records = tiny_styles_get_all_categories();
$categorydata = [];

// Categories prepared for the template.
foreach ($records as $category) {
    $moveupiconhtml = html_writer::link(
        '#',
        $OUTPUT->pix_icon('t/up', get_string('moveup')),
        [
            'class'       => 'moveup text-primary text-decoration-none',
            'data-action' => 'moveup',
            'data-id'     => $category->id,
            'title'       => get_string('moveup'),
        ]
    );

    $movedowniconhtml = html_writer::link(
        '#',
        $OUTPUT->pix_icon('t/down', get_string('movedown')),
        [
            'class'       => 'movedown text-primary text-decoration-none',
            'data-action' => 'movedown',
            'data-id'     => $category->id,
            'title'       => get_string('movedown'),
        ]
    );

    $deleteurl = new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php', [
        'tiny_styles_action' => 'delete',
        'id'                 => $category->id,
        'sesskey'            => sesskey(),
    ]);

    $deleteiconhtml = html_writer::link(
        $deleteurl,
        $OUTPUT->pix_icon('t/delete', get_string('delete')),
        [
            'class'        => 'text-primary text-decoration-none',
            'title'        => get_string('delete'),
            'aria-label'   => get_string('delete'),
            'data-action'  => 'confirm-delete',
            'data-confirm' => get_string('confirmdeletecategory', 'tiny_styles'),
        ]
    );

    // Filtering dividers out.
    if ($category->menumode === 'divider') {
        $categorydata[] = [
            'id' => $category->id,
            'enabled' => $category->enabled,
            'name' => tiny_styles_format_name($category->name),
            'description' => tiny_styles_format_name($category->description),
            'menumode' => $category->menumode,
            'elementsurl' => '#',
            'isdivider' => true,
            'moveupiconhtml'  => $moveupiconhtml,
            'movedowniconhtml' => $movedowniconhtml,
            'editurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
                'tiny_styles_action' => 'edit', 'id' => $category->id,
            ]))->out(false),
            'deleteiconhtml' => $deleteiconhtml,
        ];
    } else {
        $categorydata[] = [
            'id' => $category->id,
            'enabled' => $category->enabled,
            'name' => tiny_styles_format_name($category->name),
            'description' => tiny_styles_format_name($category->description),
            'menumode' => $category->menumode,
            'elementsurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                'catid' => $category->id,
            ]))->out(false),
            'isdivider' => false,
            'moveupiconhtml'  => $moveupiconhtml,
            'movedowniconhtml' => $movedowniconhtml,
            'editurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
                'tiny_styles_action' => 'edit', 'id' => $category->id,
            ]))->out(false),
            'deleteiconhtml' => $deleteiconhtml,
        ];
    }
}

$createcaturl = new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
    'tiny_styles_action' => 'create',
]);
$exporturl = new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php', [
    'tiny_styles_action' => 'export',
    'sesskey' => sesskey(),
]);
$importurl = new moodle_url('/lib/editor/tiny/plugins/styles/import.php');

$templatecontext = [
    'categories'     => $categorydata,
    'createcaturl'   => $createcaturl->out(false),
    'createcategory' => get_string('createcategory', 'tiny_styles'),
    'exporturl'      => $exporturl->out(false),
    'exportlabel'    => get_string('exportdata', 'tiny_styles'),
    'importurl'      => $importurl->out(false),
    'importlabel'    => get_string('importdata', 'tiny_styles'),
];

$tablehtml = $OUTPUT->render_from_template('tiny_styles/categorytable', $templatecontext);

echo $tablehtml;

echo $OUTPUT->footer();
