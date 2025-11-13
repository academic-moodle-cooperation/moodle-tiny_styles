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
 * External service for sorting elements within a category (move up/down).
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
use external_value;
use context_system;
use moodle_exception;

/**
 * External service for element sorting within categories.
 */
class sort_elements extends external_api {
    /**
     * Returns the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'elementid' => new external_value(PARAM_INT, 'Element ID to move'),
            'categoryid' => new external_value(PARAM_INT, 'Category ID'),
            'direction' => new external_value(PARAM_ALPHA, 'Direction: up or down'),
        ]);
    }

    /**
     * Moves an element up or down within a category.
     *
     * @param int $elementid The element ID to move
     * @param int $categoryid The category ID
     * @param string $direction The direction to move (up or down)
     * @return array Result with status and message
     */
    public static function execute($elementid, $categoryid, $direction) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'elementid' => $elementid,
            'categoryid' => $categoryid,
            'direction' => $direction,
        ]);

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Validate direction.
        if (!in_array($params['direction'], ['up', 'down'])) {
            throw new moodle_exception('error:invalidaction', 'tiny_styles', '', $params['direction']);
        }

        // Check if the record exists in the bridge table.
        $exists = $DB->record_exists('tiny_styles_cat_elements', [
            'categoryid' => $params['categoryid'],
            'elementid' => $params['elementid'],
        ]);

        if (!$exists) {
            throw new moodle_exception('error:recordnotfound', 'tiny_styles');
        }

        // Get the current element's bridging record.
        $current = $DB->get_record('tiny_styles_cat_elements', [
            'categoryid' => $params['categoryid'],
            'elementid' => $params['elementid'],
        ], '*', MUST_EXIST);

        // Find the neighbor element (the one above or below).
        $sqlparams = ['catid' => $params['categoryid'], 'sort' => $current->sortorder];
        if ($params['direction'] === 'up') {
            $sql = "SELECT *
                      FROM {tiny_styles_cat_elements}
                     WHERE categoryid = :catid
                       AND sortorder < :sort
                  ORDER BY sortorder DESC";
        } else {
            $sql = "SELECT *
                      FROM {tiny_styles_cat_elements}
                     WHERE categoryid = :catid
                       AND sortorder > :sort
                  ORDER BY sortorder ASC";
        }

        $neighbors = $DB->get_records_sql($sql, $sqlparams, 0, 1);

        if (empty($neighbors)) {
            // No neighbors found, nothing to swap.
            return [
                'success' => true,
                'message' => get_string('elementsorder', 'tiny_styles'),
            ];
        }

        // Get the first (and only) record.
        $neighbor = reset($neighbors);

        // Swap sortorder values.
        $temp = $current->sortorder;
        $current->sortorder = $neighbor->sortorder;
        $neighbor->sortorder = $temp;

        // Save changes to database.
        $DB->update_record('tiny_styles_cat_elements', $current);
        $DB->update_record('tiny_styles_cat_elements', $neighbor);

        return [
            'success' => true,
            'message' => get_string('elementsorder', 'tiny_styles'),
        ];
    }

    /**
     * Returns the structure of the data returned by execute().
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'message' => new external_value(PARAM_TEXT, 'Success or error message'),
        ]);
    }
}
