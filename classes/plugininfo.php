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
 * Tiny TinyMCE-Gestaltungsvorlagen plugin for Moodle.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_styles;
defined('MOODLE_INTERNAL') || die();

use context;
use editor_tiny\plugin;
use editor_tiny\plugin_with_configuration;

/**
 * Crude plugininfo class
 */
class plugininfo extends plugin implements plugin_with_configuration {

    /**
     * Pluging info.
     * @param \context $context
     * @param array $options
     * @param array $fpoptions
     * @param mixed $editor
     * @return array{property: string}
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        return [
            'property' => 'TODO Calculate your values here',
        ];
    }
}
