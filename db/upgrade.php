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
 * Upgrade file for Moodle tiny_styles plugin.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Applies necessary db updates with newer versions.
 *
 * @param mixed $oldversion Previously installed plugin version.
 * @return bool
 */
function xmldb_tiny_styles_upgrade($oldversion = 0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025041002) {
        $table = new xmldb_table('tiny_styles_categories');

        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, '400', null, false, null, null, 'name');
        $dbman->change_field_precision($table, $field);

        $table = new xmldb_table('tiny_styles_elements');

        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_precision($table, $field);

        upgrade_plugin_savepoint(true, 2025041002, 'tiny', 'styles');
    }

    if ($oldversion < 2025041500) {
        $table = new xmldb_table('tiny_styles_elements');
        $field = new xmldb_field('cssclasses', XMLDB_TYPE_CHAR, '1024', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_precision($table, $field);
        }
        upgrade_plugin_savepoint(true, 2025041500, 'tiny', 'styles');
    }

    if ($oldversion < 2025073002) {
        $table = new xmldb_table('tiny_styles_categories');
        $field = new xmldb_field('presentation', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'submenu');

        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'menumode');
        }
        upgrade_plugin_savepoint(true, 2025073002, 'tiny', 'styles');
    }

    // Increases the input limits for allowing multilang filtering.
    if ($oldversion < 2025080603) {
        $table = new xmldb_table('tiny_styles_categories');

        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, '1000', null, false, null, null, 'name');
        $dbman->change_field_precision($table, $field);

        $table = new xmldb_table('tiny_styles_elements');
        $field = new xmldb_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'id');
        $dbman->change_field_precision($table, $field);

        upgrade_plugin_savepoint(true, 2025080603, 'tiny', 'styles');
    }

    // Migrate category symbols from *.svg filenames to raw FA icon names.
    if ($oldversion < 2026012201.02) {
        $symbolmap = [
            'label'    => 'tag',
            'box'      => 'table-list',
            'default'  => 'star',
            'preview'  => 'eye',
            'paint'    => 'palette',
            'check'    => 'check',
            'graduate' => 'graduation-cap',
            'laptop'   => 'laptop',
            'magnify'  => 'magnifying-glass',
            'pen'      => 'pen',
            'school'   => 'school',
            'square'   => 'square',
            'flag'     => 'flag',
            'brush'    => 'paintbrush',
            'info'     => 'circle-info',
            'download' => 'download',
            'book'     => 'book',
            'folder'   => 'folder',
            'remove'   => 'xmark',
        ];

        $categories = $DB->get_records('tiny_styles_categories', null, '', 'id, symbol');
        foreach ($categories as $cat) {
            $symbol = $cat->symbol ?? '';
            if (empty($symbol) || !str_ends_with($symbol, '.svg')) {
                continue;
            }
            $base = str_replace('.svg', '', $symbol);
            $faname = $symbolmap[$base] ?? $base;
            $DB->set_field('tiny_styles_categories', 'symbol', $faname, ['id' => $cat->id]);
        }

        // Ensures the default Boxes category has showdesc set for existing installs.
        $DB->set_field('tiny_styles_categories', 'showdesc', 'helptext', ['name' => 'Boxes']);

        upgrade_plugin_savepoint(true, 2026012201.02, 'tiny', 'styles');
    }

    return true;
}
