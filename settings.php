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

if ($hassiteconfig) {
    $settingspage = new admin_settingpage(
        'tiny_styles_admin',
        get_string('tiny_styles_admin', 'tiny_styles')
    );

    if ($ADMIN->fulltree) {
        global $DB, $OUTPUT;

        $records = $DB->get_records('tiny_styles_categories', null, 'sortorder ASC');
        $categorydata = [];

        // todo: filtering for table link visibility
        foreach ($records as $cat) {
            $categorydata[] = [
                'name'        => $cat->name,
                'description' => $cat->description,
                'presentation'=> $cat->presentation,
                'viewurl'     => '#',
                'elementsurl' => (new moodle_url('/lib/editor/tiny/plugins/styles/elements.php', [
                    'catid' => $cat->id
                ]))->out(false),
                'moveupurl'   => '#',
                'movedownurl' => '#',
                'editurl'     => (new moodle_url('/lib/editor/tiny/plugins/styles/category.php', [
                    'action' => 'edit', 'id' => $cat->id
                ]))->out(false),
                'deleteurl'   => '#',
            ];
        }

        // to create a new category
        $createcaturl = new moodle_url('/lib/editor/tiny/plugins/styles/category.php', ['action'=>'create']);

        // mustache
        $templatecontext = [
            'categories'     => $categorydata,
            'createcaturl'   => $createcaturl->out(false),
            'createcategory' => get_string('createcategory', 'tiny_styles'),
        ];

        $tablehtml = $OUTPUT->render_from_template('tiny_styles/categorytable', $templatecontext);

        $headingcontent  = html_writer::tag('h3', get_string('categories', 'tiny_styles'));
        $headingcontent .= $tablehtml;

        $settingspage->add(new admin_setting_heading('tiny_styles_categorieslist', '', $headingcontent));
    }

    $ADMIN->add('editortiny', $settingspage);
}