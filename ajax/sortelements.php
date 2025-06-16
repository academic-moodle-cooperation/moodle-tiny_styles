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
 * Enables moving the elements up and down seamlessly.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

require_once(__DIR__ . '/../../../../../../config.php');

// Log initial access
error_log('AJAX request received at: ' . date('Y-m-d H:i:s'));

try {
    require_login();
    require_sesskey();

    header('Content-Type: application/json');

    $context = context_system::instance();
    require_capability('moodle/site:config', $context);

    // Log raw input
    $rawInput = file_get_contents('php://input');
    error_log('Raw input: ' . $rawInput);

    $data = json_decode($rawInput, true);

    // Log parsed data
    error_log('Parsed data: ' . print_r($data, true));

    if (!isset($data['elementid'], $data['categoryid'], $data['direction'])) {
        throw new moodle_exception('Missing required parameters: ' .
            (isset($data['elementid']) ? '' : 'elementid ') .
            (isset($data['categoryid']) ? '' : 'categoryid ') .
            (isset($data['direction']) ? '' : 'direction'));
    }

    $elementid = (int)$data['elementid'];
    $categoryid = (int)$data['categoryid'];
    $direction = $data['direction'];

    // Log parsed values
    error_log("ElementID: $elementid, CategoryID: $categoryid, Direction: $direction");

    if (!in_array($direction, ['up', 'down'])) {
        throw new moodle_exception('Invalid direction: ' . $direction);
    }

    global $DB;

    // Check if the record exists
    $exists = $DB->record_exists('tiny_styles_cat_elements', [
        'categoryid' => $categoryid,
        'elementid' => $elementid
    ]);

    if (!$exists) {
        throw new moodle_exception("No record found for categoryid=$categoryid and elementid=$elementid");
    }

    // Get the current element's bridging record
    $current = $DB->get_record('tiny_styles_cat_elements', [
        'categoryid' => $categoryid,
        'elementid' => $elementid
    ], '*', MUST_EXIST);

    error_log("Current record found: " . print_r($current, true));

    // Find the neighbor element (the one above or below)
    $params = ['catid' => $categoryid, 'sort' => $current->sortorder];
    if ($direction === 'up') {
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

    error_log("SQL query: $sql");
    error_log("Params: " . print_r($params, true));

    $neighbors = $DB->get_records_sql($sql, $params, 0, 1);
    error_log("Neighbors found: " . print_r($neighbors, true));

    if (empty($neighbors)) {
        // No neighbors found, nothing to swap
        $response = ['status' => 'success', 'message' => 'No change required (no neighbor found)'];
        error_log("Response: " . json_encode($response));
        echo json_encode($response);
        exit;
    }

    // Get the first (and only) record
    $neighbor = reset($neighbors);

    // Swap sortorder values
    $temp = $current->sortorder;
    $current->sortorder = $neighbor->sortorder;
    $neighbor->sortorder = $temp;

    error_log("Updating records - Current ID: {$current->id}, New sortorder: {$current->sortorder}");
    error_log("Updating records - Neighbor ID: {$neighbor->id}, New sortorder: {$neighbor->sortorder}");

    // Save changes to database
    $DB->update_record('tiny_styles_cat_elements', $current);
    $DB->update_record('tiny_styles_cat_elements', $neighbor);

    $response = [
        'status' => 'success',
        'message' => 'Order updated successfully',
        'debug' => [
            'current' => $current->id . ' (now ' . $current->sortorder . ')',
            'neighbor' => $neighbor->id . ' (now ' . $neighbor->sortorder . ')'
        ]
    ];

    error_log("Success response: " . json_encode($response));
    echo json_encode($response);

} catch (Throwable $e) {
    $errorInfo = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];

    error_log("Error occurred: " . print_r($errorInfo, true));

    http_response_code(500);
    echo json_encode($errorInfo);
}