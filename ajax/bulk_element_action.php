<?php
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../../../../config.php');
require_login();
require_sesskey();

// Set the JSON header.
header('Content-Type: application/json');

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!isset($data['action']) || !isset($data['elementids'])) {
        throw new moodle_exception('Missing parameters');
    }
    $action = $data['action'];
    $elementids = $data['elementids'];
    if (!is_array($elementids) || empty($elementids)) {
        throw new moodle_exception('No elements selected');
    }
    global $DB;
    switch ($action) {
        case 'show':
            foreach ($elementids as $id) {
                $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
                $element->enabled = 1;
                $DB->update_record('tiny_styles_elements', $element);
            }
            break;
        case 'hide':
            foreach ($elementids as $id) {
                $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
                $element->enabled = 0;
                $DB->update_record('tiny_styles_elements', $element);
            }
            break;
        case 'duplicate':

            // Get the category id.
            $catid = isset($data['categoryid']) ? (int)$data['categoryid'] : 0;

            foreach ($elementids as $id) {
                // Retrieve the original element.
                $element = $DB->get_record('tiny_styles_elements', ['id' => $id], '*', MUST_EXIST);
                if (!$element) {
                    continue;
                }
                // Duplicate.
                $newelement = clone $element;
                unset($newelement->id);
                $newelement->enabled     = 0;
                $newelement->timecreated = time();
                $newelement->timemodified = time();
                $newelementid = $DB->insert_record('tiny_styles_elements', $newelement);

                // If a category id is provided, add the duplicated element to that category.
                if ($catid) {
                    // The current maximum sort.
                    $exists = $DB->record_exists('tiny_styles_cat_elements', ['categoryid' => $catid]);
                    if ($exists) {
                        $maxsort = $DB->get_field_sql("
                    SELECT MAX(sortorder)
                    FROM {tiny_styles_cat_elements}
                    WHERE categoryid = ?",
                            [$catid]);
                    } else {
                        $maxsort = 0;
                    }
                    // Linking record.
                    $link = new stdClass();
                    $link->categoryid   = $catid;
                    $link->elementid    = $newelementid;
                    $link->enabled      = 1;
                    $link->sortorder    = $maxsort + 1;
                    $link->timecreated  = time();
                    $link->timemodified = time();
                    $DB->insert_record('tiny_styles_cat_elements', $link);
                }
            }
            break;
        case 'delete':
            foreach ($elementids as $id) {
                $DB->delete_records('tiny_styles_elements', ['id' => $id]);
            }
            break;
        default:
            throw new moodle_exception('Invalid action');
    }
    echo json_encode(['success' => true]);
} catch (\moodle_exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage());
}
