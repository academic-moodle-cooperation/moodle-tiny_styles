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
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();
require_sesskey();
require_capability('moodle/site:config', context_system::instance());

// Fetch categories and elements in one query for export.
$sqlcsv = "SELECT c.id, c.name, c.symbol, c.presentation, c.sortorder AS catsort,
                  e.id AS elemid, e.name AS elemname, e.type, e.cssclasses, e.custom,
                  ce.sortorder AS cesort
           FROM {tiny_styles_categories} c
           LEFT JOIN {tiny_styles_cat_elements} ce ON ce.categoryid = c.id
           LEFT JOIN {tiny_styles_elements} e ON e.id = ce.elementid
           ORDER BY c.sortorder, ce.sortorder";

$recordset = $DB->get_recordset_sql($sqlcsv);

// Temporary in-memory CSV file.
$csvtemp = tmpfile();
$csvheader = [
    'Category ID', 'Category Name', 'Category Symbol', 'Category Presentation',
    'Element ID', 'Element Name', 'Element Type', 'Element CSS Classes', 'Element Custom'
];
fputcsv($csvtemp, $csvheader);

foreach ($recordset as $record) {
    $row = [
        $record->id,
        $record->name,
        $record->symbol,
        $record->presentation,
        $record->elemid,
        $record->elemname,
        $record->type,
        $record->cssclasses,
        $record->custom
    ];
    fputcsv($csvtemp, $row);
}
$recordset->close();

rewind($csvtemp);
$csvcontent = stream_get_contents($csvtemp);
fclose($csvtemp);

$categories = $DB->get_records('tiny_styles_categories');
$elements = $DB->get_records('tiny_styles_elements');
$cat_elements = $DB->get_records('tiny_styles_cat_elements');

$exportdata = [
    'categories' => array_values($categories),
    'elements' => array_values($elements),
    'cat_elements' => array_values($cat_elements)
];
$jsoncontent = json_encode($exportdata, JSON_PRETTY_PRINT);

if (!class_exists('ZipArchive')) {
    print_error('ZipArchive not available on this server.');
}

$zip = new ZipArchive();
$zipfilename = tempnam(sys_get_temp_dir(), 'export') . '.zip';
if ($zip->open($zipfilename, ZipArchive::CREATE) !== TRUE) {
    print_error('Cannot create a zip file for export.');
}
$zip->addFromString('tiny_styles_export.csv', $csvcontent);
$zip->addFromString('tiny_styles_export.json', $jsoncontent);
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="tiny_styles_export.zip"');
header('Content-Length: ' . filesize($zipfilename));
readfile($zipfilename);
unlink($zipfilename);
exit;
