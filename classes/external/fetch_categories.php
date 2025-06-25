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
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

        $sql = "SELECT c.id, c.name, c.symbol, c.presentation, c.description,
            e.id AS elemid, e.name AS elemname, e.type, e.cssclasses, e.custom
        FROM {tiny_styles_categories} c
        LEFT JOIN {tiny_styles_cat_elements} ce ON ce.categoryid = c.id
        LEFT JOIN {tiny_styles_elements} e ON e.id = ce.elementid
        WHERE c.enabled = 1 
        AND (e.enabled = 1 OR c.presentation = 'divider')
        ORDER BY c.sortorder, ce.sortorder";
        $recordset = $DB->get_recordset_sql($sql);

        $categories = [];
        foreach ($recordset as $r) {
            $catid = $r->id;
            if (!isset($categories[$catid])) {
                $categories[$catid] = [
                    'id' => $catid,
                    'name' => $r->name,
                    'symbol' => $r->symbol,
                    'description' => $r->description,
                    'presentation' => $r->presentation,
                    'elements' => [],
                ];
            }
            if (!empty($r->elemid)) {
                $categories[$catid]['elements'][] = [
                    'id'         => $r->elemid,
                    'name'       => $r->elemname,
                    'type'       => $r->type,
                    'cssclasses' => $r->cssclasses,
                    'custom'     => $r->custom,
                ];
            }
        }
        $recordset->close();
        $results = array_values($categories);

        return $results;
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'           => new external_value(PARAM_INT, 'Category ID'),
                'name'         => new external_value(PARAM_TEXT, 'Category name'),
                'symbol'       => new external_value(PARAM_RAW,  'Optional FA symbol', VALUE_OPTIONAL),
                'description'  => new external_value(PARAM_TEXT, 'Category description'),
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
