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
                $catobj = new \stdClass();
                $catobj->name = $catarr['name'] ?? 'no name';
                $catobj->description = $catarr['description'] ?? '';
                $catobj->showdesc = $catarr['showdesc'] ?? 'never';
                $catobj->symbol = '';
                $catobj->menumode = $catarr['menumode'] ?? 'submenu';
                $catobj->enabled = $catarr['enabled'] ?? 0;
                $catobj->timecreated = time();
                $catobj->timemodified = time();

                // Category sortorder from DB.
                $maxcatorder = $DB->get_field_sql(
                    "SELECT MAX(sortorder) FROM {tiny_styles_categories}"
                );
                $catobj->sortorder = ($maxcatorder === null ? 0 : $maxcatorder) + 1;
                $catobj->id = $DB->insert_record('tiny_styles_categories', $catobj);

                $catmapping[$catobj->name] = $catobj->id;
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
                    $elemobj = new \stdClass();
                    $elemobj->name = $elemarr['name'] ?? 'no name';
                    $elemobj->type = $elemarr['type'] ?? 'inline';
                    $elemobj->cssclasses = $elemarr['cssclasses'] ?? '';
                    $elemobj->enabled = $elemarr['enabled'] ?? 0;
                    $elemobj->custom = $elemarr['custom'] ?? 1;
                    $elemobj->timecreated = time();
                    $elemobj->timemodified = time();

                    $maxelemorder = $DB->get_field_sql(
                        "SELECT MAX(sortorder) FROM {tiny_styles_elements}"
                    );
                    $elemobj->sortorder = ($maxelemorder === null ? 0 : $maxelemorder) + 1;
                    $elemobj->id = $DB->insert_record('tiny_styles_elements', $elemobj);

                    $bridgeparams = [
                        'categoryid' => $newcatid,
                        'elementid'  => $elemobj->id,
                    ];
                    if (!$DB->record_exists('tiny_styles_cat_elements', $bridgeparams)) {
                        $bridge = new \stdClass();
                        $bridge->categoryid = $newcatid;
                        $bridge->elementid = $elemobj->id;
                        $bridge->enabled = 1;
                        // Next highest in bridging table.
                        $maxbridgesort = $DB->get_field_sql(
                            "SELECT MAX(sortorder)
                               FROM {tiny_styles_cat_elements}
                              WHERE categoryid = ?",
                            [$newcatid]
                        );
                        $bridge->sortorder = ($maxbridgesort === null ? 0 : $maxbridgesort) + 1;
                        $bridge->timecreated = time();
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
