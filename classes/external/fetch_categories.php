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
 * Methods for fetching categories from the DB to the editor.
 *
 * @category    database
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_styles\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use external_iterator;
use stdClass;
use context_system;

class fetch_categories extends external_api {

    /**
     * Returns the external function parameters required by execute().
     * Additional parameters can be defined later.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Fetches and returns all enabled categories and associated elements.
     *
     * @return array Nested array of categories with associated elements
     */
    public static function execute() {
        global $DB;

        $context = context_system::instance();
        self::validate_context($context);

        $sql = "SELECT c.id, c.name, c.symbol, c.presentation,
            e.id AS elemid, e.name AS elemname, e.type, e.cssclasses, e.custom
        FROM {tiny_styles_categories} c
        LEFT JOIN {tiny_styles_cat_elements} ce ON ce.categoryid = c.id
        LEFT JOIN {tiny_styles_elements} e ON e.id = ce.elementid
        WHERE c.enabled = 1 
        AND (e.enabled = 1 OR c.presentation = 'divider')
        ORDER BY c.sortorder, ce.sortorder";
        $rs = $DB->get_recordset_sql($sql);

        $cats = [];
        foreach ($rs as $r) {
            $cid = $r->id;
            if (!isset($cats[$cid])) {
                $cats[$cid] = [
                    'id' => $cid,
                    'name' => $r->name,
                    'symbol' => $r->symbol,
                    'presentation' => $r->presentation,
                    'elements' => [],
                ];
            }
            if (!empty($r->elemid)) {
                $cats[$cid]['elements'][] = [
                    'id'         => $r->elemid,
                    'name'       => $r->elemname,
                    'type'       => $r->type,
                    'cssclasses' => $r->cssclasses,
                    'custom'     => $r->custom,
                ];
            }
        }
        $rs->close();
        $results = array_values($cats);

        return $results;
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'           => new external_value(PARAM_INT, 'Category ID'),
                'name'         => new external_value(PARAM_TEXT, 'Category name'),
                'symbol'       => new external_value(PARAM_RAW,  'Optional FA symbol', VALUE_OPTIONAL),
                'presentation' => new external_value(PARAM_TEXT, 'divider/submenu/inline/'),
                'elements'     => new external_multiple_structure(
                    new external_single_structure([
                        'id'         => new external_value(PARAM_INT, 'Element ID'),
                        'name'       => new external_value(PARAM_TEXT, 'Element name'),
                        'type'       => new external_value(PARAM_TEXT, 'inline/block'),
                        'cssclasses' => new external_value(PARAM_RAW,  'e.g. "alert alert-info"'),
                        'custom'     => new external_value(PARAM_INT , 'Custom CSS class boolean'),
                    ]),
                    'list of bridging elements',
                    VALUE_OPTIONAL
                ),
            ])
        );
    }
}
