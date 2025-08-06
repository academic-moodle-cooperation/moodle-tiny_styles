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
 * Locallib for extra methods.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Used to move a category up in the database.
 * 
 * @param int $catid Unique Category ID.
 * @return void
 */
function move_category_up(int $catid): void {
    global $DB;

    $cat = $DB->get_record('tiny_styles_categories', ['id' => $catid], '*', MUST_EXIST);

    $sql = "SELECT *
              FROM {tiny_styles_categories}
             WHERE sortorder < :currsort
          ORDER BY sortorder DESC";

    $params = ['currsort' => $cat->sortorder];
    $record = $DB->get_records_sql($sql, $params, 0, 1);
    $above = reset($record);

    if ($above) {
        $oldsort = $cat->sortorder;
        $cat->sortorder = $above->sortorder;
        $above->sortorder = $oldsort;

        $DB->update_record('tiny_styles_categories', $cat);
        $DB->update_record('tiny_styles_categories', $above);
    }
}

/**
 * Used to move a category down in the database.
 * 
 * @param int $catid Unique Category ID.
 * @return void
 */
function move_category_down(int $catid): void {
    global $DB;
    $cat = $DB->get_record('tiny_styles_categories', ['id' => $catid], '*', MUST_EXIST);

    $sql = "SELECT *
              FROM {tiny_styles_categories}
             WHERE sortorder > :currsort
          ORDER BY sortorder ASC";

    $params = ['currsort' => $cat->sortorder];
    $record = $DB->get_records_sql($sql, $params, 0, 1);
    $below = reset($record);

    if ($below) {
        $oldsort = $cat->sortorder;
        $cat->sortorder = $below->sortorder;
        $below->sortorder = $oldsort;

        $DB->update_record('tiny_styles_categories', $cat);
        $DB->update_record('tiny_styles_categories', $below);
    }
}

/**
 * Used to move an element up in the database.
 * 
 * @param int $catid Unique Elemnet ID.
 * @return void
 */
function move_element_up(int $catid, int $elementid): void {
    global $DB;

    // Retrieve the current element's bridging record.
    $catElem = $DB->get_record('tiny_styles_cat_elements', [
        'categoryid' => $catid,
        'elementid'  => $elementid
    ], '*', MUST_EXIST);

    // Find the neighbor with a lower sort order (the one immediately above).
    $sql = "SELECT *
              FROM {tiny_styles_cat_elements}
             WHERE categoryid = :catid
               AND sortorder < :currsort
          ORDER BY sortorder DESC";
    $params = [
        'catid'    => $catid,
        'currsort' => $catElem->sortorder
    ];
    $neighbors = $DB->get_records_sql($sql, $params, 0, 1);
    $above = reset($neighbors);

    // If an above neighbor exists, swap their sort orders.
    if ($above) {
        $oldsort = $catElem->sortorder;
        $catElem->sortorder = $above->sortorder;
        $above->sortorder = $oldsort;

        $DB->update_record('tiny_styles_cat_elements', $catElem);
        $DB->update_record('tiny_styles_cat_elements', $above);
    }
}


/**
 * Used to move an element down in the database.
 * 
 * @param int $catid Unique Element ID.
 * @return void
 */
function move_element_down(int $catid, int $elementid): void {
    global $DB;

    // Retrieve the current element's bridging record.
    $catElem = $DB->get_record('tiny_styles_cat_elements', [
        'categoryid' => $catid,
        'elementid'  => $elementid
    ], '*', MUST_EXIST);

    // Find the neighbor with a higher sort order (the one immediately below).
    $sql = "SELECT *
              FROM {tiny_styles_cat_elements}
             WHERE categoryid = :catid
               AND sortorder > :currsort
          ORDER BY sortorder ASC";
    $params = [
        'catid'    => $catid,
        'currsort' => $catElem->sortorder
    ];
    $neighbors = $DB->get_records_sql($sql, $params, 0, 1);
    $below = reset($neighbors);

    // If a below neighbor exists, swap their sort orders.
    if ($below) {
        $oldsort = $catElem->sortorder;
        $catElem->sortorder = $below->sortorder;
        $below->sortorder = $oldsort;

        $DB->update_record('tiny_styles_cat_elements', $catElem);
        $DB->update_record('tiny_styles_cat_elements', $below);
    }
}
