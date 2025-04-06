<?php
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
    // Retrieve the category record from the database.
    $category = $DB->get_record('tiny_styles_categories', ['id' => $categoryid], '*', MUST_EXIST);

    // Toggle the enabled state.
    $category->enabled = $category->enabled ? 0 : 1;

    // Update the record.
    $DB->update_record('tiny_styles_categories', $category);

    // Return the new state.
    echo json_encode(['success' => true, 'newstate' => $category->enabled]);

} catch (\moodle_exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
