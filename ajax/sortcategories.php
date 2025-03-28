<?php
// /lib/editor/tiny/plugins/styles/ajax/sortcategories.php

require_once(__DIR__ . '/../../../../../../config.php');
require_login();
require_sesskey();

$context = context_system::instance();
require_capability('moodle/site:config', $context);

header('Content-Type: application/json');

try {
    // Get the raw JSON input
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new moodle_exception('Missing input data');
    }

    $data = json_decode($input, true);
    if (!isset($data['action'], $data['id'])) {
        throw new moodle_exception('Invalid input data');
    }

    $action = $data['action'];
    $catid = (int)$data['id'];

    // Include your logic file
    require_once($CFG->dirroot . '/lib/editor/tiny/plugins/styles/locallib.php');

    if ($action === 'moveup') {
        move_category_up($catid);
    } else if ($action === 'movedown') {
        move_category_down($catid);
    } else {
        throw new moodle_exception('Invalid action');
    }

    echo json_encode(['status' => 'success']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
