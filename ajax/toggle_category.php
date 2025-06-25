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
 * Enables toggling the categories enabled/disabled seamlessly.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../../../config.php');
require_login();
require_sesskey();

header('Content-Type: application/json');

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!isset($data['categoryid'])) {
        throw new \moodle_exception('Missing categoryid');
    }

    $categoryid = (int)$data['categoryid'];

    global $DB;
    // Retrieves the category and updates.
    $category = $DB->get_record('tiny_styles_categories', ['id' => $categoryid], '*', MUST_EXIST);
    $category->enabled = $category->enabled ? 0 : 1;
    $DB->update_record('tiny_styles_categories', $category);

    echo json_encode(['success' => true, 'newstate' => $category->enabled]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
