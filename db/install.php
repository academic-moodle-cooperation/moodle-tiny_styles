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
 * Post-install script for the default values for tiny_styles
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom installation logic
 */
function xmldb_tiny_styles_install() {
    global $DB;

    $categoryids = create_default_categories();
    $elements = create_default_elements();
    create_category_element_links($categoryids, $elements);
}

/**
 * Creates the default style categories.
 * @return array Array of created category IDs keyed by type
 */
function create_default_categories() {
    global $DB;

    $categoryids = [];

    if (!$DB->record_exists('tiny_styles_categories', [])) {
        // Labels category.
        $cat = new stdClass();
        $cat->name = 'Labels';
        $cat->description = 'Design Elements directly in Text';
        $cat->showdesc = 'helptext';
        $cat->symbol = 'label.svg';
        $cat->menumode = 'submenu';
        $cat->enabled = 1;
        $cat->sortorder = 1;
        $cat->timecreated = time();
        $cat->timemodified = time();
        $categoryids['labels'] = $DB->insert_record('tiny_styles_categories', $cat);

        // Boxes category.
        $cat = new stdClass();
        $cat->name = 'Boxes';
        $cat->description = 'Format paragraphs';
        $cat->showdesc = 'never';
        $cat->symbol = 'box.svg';
        $cat->menumode = 'submenu';
        $cat->enabled = 1;
        $cat->sortorder = 2;
        $cat->timecreated = time();
        $cat->timemodified = time();
        $categoryids['boxes'] = $DB->insert_record('tiny_styles_categories', $cat);

        // Future categories (commented out for now).
        // Uncomment this: create_future_categories();.
    }

    return $categoryids;
}

/**
 * Creates future/commented categories for potential development.
 * These categories can be uncommented in future development,
 * if a default UNI-Vorlagen category is implemented.
 * For further development uncomment and edit the following code:
 * $dividercatid = $DB -> insert_record('tiny_styles_categories', $cat);.
 * $unicatid = $DB -> insert_record('tiny_styles_categories', $cat);
 */
function create_future_categories() {
    // Divider category (commented out).
    $cat = new stdClass();
    $cat->name = '-------';
    $cat->description = '';
    $cat->showdesc = 'never';
    $cat->symbol = '';
    $cat->menumode = 'divider';
    $cat->enabled = 1;
    $cat->sortorder = 3;
    $cat->timecreated = time();
    $cat->timemodified = time();
    // Uni-Vorlagen category (commented out).
    $cat = new stdClass();
    $cat->name = 'Uni-Vorlagen';
    $cat->description = 'Vorlagen im Corporate Design der Uni';
    $cat->showdesc = 'tooltip';
    $cat->symbol = 'school.svg';
    $cat->menumode = 'inline';
    $cat->enabled = 1;
    $cat->sortorder = 4;
    $cat->timecreated = time();
    $cat->timemodified = time();

}

/**
 * Creates the default style elements.
 * @return array Array of created elements with their IDs
 */
function create_default_elements() {
    global $DB;

    if ($DB->record_exists('tiny_styles_elements', [])) {
        return [];
    }

    $elements = get_default_elements_data();

    foreach ($elements as $key => $data) {
        $elem = new stdClass();
        $elem->name = $data['name'];
        $elem->type = $data['type'];
        $elem->cssclasses = $data['cssclasses'];
        $elem->enabled = 1;
        $elem->sortorder = $data['sortorder'];
        $elem->timecreated = time();
        $elem->timemodified = time();
        $elements[$key]['id'] = $DB->insert_record('tiny_styles_elements', $elem);
    }

    return $elements;
}

/**
 * Gets the default elements configuration data.
 * @return array Array of element configurations
 */
