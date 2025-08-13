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
 * Configuration for the tiny_styles Moodle plugin.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    addToolbarButtons,
    addMenubarItem,
} from 'editor_tiny/utils';

/**
 * Add a button to toolbar
 *
 * @param {Object} instanceConfig TinyMCE config object
 * @return {String|Array} updated toolbar setting
 */
const getToolbarConfiguration = (instanceConfig) => {
   let toolbar = instanceConfig.toolbar;
    toolbar = addToolbarButtons(toolbar, 'formatting', ['tiny_styles_button']);
    return toolbar;
};

/**
 * Add menu item under "Format"
 *
 * @param {Object} instanceConfig TinyMCE config object
 * @return {String|Array} updated menu setting
 */
const getMenuConfiguration = (instanceConfig) => {
    let menu = instanceConfig.menu;

    menu = addMenubarItem(menu, 'format', ['tiny_styles_nestedmenu']);
    return menu;
};

/**
 * Exports a "configure" function and merges changes to instance config
 *
 * @param {Object} instanceConfig TinyMCE config object.
 * @return {Object} updated TinyMCE config object
 */
export const configure = (instanceConfig) => {
    return {
        toolbar: getToolbarConfiguration(instanceConfig),
        menu: getMenuConfiguration(instanceConfig),
    };
};
