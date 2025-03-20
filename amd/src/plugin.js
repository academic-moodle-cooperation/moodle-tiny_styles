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
 * Tiny tiny_styles for Moodle.
 *
 * @module      tiny_styles/plugin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

        resolve([pluginName, Configuration]);

    } catch (error) {
        // console.error('Error initializing TinyMCE plugin:', error);
    }
});