function get_default_elements_data() {
    return [
        ['name' => 'Blue Label', 'type' => 'inline', 'cssclasses' => 'badge bg-primary text-white', 'sortorder' => 1],
        ['name' => 'Green Label', 'type' => 'inline', 'cssclasses' => 'badge bg-success text-white', 'sortorder' => 2],
        ['name' => 'Gray Label', 'type' => 'inline', 'cssclasses' => 'badge bg-secondary text-dark', 'sortorder' => 3],
        ['name' => 'Yellow Label', 'type' => 'inline', 'cssclasses' => 'badge bg-warning text-dark', 'sortorder' => 4],
        ['name' => 'Red Label', 'type' => 'inline', 'cssclasses' => 'badge bg-danger text-white', 'sortorder' => 5],
        ['name' => 'Info Label', 'type' => 'inline', 'cssclasses' => 'badge bg-info text-dark', 'sortorder' => 6],
        ['name' => 'Dark Label', 'type' => 'inline', 'cssclasses' => 'badge bg-dark text-white', 'sortorder' => 7],
        ['name' => 'Light Label', 'type' => 'inline', 'cssclasses' => 'badge bg-light text-dark', 'sortorder' => 8],
        ['name' => 'Info Box', 'type' => 'block', 'cssclasses' => 'alert alert-info', 'sortorder' => 9],
        ['name' => 'Yellow Box', 'type' => 'block', 'cssclasses' => 'alert alert-warning', 'sortorder' => 10],
        ['name' => 'Red Box', 'type' => 'block', 'cssclasses' => 'alert alert-danger', 'sortorder' => 11],
        ['name' => 'Green Box', 'type' => 'block', 'cssclasses' => 'alert alert-success', 'sortorder' => 12],
        ['name' => 'Dark Box', 'type' => 'block', 'cssclasses' => 'alert alert-dark', 'sortorder' => 13],
        ['name' => 'Blue Box', 'type' => 'block', 'cssclasses' => 'alert alert-primary', 'sortorder' => 14],
        ['name' => 'Grey Box', 'type' => 'block', 'cssclasses' => 'alert alert-secondary', 'sortorder' => 15],
        ['name' => 'Light Box', 'type' => 'block', 'cssclasses' => 'alert alert-light', 'sortorder' => 16],
        ['name' => 'Divider Link', 'type' => '', 'cssclasses' => '', 'sortorder' => 17],
    ];
}

/**
 * Creates the category-element relationship links.
 * @param array $categoryids Array of category IDs keyed by type
 * @param array $elements Array of elements with their IDs
 */
function create_category_element_links($categoryids, $elements) {
    global $DB;

    if ($DB->record_exists('tiny_styles_cat_elements', []) || empty($categoryids) || empty($elements)) {
        return;
    }

    $links = build_category_element_links($categoryids, $elements);

    foreach ($links as $data) {
        $link = new stdClass();
        $link->categoryid = $data['categoryid'];
        $link->elementid = $data['elementid'];
        $link->enabled = 1;
        $link->sortorder = $data['sortorder'];
        $link->timecreated = time();
        $link->timemodified = time();
        $DB->insert_record('tiny_styles_cat_elements', $link);
    }
}

/**
 * Builds the array of category-element links.
 * @param array $categoryids Array of category IDs
 * @param array $elements Array of elements
 * @return array Array of link configurations
 */
function build_category_element_links($categoryids, $elements) {
    return [
        // Labels category links (elements 0-7).
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[0]['id'], 'sortorder' => 1],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[1]['id'], 'sortorder' => 2],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[2]['id'], 'sortorder' => 3],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[3]['id'], 'sortorder' => 4],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[4]['id'], 'sortorder' => 5],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[5]['id'], 'sortorder' => 6],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[6]['id'], 'sortorder' => 7],
        ['categoryid' => $categoryids['labels'], 'elementid' => $elements[7]['id'], 'sortorder' => 8],
        // Boxes category links (elements 8-15).
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[8]['id'], 'sortorder' => 1],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[9]['id'], 'sortorder' => 2],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[10]['id'], 'sortorder' => 3],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[11]['id'], 'sortorder' => 4],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[12]['id'], 'sortorder' => 5],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[13]['id'], 'sortorder' => 6],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[14]['id'], 'sortorder' => 7],
        ['categoryid' => $categoryids['boxes'], 'elementid' => $elements[15]['id'], 'sortorder' => 8],
    ];
}
