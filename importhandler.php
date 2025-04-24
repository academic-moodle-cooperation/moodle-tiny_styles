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

// raw json input
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
    $catmapping = [];

    // check the import for categories
    //todo: langstring
    if (empty($data['categories']) || !is_array($data['categories'])) {
        throw new moodle_exception('Import JSON must include a "categories" array.');
    }

    foreach ($data['categories'] as $catarr) {
        $catObj = new stdClass();
        $catObj->name         = $catarr['name']         ?? 'no name';
        $catObj->description  = $catarr['description']  ?? '';
        $catObj->showdesc     = $catarr['showdesc']     ?? 'never';
        $catObj->symbol       = '';
        $catObj->presentation = $catarr['presentation'] ?? 'submenu';
        $catObj->enabled      = $catarr['enabled']      ?? 0;
        $catObj->timecreated  = time();
        $catObj->timemodified = time();
        // new category sortorder from DB.
        $maxcatorder = $DB->get_field_sql(
            "SELECT MAX(sortorder) FROM {tiny_styles_categories}"
        );
        $catObj->sortorder = ($maxcatorder === null ? 0 : $maxcatorder) + 1;
        $catObj->id = $DB->insert_record('tiny_styles_categories', $catObj);

        $catmapping[$catObj->name] = $catObj->id;
    }

    // elements for each cat.
    foreach ($data['categories'] as $catarr) {
        if (empty($catarr['elements']) || !is_array($catarr['elements'])) {
            continue;
        }

        $catname = $catarr['name'];
        if (!isset($catmapping[$catname])) {
            continue;
        }
        $newcatid = $catmapping[$catname];

        foreach ($catarr['elements'] as $elemarr) {
            $elemObj = new stdClass();
            $elemObj->name        = $elemarr['name']        ?? 'no name';
            $elemObj->type        = $elemarr['type']        ?? 'inline';
            $elemObj->cssclasses  = $elemarr['cssclasses']  ?? '';
            $elemObj->enabled     = $elemarr['enabled']     ?? 0;
            $elemObj->custom      = $elemarr['custom']      ?? 1;
            $elemObj->timecreated = time();
            $elemObj->timemodified= time();

            $maxelemorder = $DB->get_field_sql(
                "SELECT MAX(sortorder) FROM {tiny_styles_elements}"
            );
            $elemObj->sortorder = ($maxelemorder === null ? 0 : $maxelemorder) + 1;
            $elemObj->id = $DB->insert_record('tiny_styles_elements', $elemObj);


            $bridgeparams = [
                'categoryid' => $newcatid,
                'elementid'  => $elemObj->id
            ];
            if (!$DB->record_exists('tiny_styles_cat_elements', $bridgeparams)) {
                $bridge = new stdClass();
                $bridge->categoryid   = $newcatid;
                $bridge->elementid    = $elemObj->id;
                $bridge->enabled      = 1;
                // next highest in bridging table
                $maxbridgesort = $DB->get_field_sql(
                    "SELECT MAX(sortorder)
                       FROM {tiny_styles_cat_elements}
                      WHERE categoryid = ?",
                    [$newcatid]
                );
                $bridge->sortorder    = ($maxbridgesort === null ? 0 : $maxbridgesort) + 1;
                $bridge->timecreated  = time();
                $bridge->timemodified = time();

                $DB->insert_record('tiny_styles_cat_elements', $bridge);
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