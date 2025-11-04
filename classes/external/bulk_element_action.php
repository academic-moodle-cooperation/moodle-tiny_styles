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
 * External service for bulk actions on elements (show, hide, duplicate, delete).
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
use context_system;
use moodle_exception;

/**
 * External service for bulk element actions.
 */
class bulk_element_action extends external_api {
    /**
     * Returns the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'action' => new external_value(PARAM_ALPHA, 'Action to perform: show, hide, duplicate, delete'),
            'elementids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'Element ID'),
                'Array of element IDs to process'
            ),
            'categoryid' => new external_value(PARAM_INT, 'Category ID (required for duplicate action)', VALUE_DEFAULT, 0),
        ]);
    }

    /**
     * Performs bulk actions on elements.
     *
     * @param string $action The action to perform
     * @param array $elementids Array of element IDs
     * @param int $categoryid Category ID for duplicate action
     * @return array Result with success status and message
     */
    public static function execute($action, $elementids, $categoryid = 0) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'action' => $action,
            'elementids' => $elementids,
            'categoryid' => $categoryid,
        ]);

        // Validate context and capability.
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Validate action.
        $validactions = ['show', 'hide', 'duplicate', 'delete'];
        if (!in_array($params['action'], $validactions)) {
            throw new moodle_exception('error:invalidaction', 'tiny_styles', '', $params['action']);
        }

        // Validate element IDs.
        if (empty($params['elementids'])) {
            throw new moodle_exception('error:missingelements', 'tiny_styles');
        }

        $count = count($params['elementids']);

        // Perform the action.
        switch ($params['action']) {
            case 'show':
                foreach ($params['elementids'] as $id) {
                    $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
                    $element->enabled = 1;
                    $DB->update_record('tiny_styles_elements', $element);
                }
                $message = get_string('elementshown', 'tiny_styles', $count);
                break;

            case 'hide':
                foreach ($params['elementids'] as $id) {
                    $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
                    $element->enabled = 0;
                    $DB->update_record('tiny_styles_elements', $element);
                }
                $message = get_string('elementhidden', 'tiny_styles', $count);
                break;

            case 'duplicate':
                if ($params['categoryid'] <= 0) {
                    throw new moodle_exception('error:missingparam', 'tiny_styles', '', 'categoryid');
                }

                foreach ($params['elementids'] as $id) {
                    // Retrieve the original element.
                    $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
                    if (!$element) {
                        continue;
                    }

                    // Duplicate the element.
                    $newelement = clone $element;
                    unset($newelement->id);
                    $newelement->enabled = 0;
                    $newelement->timecreated = time();
                    $newelement->timemodified = time();
                    $newelementid = $DB->insert_record('tiny_styles_elements', $newelement);

                    // Get the current maximum sort order for this category.
                    $exists = $DB->record_exists('tiny_styles_cat_elements', ['categoryid' => $params['categoryid']]);
                    if ($exists) {
                        $maxsort = $DB->get_field_sql(
                            "SELECT MAX(sortorder)
                               FROM {tiny_styles_cat_elements}
                              WHERE categoryid = ?",
                            [$params['categoryid']]
                        );
                    } else {
                        $maxsort = 0;
                    }

                    // Create linking record in bridge table.
                    $link = new \stdClass();
                    $link->categoryid = $params['categoryid'];
                    $link->elementid = $newelementid;
                    $link->enabled = 1;
                    $link->sortorder = $maxsort + 1;
                    $link->timecreated = time();
                    $link->timemodified = time();
                    $DB->insert_record('tiny_styles_cat_elements', $link);
                }
                $message = get_string('elementduplicated', 'tiny_styles', $count);
                break;

            case 'delete':
                foreach ($params['elementids'] as $id) {
                    // Delete from elements table (bridge table entries will be handled by foreign keys or separately).
                    $DB->delete_records('tiny_styles_elements', ['id' => $id]);
                    // Also delete from bridge table to ensure cleanup.
                    $DB->delete_records('tiny_styles_cat_elements', ['elementid' => $id]);
                }
                $message = get_string('elementdeleted', 'tiny_styles', $count);
                break;

            default:
                throw new moodle_exception('error:invalidaction', 'tiny_styles', '', $params['action']);
        }

        return [
            'success' => true,
            'message' => $message,
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
