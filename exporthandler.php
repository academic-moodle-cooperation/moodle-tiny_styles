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
 * Handles exporting the categories and styles.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();
require_sesskey();
require_capability('moodle/site:config', context_system::instance());

// Table fetch ordered by sortorder to preserve original ordering.
$categories = $DB->get_records('tiny_styles_categories', null, 'sortorder ASC');
$elements = $DB->get_records('tiny_styles_elements', null, 'sortorder ASC');
$catelements = $DB->get_records('tiny_styles_cat_elements', null, 'sortorder ASC');

// Associative array, each key is a category id.
$exportcategories = [];
foreach ($categories as $cat) {
    $exportcategories[$cat->id] = [
        'name'             => $cat->name,
        'description'      => $cat->description,
        'symbol'           => $cat->symbol,
        'menumode'         => $cat->menumode,
        'showdesc'         => $cat->showdesc ?? 'never',
        'enabled'          => $cat->enabled,
        'visibility_admin' => $cat->visibility_admin ?? 'all',
        'visibility_roles' => json_decode($cat->visibility_roles ?? '', true) ?? [],
        'elements'         => [],
    ];
}

// Each element to its category based on catelements.
foreach ($catelements as $ce) {
    $categoryid = $ce->categoryid;
    $elementid  = $ce->elementid;

    // The bridging references a non-existent category or element.
    if (!isset($exportcategories[$categoryid]) || !isset($elements[$elementid])) {
        continue;
    }

    // Adds element data to the category elements array.
    $exportcategories[$categoryid]['elements'][] = [
        'id'               => $elements[$elementid]->id,
        'name'             => $elements[$elementid]->name,
        'custom'           => $elements[$elementid]->custom,
        'type'             => $elements[$elementid]->type,
        'cssclasses'       => $elements[$elementid]->cssclasses,
        'enabled'          => $elements[$elementid]->enabled,
        'visibility_admin' => $elements[$elementid]->visibility_admin ?? 'all',
        'visibility_roles' => json_decode($elements[$elementid]->visibility_roles ?? '', true) ?? [],
    ];
}

$exportdata = [
    'categories' => array_values($exportcategories),
];
$jsoncontent = json_encode($exportdata, JSON_PRETTY_PRINT);

// Export as JSON file directly.
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="tiny_styles_export.json"');
header('Content-Length: ' . strlen($jsoncontent));
echo $jsoncontent;
exit;
