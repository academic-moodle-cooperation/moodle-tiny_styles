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
 * TinyMCE styles plugin for Moodle.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getTinyMCE} from 'editor_tiny/loader';
import {getPluginMetadata} from 'editor_tiny/utils';
import {pluginName} from './common';
import {register as registerOptions} from './options';
import {getSetup as getCommandSetup} from './commands';
import {editCustomStyles} from './commands';
import * as Configuration from './configuration';

// eslint-disable-next-line no-async-promise-executor
export default new Promise(async (resolve) => {
    try {
        const [
            tinyMCE,
            pluginMetadata,
            setupCommands] = await Promise.all([
            getTinyMCE(),
            getPluginMetadata(pluginName, pluginName),
            getCommandSetup(),
        ]);

        // Register the plugin
        tinyMCE.PluginManager.add(pluginName, (editor) => {
            registerOptions(editor);
            setupCommands(editor);

            // Runs a check on the editor text to update custom in text styles.
            editor.on('init', async () => {
                await editCustomStyles(editor);
            });
            return pluginMetadata;
        });
        // invert coloring for selected items
        const style = document.createElement('style');
        style.textContent = `
            .tox-collection__item--active .tox-collection__item-icon svg,
            .tox-collection__item--selected .tox-collection__item-icon svg {
            filter: brightness(0.2) invert(1);
        }`;
        document.head.appendChild(style);

        resolve([pluginName, Configuration]);

    } catch (error) {
        // console.error('Error initializing TinyMCE plugin:', error);
    }
});
