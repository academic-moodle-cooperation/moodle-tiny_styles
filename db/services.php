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
 * Database fetching for TinyMCE editor
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Declares the available web service functions for integration with Moodle's external API.
$functions = [
    'tiny_styles_fetch_categories' => [
        'classname' => 'tiny_styles\external\fetch_categories',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Fetch categories and styles for the editor',
        'type' => 'read',
        'ajax' => true,
        'capabilities' => '',
    ],
    'tiny_styles_bulk_element_action' => [
        'classname' => 'tiny_styles\external\bulk_element_action',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Perform bulk actions on elements (show, hide, duplicate, delete)',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/site:config',
    ],
    'tiny_styles_sort_categories' => [
        'classname' => 'tiny_styles\external\sort_categories',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Move categories up or down in sort order',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/site:config',
    ],
    'tiny_styles_sort_elements' => [
        'classname' => 'tiny_styles\external\sort_elements',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Move elements up or down within a category',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/site:config',
    ],
    'tiny_styles_toggle_category' => [
        'classname' => 'tiny_styles\external\toggle_category',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Toggle category enabled/disabled state',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/site:config',
    ],
    'tiny_styles_toggle_element' => [
        'classname' => 'tiny_styles\external\toggle_element',
        'methodname' => 'execute',
        'classpath' => '',
        'description' => 'Toggle element enabled/disabled state',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'moodle/site:config',
    ],
];
