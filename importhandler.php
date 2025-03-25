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
 * Handles importing the categories and styles.
 *
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../../../../config.php');

require_login();
require_sesskey();

header('Content-Type: application/json');

// Read raw JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON: ' . json_last_error_msg()
    ]);
    exit;
}

try {
    // Categories.
    $catmapping = [];
    foreach ($data['categories'] as $category) {
        $oldcatid = $category['id'];
        $categoryObj = (object)$category;

        if ($existing = $DB->get_record('tiny_styles_categories', ['name' => $categoryObj->name])) {
            $catmapping[$oldcatid] = $existing->id;
        } else {
            unset($categoryObj->id);
            $newcatid = $DB->insert_record('tiny_styles_categories', $categoryObj);
            $catmapping[$oldcatid] = $newcatid;
        }
    }

    // Elements.
    $elemmapping = [];
    foreach ($data['elements'] as $element) {
        $oldelemid = $element['id'];
        $elementObj = (object)$element;

        if ($existing = $DB->get_record('tiny_styles_elements', ['name' => $elementObj->name])) {
            $elemmapping[$oldelemid] = $existing->id;
        } else {
            unset($elementObj->id);
            $newelemid = $DB->insert_record('tiny_styles_elements', $elementObj);
            $elemmapping[$oldelemid] = $newelemid;
        }
    }

    // Category-element.
    foreach ($data['cat_elements'] as $bridge) {
        $bridgeObj = (object)$bridge;
        $oldcatid = $bridgeObj->categoryid;
        $oldelemid = $bridgeObj->elementid;

        if (isset($catmapping[$oldcatid]) && isset($elemmapping[$oldelemid])) {
            $params = ['categoryid' => $catmapping[$oldcatid], 'elementid' => $elemmapping[$oldelemid]];
            if (!$DB->record_exists('tiny_styles_cat_elements', $params)) {
                unset($bridgeObj->id);
                $bridgeObj->categoryid = $catmapping[$oldcatid];
                $bridgeObj->elementid = $elemmapping[$oldelemid];
                $DB->insert_record('tiny_styles_cat_elements', $bridgeObj);
            }
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Import successful.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}