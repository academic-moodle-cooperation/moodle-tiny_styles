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
 * Plugin administration elements
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Category id for bridging table query.
$catid = required_param('catid', PARAM_INT);
$tinystylesaction = optional_param('tiny_styles_action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', ['catid' => $catid]));
$PAGE->set_title(get_string('elementstitle', 'tiny_styles'));

// Javascript helpers.
$PAGE->requires->js_call_amd('tiny_styles/select_all', 'init');
$PAGE->requires->js_call_amd('tiny_styles/preview_element', 'init', ['a.element-preview-link']);
$PAGE->requires->js_call_amd('tiny_styles/sortelements', 'init');
$PAGE->requires->js_call_amd('tiny_styles/duplicate', 'init');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redirect(
        new moodle_url('/lib/editor/tiny/plugins/styles/categorysettings.php')
    );
}

global $OUTPUT;

// Get elements and category name from database.
$records = tiny_styles_get_elements_by_category($catid);
$catname = format_string(tiny_styles_get_category_name($catid), true, ['context' => context_system::instance()]);

// Array of elements for mustache template.
$elements = [];
foreach ($records as $r) {
    $moveupiconhtml = html_writer::tag(
        'button',
        $OUTPUT->pix_icon('t/up', get_string('moveup')),
        [
            'type'        => 'button',
            'class'       => 'move-up btn-icon text-primary',
            'data-id'     => $r->id,
            'title'       => get_string('moveup'),
            'style'       => 'background: none; border: none; cursor: pointer; padding: 0;',
        ]
    );

    $movedowniconhtml = html_writer::tag(
        'button',
        $OUTPUT->pix_icon('t/down', get_string('movedown')),
        [
            'type'        => 'button',
            'class'       => 'move-down btn-icon text-primary',
            'data-id'     => $r->id,
            'title'       => get_string('movedown'),
            'style'       => 'background: none; border: none; cursor: pointer; padding: 0;',
        ]
    );

    $deleteurl = new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid'   => $catid,
        'tiny_styles_action'  => 'delete',
        'id'      => $r->id,
        'sesskey' => sesskey(),
    ]);

    $deleteiconhtml = $OUTPUT->action_icon(
        $deleteurl,
        new pix_icon('t/delete', get_string('delete')),
        new confirm_action(get_string('confirmdeleteelement', 'tiny_styles')),
        ['title' => get_string('delete')],
    );

    $viewdetailsattrs = [
        'href'           => '#',
        'class'          => 'element-preview-link',
        'data-name'      => format_string($r->name, true, ['context' => context_system::instance()]),
        'data-cssclass'  => $r->cssclasses,
        'data-type'      => $r->type,
    ];

    $viewdetailsicon = new pix_icon('i/preview', get_string('preview', 'tiny_styles'));
    $viewdetailshtml = $OUTPUT->action_icon('#', $viewdetailsicon, null, $viewdetailsattrs);

    $elements[] = [
        'id'             => $r->id,
        'name'           => format_string($r->name, true, ['context' => context_system::instance()]),
        'type'           => $r->type,
        'bootstrapclass' => $r->cssclasses,
        'enabled' => (bool)$r->enabled,
        'viewdetailsiconhtml' => $viewdetailshtml,
        'moveupiconhtml'   => $moveupiconhtml,
        'movedowniconhtml' => $movedowniconhtml,
        'editurl'        => (new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
            'tiny_styles_action' => 'edit',
            'id'     => $r->id,
            'catid' => $catid,
        ]))->out(false),
        'deleteiconhtml' => $deleteiconhtml,
    ];
}

$templatecontext = [
    'heading'           => get_string('elementsheading', 'tiny_styles'),
    'category'          => $catname,
    'navigateback'      => get_string('back_overview', 'tiny_styles'),
    'createbuttonlabel' => get_string('create_element', 'tiny_styles'),
    'createelementurl'  => (
        new moodle_url(
            '/lib/editor/tiny/plugins/styles/create_element.php',
            ['catid' => $catid]
        )
    )->out(false),
    'submiturl'         => (
        new moodle_url(
            '/lib/editor/tiny/plugins/styles/elements.php',
            [
            'catid' => $catid,
            'sesskey' => sesskey(),
            ]
        )
    )->out(false),
    'elements'          => $elements,
    'bulkactionslabel' => get_string('withselection', 'tiny_styles'),
    'selectdefault'    => get_string('selectdefault', 'tiny_styles'),
    'showaction'       => get_string('showaction', 'tiny_styles'),
    'hideaction'       => get_string('hideaction', 'tiny_styles'),
    'duplicateaction'  => get_string('duplicateaction', 'tiny_styles'),
    'deleteaction'     => get_string('deleteaction', 'tiny_styles'),
];


// Handle delete action.
if ($tinystylesaction === 'delete' && $id > 0) {
    confirm_sesskey();

    try {
        tiny_styles_delete_element_with_bridges($id);
        redirect(
            new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                'catid' => $catid,
                'sesskey' => sesskey(),
            ]),
            get_string('elementdeleted', 'tiny_styles'),
            1
        );
    } catch (Exception $e) {
        redirect(
            new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', ['catid' => $catid]),
            get_string('error') . ': ' . $e->getMessage(),
            1,
            \core\output\notification::NOTIFY_ERROR
        );
    }
    exit;
}

// Info message.
$bulkmessage = '';
if (isset($_SESSION['tiny_styles_bulk_message'])) {
    $bulkmessage = $_SESSION['tiny_styles_bulk_message'];
    unset($_SESSION['tiny_styles_bulk_message']);
}

if (!empty($bulkmessage)) {
    $templatecontext['has_bulk_message'] = true;
    $templatecontext['bulk_message'] = $bulkmessage;
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('tiny_styles/elements_table', $templatecontext);
$PAGE->requires->js_call_amd('tiny_styles/toggle_element', 'init');
$PAGE->requires->js_call_amd('tiny_styles/bulk_actions', 'init');
echo $OUTPUT->footer();
