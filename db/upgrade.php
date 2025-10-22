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

    return true;
}
