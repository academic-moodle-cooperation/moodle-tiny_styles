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
 * External service for sorting categories (move up/down).
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
 * External service for category sorting.
 */
class sort_categories extends external_api {

    /**
     * Returns the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'action' => new external_value(PARAM_ALPHA, 'Action: moveup or movedown'),
            'categoryid' => new external_value(PARAM_INT, 'Category ID to move'),
        ]);
    }

    /**
     * Moves a category up or down in sort order.
     *
     * @param string $action The action to perform (moveup or movedown)
     * @param int $categoryid The category ID to move
     * @return array Result with status and message
     */
    public static function execute($action, $categoryid) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'action' => $action,
            'categoryid' => $categoryid,
        ]);

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Validate action.
        if (!in_array($params['action'], ['moveup', 'movedown'])) {
            throw new moodle_exception('error:invalidaction', 'tiny_styles', '', $params['action']);
        }

        // Get the current category record.
        $current = $DB->get_record('tiny_styles_categories', ['id' => $params['categoryid']], '*', MUST_EXIST);

        // Build SQL query to find neighbor based on action.
        if ($params['action'] === 'moveup') {
            $sql = "SELECT *
                      FROM {tiny_styles_categories}
                     WHERE sortorder < :currsort
                  ORDER BY sortorder DESC";
        } else {
            $sql = "SELECT *
                      FROM {tiny_styles_categories}
                     WHERE sortorder > :currsort
                  ORDER BY sortorder ASC";
        }
        $sqlparams = ['currsort' => $current->sortorder];

        $neighbors = $DB->get_records_sql($sql, $sqlparams, 0, 1);

        if (empty($neighbors)) {
            // No neighbors found; nothing to swap.
            return [
                'success' => true,
                'message' => get_string('categoriesorder', 'tiny_styles'),
            ];
        }

        // Get the first (and only) neighbor record.
        $neighbor = reset($neighbors);

        // Swap sortorder values between current and neighbor.
        $temp = $current->sortorder;
        $current->sortorder = $neighbor->sortorder;
        $neighbor->sortorder = $temp;

        // Save changes to the database.
        $DB->update_record('tiny_styles_categories', $current);
        $DB->update_record('tiny_styles_categories', $neighbor);

        return [
            'success' => true,
            'message' => get_string('categoriesorder', 'tiny_styles'),
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
