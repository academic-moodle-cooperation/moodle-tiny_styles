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
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../../../config.php');

try {
    require_login();
    require_sesskey();

    header('Content-Type: application/json');

    $context = context_system::instance();
    require_capability('moodle/site:config', $context);

    $rawinput = file_get_contents('php://input');

    $data = json_decode($rawinput, true);

    if (!isset($data['elementid'], $data['categoryid'], $data['direction'])) {
        throw new moodle_exception('Missing required parameters: ' .
            (isset($data['elementid']) ? '' : 'elementid ') .
            (isset($data['categoryid']) ? '' : 'categoryid ') .
            (isset($data['direction']) ? '' : 'direction'));
    }

    $elementid = (int)$data['elementid'];
    $categoryid = (int)$data['categoryid'];
    $direction = $data['direction'];

    if (!in_array($direction, ['up', 'down'])) {
        throw new moodle_exception('Invalid direction: ' . $direction);
    }

    global $DB;

    // Check if the record exists.
    $exists = $DB->record_exists('tiny_styles_cat_elements', [
        'categoryid' => $categoryid,
        'elementid' => $elementid,
    ]);

    if (!$exists) {
        throw new moodle_exception("No record found for categoryid=$categoryid and elementid=$elementid");
    }

    // Get the current element's bridging record.
    $current = $DB->get_record('tiny_styles_cat_elements', [
        'categoryid' => $categoryid,
        'elementid' => $elementid,
    ], '*', MUST_EXIST);

    // Find the neighbor element (the one above or below).
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

    $neighbors = $DB->get_records_sql($sql, $params, 0, 1);

    if (empty($neighbors)) {
        // No neighbors found, nothing to swap.
        $response = ['status' => 'success', 'message' => 'No change required (no neighbor found)'];
        echo json_encode($response);
        exit;
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

    $response = [
        'status' => 'success',
        'message' => 'Order updated successfully',
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
