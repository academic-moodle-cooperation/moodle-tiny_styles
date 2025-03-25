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
 * @package     tiny_styles
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');
require_login();
require_sesskey();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Category id for bridging table query.
$catid = required_param('catid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', ['catid' => $catid]));
$PAGE->set_title(get_string('elementstitle', 'tiny_styles'));

// Helper method for selecting all checkboxes.
$PAGE->requires->js_call_amd('tiny_styles/select_all', 'init');
$PAGE->requires->js_call_amd('tiny_styles/preview_element', 'init', ['a.element-preview-link']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redirect(
        new moodle_url('/admin/settings.php',
        ['section' => 'tiny_styles_admin'])
    );
}

global $DB, $OUTPUT;
$sql = "SELECT e.* 
          FROM {tiny_styles_elements} e
          JOIN {tiny_styles_cat_elements} ce ON ce.elementid = e.id
         WHERE ce.categoryid = :catid
         ORDER BY ce.sortorder ASC, e.id";
$params = ['catid' => $catid];

$records = $DB->get_records_sql($sql, $params);

// Array of elements for mustache template.
$elements = [];
foreach ($records as $r) {

    // Moving up and down methods.
    $moveupurl = new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid'   => $catid,
        'action'  => 'moveup',
        'id'      => $r->id,
        'sesskey' => sesskey()
    ]);

    $moveupiconhtml = $OUTPUT->action_icon(
        $moveupurl,
        new pix_icon('t/up', get_string('moveup'))
    );

    $movedownurl = new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid'   => $catid,
        'action'  => 'movedown',
        'id'      => $r->id,
        'sesskey' => sesskey()
    ]);
    $movedowniconhtml = $OUTPUT->action_icon(
        $movedownurl,
        new pix_icon('t/down', get_string('movedown'))
    );

    // Delete url for delete action.
    $deleteurl = new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid'   => $catid,
        'action'  => 'delete',
        'id'      => $r->id,
        'sesskey' => sesskey()
    ]);

    $deleteiconhtml = $OUTPUT->action_icon(
        $deleteurl,
        new pix_icon('t/delete', get_string('delete')),
        new confirm_action(get_string('confirmdeleteelement', 'tiny_styles')),
        ['title' => get_string('delete')]
    );

    // Enable disable action.
    $enabled = (int)$r->enabled;
    if ($enabled) {
        $eyeiconname = 't/hide';
        $eyealt = get_string('hide');
    } else {
        $eyeiconname = 't/show';
        $eyealt = get_string('show');
    }

    $toggleenableurl = new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid'   => $catid,
        'action'  => 'toggleenable',
        'id'      => $r->id,
        'sesskey' => sesskey()
    ]);

    $toggleiconhtml = $OUTPUT->action_icon(
        $toggleenableurl,
        new pix_icon($eyeiconname, $eyealt)
    );

    $viewdetailsattrs = [
        'href'           => '#',
        'class'          => 'element-preview-link',
        'data-name'      => $r->name,
        'data-cssclass'  => $r->cssclasses,
        'data-type'      => $r->type
    ];

    $viewdetailsicon = new pix_icon('i/preview', get_string('preview', 'tiny_styles'));

    $viewdetailshtml = $OUTPUT->action_icon('#', $viewdetailsicon, null, $viewdetailsattrs);


    $elements[] = [
        'id'             => $r->id,
        'name'           => $r->name,
        'type'           => $r->type,
        'bootstrapclass' => $r->cssclasses,
        'toggleiconhtml' => $toggleiconhtml,
        'viewdetailsiconhtml' => $viewdetailshtml,
        'moveupiconhtml'   => $moveupiconhtml,
        'movedowniconhtml' => $movedowniconhtml,
        'editurl'        => (new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
            'action' => 'edit',
            'id'     => $r->id,
            'catid' => $catid,
        ]))->out(false),
        'deleteiconhtml' => $deleteiconhtml,
    ];
}

$templatecontext = [
    'heading'           => get_string('elementsheading', 'tiny_styles'),
    'navigateback'      => get_string('back_overview', 'tiny_styles'),
    'createbuttonlabel' => get_string('create_element', 'tiny_styles'),
    'createelementurl'  => (new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', ['catid' => $catid]))->out(false),
    'submiturl'         => (new moodle_url('/lib/editor/tiny/plugins/styles/elements.php',
        ['catid' => $catid,
        'sesskey' => sesskey()
        ]))->out(false),
    'elements'          => $elements,
];


if ($action === 'delete' && $id > 0) {
    confirm_sesskey();

    $DB->delete_records('tiny_styles_elements', ['id' => $id]);
    $DB->delete_records('tiny_styles_cat_elements', ['elementid' => $id]);

    redirect(
        new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
            'catid' => $catid,
            'sesskey' => sesskey()
        ]),
        get_string('elementdeleted', 'tiny_styles'),
        1
    );
    exit;

} else if ($action === 'moveup' && $id > 0) {
    confirm_sesskey();
    move_element_up($catid, $id);
    redirect(new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid' => $catid,
        'sesskey' => sesskey()
    ]));
    exit;
} else if ($action === 'movedown' && $id > 0) {
    confirm_sesskey();
    move_element_down($catid, $id);
    redirect(new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
        'catid' => $catid,
        'sesskey' => sesskey()
    ]));
    exit;
} else if ($action === 'toggleenable' && $id > 0) {
    confirm_sesskey();

    $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
    $element->enabled = $element->enabled ? 0 : 1;
    $DB->update_record('tiny_styles_elements', $element);

    redirect(
        new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
            'catid' => $catid,
            'sesskey' => sesskey()
        ])
    );
    exit;
}

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('tiny_styles/elements_table', $templatecontext);
echo $OUTPUT->footer();
