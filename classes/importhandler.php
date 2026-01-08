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
// phpcs:disable moodle.Commenting.MissingDocblock

namespace tiny_styles;

/**
 * Handles importing the categories and styles from JSON data.
 */
class importhandler {
    /**
     * Truncate string to maximum length and log if truncated.
     *
     * @param string $value The string to truncate
     * @param int $maxlength Maximum allowed length
     * @param string $fieldname Field name for logging
     * @return string Truncated string
     */
    private static function truncate_field($value, $maxlength, $fieldname) {
        if (strlen($value) > $maxlength) {
            debugging(
                "Import: {$fieldname} truncated from " . strlen($value) . " to {$maxlength} characters",
                DEBUG_DEVELOPER
            );
            return substr($value, 0, $maxlength);
        }
        return $value;
    }

    /**
     * Process the imported JSON data.
     *
     * @param array $data The decoded JSON data
     * @return bool True if import was successful
     * @throws \moodle_exception If the JSON format is invalid
     */
    // PHPMD:suppress CyclomaticComplexity,NPathComplexity.
    public static function process(array $data): bool {
        global $DB;

        // Check the import for categories.
        if (empty($data['categories']) || !is_array($data['categories'])) {
            throw new \moodle_exception('importjsoncategories', 'tiny_styles');
        }

        $transaction = $DB->start_delegated_transaction();
        try {
            $catmapping = [];

            // Fetch max sortorder values once.
            $maxcatorder = $DB->get_field_sql(
                "SELECT MAX(sortorder) FROM {tiny_styles_categories}"
            );
            $currentcatorder = $maxcatorder === null ? 0 : $maxcatorder;

            $maxelemorder = $DB->get_field_sql(
                "SELECT MAX(sortorder) FROM {tiny_styles_elements}"
            );
            $currentelemorder = $maxelemorder === null ? 0 : $maxelemorder;

            // Prepare all category records for bulk insert.
            $categoriestoinsert = [];
            $time = time();
            foreach ($data['categories'] as $catarr) {
                $catobj = new \stdClass();
                $catobj->name = self::truncate_field($catarr['name'] ?? 'no name', 255, 'Category name');
                $catobj->description = self::truncate_field($catarr['description'] ?? '', 1000, 'Category description');
                $catobj->symbol = $catarr['symbol'] ?? '';
                $catobj->menumode = $catarr['menumode'] ?? 'submenu';
                $catobj->enabled = $catarr['enabled'] ?? 0;
                $catobj->timecreated = $time;
                $catobj->timemodified = $time;
                $currentcatorder++;
                $catobj->sortorder = $currentcatorder;

                $categoriestoinsert[] = $catobj;
            }

            // Bulk insert all categories.
            if (!empty($categoriestoinsert)) {
                $DB->insert_records('tiny_styles_categories', $categoriestoinsert);

                // Retrieve inserted categories to get their IDs.
                $insertedcats = $DB->get_records(
                    'tiny_styles_categories',
                    ['timecreated' => $time],
                    'sortorder ASC'
                );

                $catindex = 0;
                foreach ($insertedcats as $cat) {
                    $catmapping[$cat->name] = $cat->id;
                    $catindex++;
                }
            }

            // Prepare all elements and bridges for bulk insert.
            $elementstoinsert = [];
            $bridgestoinsert = [];
            $bridgesortorder = [];

            // Get max bridge sortorder per category.
            foreach ($catmapping as $catname => $catid) {
                $maxbridgesort = $DB->get_field_sql(
                    "SELECT MAX(sortorder)
                       FROM {tiny_styles_cat_elements}
                      WHERE categoryid = ?",
                    [$catid]
                );
                $bridgesortorder[$catid] = $maxbridgesort === null ? 0 : $maxbridgesort;
            }

            // Prepare elements and bridges.
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
                    $elemobj->name = self::truncate_field($elemarr['name'] ?? 'no name', 255, 'Element name');
                    $elemobj->type = $elemarr['type'] ?? 'inline';
                    $elemobj->cssclasses = $elemarr['cssclasses'] ?? '';
                    $elemobj->enabled = $elemarr['enabled'] ?? 0;
                    $elemobj->custom = $elemarr['custom'] ?? 1;
                    $elemobj->timecreated = $time;
                    $elemobj->timemodified = $time;
                    $currentelemorder++;
                    $elemobj->sortorder = $currentelemorder;

                    // Store category and bridge info for later.
                    $elemobj->_categoryid = $newcatid;
                    $elementstoinsert[] = $elemobj;
                }
            }

            // Bulk insert all elements.
            if (!empty($elementstoinsert)) {
                $DB->insert_records('tiny_styles_elements', $elementstoinsert);

                // Retrieve inserted elements to get their IDs.
                $insertedelems = $DB->get_records(
                    'tiny_styles_elements',
                    ['timecreated' => $time],
                    'sortorder ASC'
                );

                $elemindex = 0;
                foreach ($insertedelems as $elem) {
                    $categoryid = $elementstoinsert[$elemindex]->_categoryid;

                    // Check if bridge already exists.
                    $bridgeparams = [
                        'categoryid' => $categoryid,
                        'elementid'  => $elem->id,
                    ];
                    if (!$DB->record_exists('tiny_styles_cat_elements', $bridgeparams)) {
                        $bridge = new \stdClass();
                        $bridge->categoryid = $categoryid;
                        $bridge->elementid = $elem->id;
                        $bridge->enabled = 1;
                        $bridgesortorder[$categoryid]++;
                        $bridge->sortorder = $bridgesortorder[$categoryid];
                        $bridge->timecreated = $time;
                        $bridge->timemodified = $time;

                        $bridgestoinsert[] = $bridge;
                    }
                    $elemindex++;
                }
            }

            // Bulk insert all bridges.
            if (!empty($bridgestoinsert)) {
                $DB->insert_records('tiny_styles_cat_elements', $bridgestoinsert);
            }
            $transaction->allow_commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollback($e);
            throw $e;
        }
    }
}
