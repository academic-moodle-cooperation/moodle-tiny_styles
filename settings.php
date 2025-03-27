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
 * @package     tiny_styles
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');


$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$PAGE->requires->js_call_amd('tiny_styles/importdata', 'init');

// todo: move up/down, delete and toggle enable to ajax implementation.
if ($action === 'moveup' && $id > 0) {
    require_sesskey();
    require_capability('moodle/site:config', context_system::instance());

    move_category_up($id);
    redirect(new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']));
    exit;

} else if ($action === 'movedown' && $id > 0) {
    require_sesskey();
    require_capability('moodle/site:config', context_system::instance());

    move_category_down($id);
    redirect(new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']));
    exit;

} else if ($action === 'delete' && $id > 0) {
    require_sesskey();
    require_capability('moodle/site:config', context_system::instance());

    // All bridging records for category.
    $bridges = $DB->get_records('tiny_styles_cat_elements', ['categoryid' => $id]);

    // Check each element in this category if used elsewhere.
    foreach ($bridges as $bridge) {
        $count = $DB->count_records('tiny_styles_cat_elements', ['elementid' => $bridge->elementid]);
        if ($count <= 1) {
            // Delete element if it is only associated with this category.
            $DB->delete_records('tiny_styles_elements', ['id' => $bridge->elementid]);
        }
    }

    // Delete category and all bridging records for this category.
    $DB->delete_records('tiny_styles_cat_elements', ['categoryid' => $id]);
    $DB->delete_records('tiny_styles_categories', ['id' => $id]);

    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin']),
        get_string('categorydeleted', 'tiny_styles'),
        1
    );
    exit;

} else if ($action === 'toggleenable' && $id > 0) {
    require_sesskey();
    require_capability('moodle/site:config', context_system::instance());

    $category = $DB->get_record('tiny_styles_categories', ['id' => $id], '*', MUST_EXIST);
    $category->enabled = $category->enabled ? 0 : 1;
    $DB->update_record('tiny_styles_categories', $category);

    redirect(
        new moodle_url('/admin/settings.php', ['section' => 'tiny_styles_admin'])
    );
    exit;
} else if ($action === 'export') {
    require_once(__DIR__ . '/exporthandler.php');
    exit;

}else if ($action === 'import') {
    require_once(__DIR__ . '/importhandler.php');
    exit;
}

if ($hassiteconfig) {

    $settingspage = new admin_settingpage('tiny_styles_admin', get_string('tiny_styles_admin', 'tiny_styles'));

    if ($ADMIN->fulltree) {
        global $DB, $OUTPUT;

        $records = $DB->get_records('tiny_styles_categories', null, 'sortorder ASC');
        $categorydata = [];

        // Categories prepared for the template.
        foreach ($records as $category) {

            // Move up/down actions.
            $moveupurl = new moodle_url('/admin/settings.php', [
                'section' => 'tiny_styles_admin',
                'action'  => 'moveup',
                'id'      => $category->id,
                'sesskey' => sesskey()
            ]);
            $moveupiconhtml = $OUTPUT->action_icon(
                $moveupurl,
                new pix_icon('t/up', get_string('moveup'))
            );

            $movedownurl = new moodle_url('/admin/settings.php', [
                'section' => 'tiny_styles_admin',
                'action'  => 'movedown',
                'id'      => $category->id,
                'sesskey' => sesskey()
            ]);
            $movedowniconhtml = $OUTPUT->action_icon(
                $movedownurl,
                new pix_icon('t/down', get_string('movedown'))
            );

            $deleteurl = new moodle_url('/admin/settings.php', [
                'section' => 'tiny_styles_admin',
                'action'  => 'delete',
                'id'      => $category->id,
                'sesskey' => sesskey()
            ]);

            $confirmstring = get_string('confirmdeletecategory', 'tiny_styles');

            // Delete icon with a confirm action.
            $deleteiconhtml = $OUTPUT->action_icon(
                $deleteurl,
                new pix_icon('t/delete', get_string('delete')),
                new confirm_action($confirmstring),
                ['title' => get_string('delete')]
            );

            // Enable/disable icon.
            $enabled = (int)$category->enabled;

            if ($enabled) {
                $eyeiconname = 't/hide';
                $eyealt = get_string('hide');
            } else {
                $eyeiconname = 't/show';
                $eyealt = get_string('show');
            }

            $toggleurl = new moodle_url('/admin/settings.php', [
                'section' => 'tiny_styles_admin',
                'action'  => 'toggleenable',
                'id'      => $category->id,
                'sesskey' => sesskey()
            ]);

            $toggleiconhtml = $OUTPUT->action_icon(
                $toggleurl,
                new pix_icon($eyeiconname, $eyealt)
            );

            // Filtering dividers out.
            if ($category->presentation === 'divider') {
                $categorydata[] = [
                    'name' => $category->name,
                    'description' => $category->description,
                    'presentation' => $category->presentation,
                    'toggleiconhtml' => $toggleiconhtml,
                    'elementsurl' => '#',
                    'moveupiconhtml'  => $moveupiconhtml,
                    'movedowniconhtml'=> $movedowniconhtml,
                    'editurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
                        'action' => 'edit', 'id' => $category->id
                    ]))->out(false),
                    'deleteiconhtml' => $deleteiconhtml,
                ];
            } else {
                $categorydata[] = [
                    'name' => $category->name,
                    'description' => $category->description,
                    'presentation' => $category->presentation,
                    'toggleiconhtml' => $toggleiconhtml,
                    'elementsurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                        'catid' => $category->id,
                    ]))->out(false),
                    'moveupiconhtml'  => $moveupiconhtml,
                    'movedowniconhtml'=> $movedowniconhtml,
                    'editurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
                        'action' => 'edit', 'id' => $category->id
                    ]))->out(false),
                    'deleteiconhtml' => $deleteiconhtml,
                ];
            }
        }

        $createcaturl = new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
            'action' => 'create',
        ]);
        $exporturl    = new moodle_url('/admin/settings.php', [
            'section' => 'tiny_styles_admin',
            'action' => 'export',
            'sesskey' => sesskey(),
        ]);
        $importurl = new moodle_url('/admin/settings.php', [
            'section' => 'tiny_styles_admin',
            'action'  => 'import',
            'sesskey' => sesskey(),
        ]);

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

        $headingcontent  = html_writer::tag('h3', get_string('categories', 'tiny_styles'));
        $headingcontent .= $tablehtml;

        $settingspage->add(new admin_setting_heading('tiny_styles_categorieslist', '', $headingcontent));
    }
    $ADMIN->add('editortiny', $settingspage);
    $settings = 0;
}
