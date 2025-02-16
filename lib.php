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
 * Defines functions to load categories/elements from DB and 
 * enable them for the Tiny editor
 *
 * @package   tiny_styles
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Fetching all enabled categories from {tiny_styles_categories},
 * plus their associated elements from {tiny_styles_cat_elements}
 * and {tiny_styles_elements}, returning a nested data structure.
 *
 * @return array
 */
function tiny_styles_get_categories_for_editor(): array {
    global $DB;

    // enabled categories by sortorder
    $sql = "SELECT * 
              FROM {tiny_styles_categories} 
             WHERE enabled = 1
          ORDER BY sortorder ASC";
    $cats = $DB->get_records_sql($sql);

    // bridging rows linking categories to elements
    $sql2 = "SELECT ce.*, e.name AS ename, e.type, e.cssclasses, e.sortorder AS elesort
               FROM {tiny_styles_cat_elements} ce
               JOIN {tiny_styles_elements} e ON e.id = ce.elementid
              WHERE ce.enabled = 1
                AND e.enabled = 1
           ORDER BY ce.sortorder, elesort, e.id";
    $bridges = $DB->get_records_sql($sql2);

    // grouping data by category id
    $catElements = [];
    foreach ($bridges as $b) {
        $catElements[$b->categoryid][] = [
            'id'         => (int) $b->elementid,
            'name'       => $b->ename,
            'type'       => $b->type,
            'cssclasses' => $b->cssclasses
        ];
    }

    // building the array
    $result = [];
    foreach ($cats as $c) {
        $cid = (int) $c->id;
        $result[] = [
            'id'          => $cid,
            'name'        => $c->name,
            'description' => $c->description,
            'showdesc'    => $c->showdesc,
            'symbol'      => $c->symbol,
            'presentation'=> $c->presentation, // 'submenu','inline','divider'
            'elements'    => isset($catElements[$cid]) ? $catElements[$cid] : []
        ];
    }

    return $result;
}

/**
 * Function automatically called during editor init,
 * enables the editor to use the categories that are loaded
 *
 * @param array $params   Editor init parameters passed by reference
 * @param \context $context The context in which the editor is being used
 */
function tiny_styles_update_init_params(&$params, $context): void {
    $cats = tiny_styles_get_categories_for_editor();
    $params['tiny_styles_categories'] = $cats;
}

