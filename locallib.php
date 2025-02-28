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
 * locallib for methods
 *
 * @package     tiny_styles
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

function move_element_up(int $catid, int $elementid): void {
    global $DB;

    // 1) Get bridging record for this category+element.
    $catElem = $DB->get_record('tiny_styles_cat_elements', [
        'categoryid' => $catid,
        'elementid'  => $elementid
    ], '*', MUST_EXIST);

    // 2) Find the “above” record with a smaller sortorder.
    //    We only want 1 record, so use the cross-DB approach with limit arguments.
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
    $above = reset($neighbors); // or array_shift($neighbors)

    if ($above) {
        // Swap their sortorders.
        $oldsort = $catElem->sortorder;
        $catElem->sortorder = $above->sortorder;
        $above->sortorder = $oldsort;

        // Update both.
        $DB->update_record('tiny_styles_cat_elements', $catElem);
        $DB->update_record('tiny_styles_cat_elements', $above);
    }
}

function move_element_down(int $catid, int $elementid): void {
    global $DB;

    $catElem = $DB->get_record('tiny_styles_cat_elements', [
        'categoryid' => $catid,
        'elementid'  => $elementid
    ], '*', MUST_EXIST);

    // Find the “below” record with a bigger sortorder.
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

    if ($below) {
        $oldsort = $catElem->sortorder;
        $catElem->sortorder = $below->sortorder;
        $below->sortorder = $oldsort;

        $DB->update_record('tiny_styles_cat_elements', $catElem);
        $DB->update_record('tiny_styles_cat_elements', $below);
    }
}
