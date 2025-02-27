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
 * Commands for the editor
 *
 * @module      tiny_styles/commands
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import Ajax from 'core/ajax';
import { get_string as getString } from 'core/str';
import { icon } from "./common";

/**
 * Fetches categories dynamically using AJAX.
 * @returns {Promise<Array>} List of categories.
 */
async function fetchCategories() {
    //debugLog('fetchCategories() - calling webservice tiny_styles_fetch_categories...');
    const requests = [{
        methodname: 'tiny_styles_fetch_categories',
        args: {},
    }];
    try {
        const [data] = await Ajax.call(requests);
      //  debugLog('fetchCategories - got data:', data);
        return data;
    } catch (err) {
        // debugLog('fetchCategories - error:', err);
        return [];
    }
}

/**
 * Builds the category-based menu structure.
 * @param {Object} editor TinyMCE instance.
 * @param {Array} cats List of categories.
 * @param {Object} icons Available icons for categories.
 * @returns {Array} Menu items.
 */
function buildCategoryItems(editor, cats, icons) {
    const items = [];

    cats.forEach((cat) => {
        if (cat.presentation === 'divider') {
            items.push({ type: 'separator' });
            return;
        }
        const subItems = [];
        if (Array.isArray(cat.elements)) {
            cat.elements.forEach((elem) => {
                subItems.push({
                    type: 'menuitem',
                    text: elem.name,
                    onAction: () => {
                       // debugLog(`Applying style for element ID=${elem.id}, name=${elem.name}`);
                        applyStyle(editor, {
                            className: elem.cssclasses,
                            block: (elem.type === 'block'),
                            custom: (elem.custom === 1),
                            id: elem.name,
                        });
                    }
                });
            });
        }
        if (subItems.length > 0) {
            let caticon = icons.default;
            if (cat.name === 'Labels') {
                caticon = icons.label;
            } else if (cat.name === 'Boxes') {
                caticon = icons.box;
            }
            items.push({
                type: 'nestedmenuitem',
                icon: caticon,
                text: cat.name,
                getSubmenuItems: () => subItems
            });
        }
    });

    return items;
}

/**
 * helper method for stripping the selected text
 * recursively strips everything except <a> and <img>
 *
 * @param {HTMLDivElement} root text snippet being parsed
 */
function stripText(root) {
    if (root.nodeType === Node.ELEMENT_NODE) {
        //const tag = root.tagName.toLowerCase();
        //if (tag !== 'a' && tag !== 'img') {
            root.removeAttribute('class');
        //}
        //root.removeAttribute('style');
        Array.from(root.childNodes).forEach(stripText);
    }
}

/**
 * Applying a bootstrap style to the selected text.
 *
 * @param {Object} editor tinyMCE editor instance
 * @param {Object} styleDef object style and bool val for the wrapping option
 */
function applyStyle(editor, styleDef) {
    const { className, block, custom, id } = styleDef;

    const selectedHtml = editor.selection.getContent({ format: 'html' });
    if (!selectedHtml.trim()) {
        return;
    }

    const container = document.createElement('div');
    container.innerHTML = selectedHtml;
    Array.from(container.childNodes).forEach(stripText);

    const newTag = block ? 'div' : 'span';
    const newWrapper = document.createElement(newTag);

    if (custom){
        newWrapper.style.cssText = className;
        newWrapper.setAttribute('style_name', id || 'custom-style');
    } else {
        newWrapper.className = className;
    }
    while (container.firstChild) {
        newWrapper.appendChild(container.firstChild);
    }
    editor.selection.setContent(newWrapper.outerHTML);
    //todo: editor selection stop applying style
}

// todo: add js code to check for manual styling changes

/**
 * Button, Icon and Menu setup for tinymce.
 */
export const getSetup = async () => {

    const [
        cats,
        buttonImage,
        labelImage,
        boxImage,
        defaultImage,
        mainMenuLabel,
    ] = await Promise.all([
        fetchCategories(),
        getButtonImage('icon', 'tiny_styles'),
        getButtonImage('iconlabel', 'tiny_styles'),
        getButtonImage('iconbox', 'tiny_styles'),
        getButtonImage('icondefault', 'tiny_styles'),
        getString('menuitem_styles', 'tiny_styles'),
    ]);

    // if (!cats || cats.length === 0) {
    //    debugLog('No categories returned from web service.');
    // }

    return (editor) => {
        // debugLog('Plugin callback, editor ID=', editor.id);

        editor.ui.registry.addIcon(icon, buttonImage.html);
        editor.ui.registry.addIcon('labelIcon', labelImage.html);
        editor.ui.registry.addIcon('boxIcon', boxImage.html);
        editor.ui.registry.addIcon('defaultIcon', defaultImage.html);

        const icons = {
            label: 'labelIcon',
            box: 'boxIcon',
            default: 'defaultIcon',
        };

        editor.ui.registry.addMenuButton('tiny_styles_button', {
            icon: icon,
            tooltip: mainMenuLabel,
            fetch: (callback) => {
                callback(buildCategoryItems(editor, cats, icons));
            }
        });

        editor.ui.registry.addNestedMenuItem('tiny_styles_nestedmenu', {
            icon: icon,
            text: mainMenuLabel,
            getSubmenuItems: () => buildCategoryItems(editor, cats, icons),
        });

        // debugLog('Added toolbar and menubar entries.');
    };
};

/**
 * Debugging
 */
//function debugLog(...args) {
//    console.log('[tiny_styles DEBUG]', ...args);
//}

/**
 * New approach using the tiny formatter
 * @param editor
 * @param styleDef
 */
function applyStyleTwo(editor, styleDef) {
    const { className, block } = styleDef;

    const selectedHtml = editor.selection.getContent({ format: 'html' });
    if (!selectedHtml.trim()) {
        return;
    }
    // this would need a check
    if (block) {
        editor.formatter.remove('blockFormat');
    } else {
        editor.formatter.remove('inlineFormat');
    }

    const container = document.createElement('div');
    container.innerHTML = selectedHtml;

    if (block) {
        editor.formatter.register('blockFormat', {
            block: 'div',
            classes: className,
            remove: 'none',
        });
    } else {
        editor.formatter.register('inlineFormat', {
            inline: 'span',
            classes: className,
            remove: 'none',
        });
    }

    const newTag = block ? 'div' : 'span';
    const newWrapper = document.createElement(newTag);
    newWrapper.className = className;
    while (container.firstChild) {
        newWrapper.appendChild(container.firstChild);
    }

    editor.selection.setContent(newWrapper.outerHTML);
}
