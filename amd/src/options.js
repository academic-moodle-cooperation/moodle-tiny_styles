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
 * Options helper for the Moodle tiny_styles plugin.
 *
 * @module      tiny_styles/options
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from './common';


const myStylesPropertyName = getPluginOptionName(pluginName, 'myStylesProperty');

/**
 * Register the plugin-specific options with TinyMCE.
 *
 * @param {TinyMCE.editor} editor
 */
export const register = (editor) => {
    editor.options.register(myStylesPropertyName, {
        processor: 'string',
    });
};

/**
 * Retrieve the value (if needed) from the editor instance.
 *
 * @param {TinyMCE.editor} editor
 * @returns {string} The current value of myStylesProperty (if set)
 */
export const getMyStylesProperty = (editor) => editor.options.get(myStylesPropertyName);
