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
 * Enables moving the categories up and down seamlessly.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../../config.php');

try {
    require_login();
    require_sesskey();

    header('Content-Type: application/json');

    $context = context_system::instance();
    require_capability('moodle/site:config', $context);

    $rawinput = file_get_contents('php://input');
    $data = json_decode($rawinput, true);

    if (!isset($data['action'], $data['id'])) {
        throw new moodle_exception('Missing required parameters: ' .
            (isset($data['action']) ? '' : 'action ') .
            (isset($data['id']) ? '' : 'id '));
    }

    $action = $data['action'];
    $catid = (int)$data['id'];

    if (!in_array($action, ['moveup', 'movedown'])) {
        throw new moodle_exception('Invalid action: ' . $action);
    }

    global $DB;

    // Get the current category record from tiny_styles_categories.
    $current = $DB->get_record('tiny_styles_categories', ['id' => $catid], '*', MUST_EXIST);

    // Build SQL query to find neighbor based on action.
    if ($action === 'moveup') {
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
    $params = ['currsort' => $current->sortorder];

    $neighbors = $DB->get_records_sql($sql, $params, 0, 1);

    if (empty($neighbors)) {
        // No neighbors found; nothing to swap.
        $response = ['status' => 'success', 'message' => 'No change required (no neighbor found)'];
        echo json_encode($response);
        exit;
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

    $response = [
        'status' => 'success',
        'message' => 'Category order updated successfully',
        'debug' => [
            'current' => $current->id . ' (now ' . $current->sortorder . ')',
            'neighbor' => $neighbor->id . ' (now ' . $neighbor->sortorder . ')',
        ],
    ];

    echo json_encode($response);

} catch (Throwable $e) {
    $errorinfo = [
        'status' => 'error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];

    http_response_code(500);
    echo json_encode($errorinfo);
}
