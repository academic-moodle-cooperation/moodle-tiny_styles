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
 * Class for handling the import of categories and styles.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_styles;

defined('MOODLE_INTERNAL') || die();

/**
 * Handles importing the categories and styles from JSON data.
 */
class importhandler {

    /**
     * Process the imported JSON data.
     *
     * @param array $data The decoded JSON data
     * @return bool True if import was successful
     * @throws \moodle_exception If the JSON format is invalid
     */
    public static function process(array $data): bool {
        global $DB;

        // Check the import for categories
        if (empty($data['categories']) || !is_array($data['categories'])) {
            throw new \moodle_exception('importjsoncategories', 'tiny_styles');
        }

        $transaction = $DB->start_delegated_transaction();
        try {
            $catmapping = [];

            // Process categories
            foreach ($data['categories'] as $catarr) {
                $catObj = new \stdClass();
                $catObj->name         = $catarr['name']         ?? 'no name';
                $catObj->description  = $catarr['description']  ?? '';
                $catObj->showdesc     = $catarr['showdesc']     ?? 'never';
                $catObj->symbol       = '';
                $catObj->menumode = $catarr['menumode'] ?? 'submenu';
                $catObj->enabled      = $catarr['enabled']      ?? 0;
                $catObj->timecreated  = time();
                $catObj->timemodified = time();

                // Category sortorder from DB.
                $maxcatorder = $DB->get_field_sql(
                    "SELECT MAX(sortorder) FROM {tiny_styles_categories}"
                );
                $catObj->sortorder = ($maxcatorder === null ? 0 : $maxcatorder) + 1;
                $catObj->id = $DB->insert_record('tiny_styles_categories', $catObj);

                $catmapping[$catObj->name] = $catObj->id;
            }

            // Process elements for each category
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
                    $elemObj = new \stdClass();
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
                        $bridge = new \stdClass();
                        $bridge->categoryid   = $newcatid;
                        $bridge->elementid    = $elemObj->id;
                        $bridge->enabled      = 1;
                        // Next highest in bridging table.
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
            $transaction->allow_commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollback($e);
            throw $e;
        }
    }
}
