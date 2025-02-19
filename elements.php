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
require_login();

$context = context_system::instance();
require_capability('moodle/site:config', $context);
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');

// Category id for bridging table query.
$catid = required_param('catid', PARAM_INT);

$PAGE->set_url(new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', ['catid' => $catid]));
$PAGE->set_title(get_string('elementstitle', 'tiny_styles'));

// Helper method for selecting all checkboxes.
$PAGE->requires->js_call_amd('tiny_styles/select_all', 'init');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    redirect(
        new moodle_url('/admin/settings.php',
        ['section' => 'tiny_styles_admin']),
        get_string('elements_updated', 'tiny_styles'), 2
    );
}

global $DB, $OUTPUT;
$sql = "SELECT e.* 
          FROM {tiny_styles_elements} e
          JOIN {tiny_styles_cat_elements} ce ON ce.elementid = e.id
         WHERE ce.categoryid = :catid
         ORDER BY ce.sortorder, e.sortorder, e.id";
$params = ['catid' => $catid];

$records = $DB->get_records_sql($sql, $params);

// Array of elements for mustache template.
$elements = [];
foreach ($records as $r) {
    $elements[] = [
        'id'             => $r->id,
        'name'           => $r->name,
        'type'           => $r->type,
        'bootstrapclass' => $r->cssclasses,
        'viewurl'        => '#',
        'viewdetailsurl' => '#',
        'moveupurl'      => '#',
        'movedownurl'    => '#',
        'editurl'        => (new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', [
            'action' => 'edit',
            'id'     => $r->id,
            'catid' => $catid,
        ]))->out(false),

        'deleteurl'      => '#',
    ];
}

$templatecontext = [
    'heading'           => get_string('elementsheading', 'tiny_styles'),
    'navigateback'      => get_string('back_overview', 'tiny_styles'),
    'createbuttonlabel' => get_string('create_element', 'tiny_styles'),
    'createelementurl'  => (new moodle_url('/lib/editor/tiny/plugins/styles/create_element.php', ['catid' => $catid]))->out(false),
    'submiturl'         => (new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', ['catid' => $catid]))->out(false),
    'elements'          => $elements,
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('tiny_styles/elements_table', $templatecontext);
echo $OUTPUT->footer();
