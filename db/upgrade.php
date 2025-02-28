<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_tiny_styles_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); 

    if ($oldversion < 2025022701) {
        $table = new xmldb_table('tiny_styles_elements');
        $field = new xmldb_field('custom', XMLDB_TYPE_INT, '1', null, XMLDB_NOTNULL, null, '0', 'enabled');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025022701, 'tiny_styles');
    }

    return true;
}

