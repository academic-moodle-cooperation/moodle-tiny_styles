<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_tiny_styles_upgrade($oldversion = 0) {
    global $DB;

    $dbman = $DB->get_manager(); 

    if ($oldversion < 2025022701) {
        $table = new xmldb_table('tiny_styles_elements');
        $field = new xmldb_field('custom', XMLDB_TYPE_INT, '1', null, XMLDB_NOTNULL, null, '0', 'enabled');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025022701, 'tiny', 'styles');
    }

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

    return true;
}

