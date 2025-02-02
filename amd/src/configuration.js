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
 * config for the Moodle tiny_styles plugin.
 *
 * @module      tiny_styles/configuration
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    boxesButtonName,
    labelsButtonName,
    styleMenuItemName,
} from './common';
import {
    addToolbarButtons,
    addMenubarItem,
} from 'editor_tiny/utils';

/**
 * Add two buttons to toolbar
 *
 * @param {Object} instanceConfig TinyMCE config object
 * @return {String|Array} updated toolbar setting
 */
const getToolbarConfiguration = (instanceConfig) => {
    let toolbar = instanceConfig.toolbar;

    // todo: fix buttons to show icons
    toolbar = addToolbarButtons(toolbar, 'content', [
        boxesButtonName,
        labelsButtonName,
    ]);
    return toolbar;
};

/**
 * include "Styles" menu item under "Format"
 *
 * @param {Object} instanceConfig TinyMCE config object
 * @return {String|Array} updated menu setting
 */
const getMenuConfiguration = (instanceConfig) => {
    let menu = instanceConfig.menu;

    menu = addMenubarItem(menu, 'format', [ styleMenuItemName ]);
    return menu;
};

/**
 * exports a "configure" function
 * and merges changes into the existing instance config 
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
