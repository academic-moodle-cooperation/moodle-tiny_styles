<?php
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../../../config.php');
require_login();
require_sesskey();

// Set appropriate headers
header('Content-Type: application/json');

// Add error handling
try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!isset($data['elementid'])) {
        throw new \moodle_exception('Missing elementid');
    }

    $elementid = (int)$data['elementid'];

    global $DB;
    // Get the element record
    $element = $DB->get_record('tiny_styles_elements', ['id' => $elementid], '*', MUST_EXIST);
    // Toggle the enabled state
    $element->enabled = $element->enabled ? 0 : 1;
    // Update the record
    $DB->update_record('tiny_styles_elements', $element);

    // Return the new state in the response
    echo json_encode(['success' => true, 'newstate' => $element->enabled]);

} catch (\moodle_exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}