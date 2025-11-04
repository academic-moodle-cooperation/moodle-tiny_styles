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
 * External service for toggling category enabled/disabled state.
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

/**
 * External service for toggling category visibility.
 */
class toggle_category extends external_api {
    /**
     * Returns the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'categoryid' => new external_value(PARAM_INT, 'Category ID to toggle'),
        ]);
    }

    /**
     * Toggles a category's enabled state.
     *
     * @param int $categoryid The category ID to toggle
     * @return array Result with success status and new state
     */
    public static function execute($categoryid) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'categoryid' => $categoryid,
        ]);

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Retrieve the category and toggle its enabled state.
        $category = $DB->get_record('tiny_styles_categories', ['id' => $params['categoryid']], '*', MUST_EXIST);
        $category->enabled = $category->enabled ? 0 : 1;
        $category->timemodified = time();
        $DB->update_record('tiny_styles_categories', $category);

        return [
            'success' => true,
            'newstate' => $category->enabled,
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
            'newstate' => new external_value(PARAM_INT, 'New enabled state (0 or 1)'),
        ]);
    }
}
