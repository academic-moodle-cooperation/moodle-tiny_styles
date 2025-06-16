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
 * Exports the examples for importing style elements.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../../config.php');
require_login();
require_sesskey();
require_capability('moodle/site:config', context_system::instance());

// ZIP with example files
if (!class_exists('ZipArchive')) {
    print_error('ZipArchive not available on this server.');
}

$zip = new ZipArchive();
$zipfilename = tempnam(sys_get_temp_dir(), 'examples') . '.zip';
if ($zip->open($zipfilename, ZipArchive::CREATE) !== TRUE) {
    print_error('Cannot create a zip file for examples.');
}

// example files to ZIP
$examplefile = __DIR__ . '/json/example.json';
$readme = __DIR__ . '/json/instructions.md';

if (file_exists($examplefile)) {
    $zip->addFile($examplefile, 'example.json');
}
if (file_exists($readme)) {
    $zip->addFile($readme, 'instructions.md');
}

$zip->close();

if (filesize($zipfilename) == 0) {
    unlink($zipfilename);
    print_error('No example files found to export.');
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="tiny_styles_examples.zip"');
header('Content-Length: ' . filesize($zipfilename));
readfile($zipfilename);
unlink($zipfilename);
exit;