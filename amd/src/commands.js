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
 * Commands for the editor.
 * 
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import Ajax from 'core/ajax';
import { get_string as getString } from 'core/str';
import { icon } from "./common";
// import PreviewElement from "./preview_element"; // uncomment to enable preview

/**
 * Fetches categories dynamically using AJAX.
 * @returns {Promise<Array>} List of categories.
 */
async function fetchCategories() {
    const requests = [{
        methodname: 'tiny_styles_fetch_categories',
        args: {},
    }];
    try {
        const [data] = await Ajax.call(requests);
        return data;
    } catch (err) {
        return [];
    }
}

/**
 * Builds the category-based menu structure.
 *
 * @param {Object} editor TinyMCE instance.
 * @param {Array} categories List of categories.
 * @param {Object} icons Available icons for categories.
 * @returns {Array} Menu items.
 */
function buildCategoryItems(editor, categories, icons) {
    const items = [];

    categories.forEach((cat) => {
        if (cat.presentation === 'divider') {
            items.push({ type: 'separator' });
            return;
        }

        // Inline presentation type
        if (cat.presentation === 'inline' && Array.isArray(cat.elements)) {
            cat.elements.forEach((elem) => {
                items.push({
                    type: 'menuitem',
                    text: elem.name,
                    onAction: () => {
                        applyStyle(editor, {
                            className: elem.cssclasses,
                            block: (elem.type === 'block'),
                            custom: (elem.custom === 1),
                            id: elem.name,
                        });
                    }
                });
            });
            return;
        }

        const subItems = [];
        if (Array.isArray(cat.elements)) {
            cat.elements.forEach((elem) => {
                subItems.push({
                    type: 'menuitem',
                    text: elem.name,
                    onAction: () => {
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

            // Handle case if cat.symbol is missing or undefined.
            const symbolraw = cat.symbol ? cat.symbol : '';
            const symbolname = symbolraw.replace('.svg', '').trim();

            if (icons[symbolname]) {
                caticon = icons[symbolname];
            } else if (cat.name === 'Labels'){
                caticon = icons.label;
            } else if (cat.name === 'Boxes'){
                caticon = icons.box;
            }
            items.push({
                type: 'nestedmenuitem',
                icon: caticon,
                text: cat.name,
                title: 'tooltip',
                getSubmenuItems: () => subItems
            });
        }
    });

    return items;
}


/**
 * Helper method for stripping the selected text.
 * Recursively removes the 'class' attribute from all elements.
 *
 * @param {HTMLElement} root - The element to process.
 */
function stripText(root) {
    if (root.nodeType === Node.ELEMENT_NODE) {
        root.removeAttribute('class');
        Array.from(root.childNodes).forEach(stripText);
    }
}

/**
 * Applies a bootstrap style to the selected text.
 *
 * @param {Object} editor - TinyMCE editor instance.
 * @param {Object} styleDef - Object containing the style definition.
 *   @param {string} styleDef.className - The CSS class/style to apply.
 *   @param {boolean} styleDef.block - Whether the style is a block element.
 *   @param {boolean} styleDef.custom - Whether the style is custom.
 *   @param {string} styleDef.id - A name identifier for the custom style.
 */
function applyStyle(editor, styleDef) {
    const { className, block, custom, id } = styleDef;

    const selectedHtml = editor.selection.getContent({ format: 'html' });
    if (!selectedHtml.trim()) {
        // PreviewElement.showPreview(id, className, block ? 'block' : 'inline');
        return;
    }

    const container = document.createElement('div');
    container.innerHTML = selectedHtml;
    Array.from(container.childNodes).forEach(stripText);

    const newTag = block ? 'div' : 'span';
    const newWrapper = document.createElement(newTag);

    if (custom) {
        newWrapper.style.cssText = className;
        // CUstom style identifier as a CSS custom property.
        newWrapper.style.setProperty('--custom-style-id', id);
    } else {
        newWrapper.className = className;
    }

    while (container.firstChild) {
        newWrapper.appendChild(container.firstChild);
    }

    // Temp identification for block styles to find them.
    if (block) {
        newWrapper.setAttribute('data-temp-style', 'true');
    }

    // Styled content pushed into the selected part.
    editor.selection.setContent(newWrapper.outerHTML);

    // A new paragraph for block styles.
    if (block) {
        // Find by temp id.
        const insertedBlock = editor.dom.select('[data-temp-style="true"]')[0];
        if (insertedBlock) {
            editor.dom.setAttrib(insertedBlock, 'data-temp-style', null);

            const newParagraph = editor.dom.create('p', {}, '');
            editor.dom.insertAfter(newParagraph, insertedBlock);
            editor.selection.setCursorLocation(newParagraph, 0);
        }
    }
    editor.focus();

}


/**
 * Asynchronous function to scan the custom styles for updates/deletions.
 *
 * @param editor - The TInyMCE editor instance.
 */
export async function editCustomStyles(editor) {

    const categoriesResponse = await fetchCategories();

    let categories = [];
    if (Array.isArray(categoriesResponse)) {
        categories = categoriesResponse;
    } else if (categoriesResponse && Array.isArray(categoriesResponse.categories)) {
        categories = categoriesResponse.categories;
    }

    const customStylesMap = {};
    categories.forEach(cat => {
        if (Array.isArray(cat.elements)) {
            cat.elements.forEach(elem => {
                if (elem.custom === 1) {
                    customStylesMap[elem.name] = elem;
                }
            });
        }
    });

    const customElements = editor.getBody().querySelectorAll('[style*="--custom-style-id"]');

    customElements.forEach(element => {
        // Custom style identifier by computed style.
        const computed = window.getComputedStyle(element);
        const customStyleId = computed.getPropertyValue('--custom-style-id').trim();

        if (!customStyleId) {
            return;
        }

        // Styling removed if the style has been removed, re-named or hidden.
        const styleDefinition = customStylesMap[customStyleId];
        if (!styleDefinition) {
            // Keeps the id in the styling for recovering hidden styles.
            const minimalCss = `--custom-style-id: ${customStyleId};`;
            editor.dom.setAttrib(element, 'style', minimalCss);
            editor.dom.setAttrib(element, 'data-mce-style', minimalCss);

            // todo: or delete styling completely?
            // editor.dom.removeAttrib(element, 'style');
            // editor.dom.removeAttrib(element, 'data-mce-style');

        } else {
            // If inline style does not match it's updated.
            if (element.style.cssText !== styleDefinition.cssclasses) {
                const newCss = styleDefinition.cssclasses + '; --custom-style-id: ' + styleDefinition.name;
                editor.dom.setAttrib(element, 'style', newCss);
                editor.dom.setAttrib(element, 'data-mce-style', newCss);
            }
        }
    });

}

/**
 * Button, Icon and Menu setup for tinymce.
 *
 */
export const getSetup = async () => {

    const [
        categories,
        buttonImage,
        labelImage,
        boxImage,
        defaultImage,
        mainMenuLabel,
        previewImage,
        applyImage,
        checkImage,
        graduateImage,
        laptopImage,
        magnifyingImage,
        penImage,
        schoolImage,
        squareImage,
        flagImage,
        brushImage,
        infoImage,
        downloadImage,
        bookImage,
        folderImage,
    ] = await Promise.all([
        fetchCategories(),
        getButtonImage('icon', 'tiny_styles'),
        getButtonImage('iconlabel', 'tiny_styles'),
        getButtonImage('iconbox', 'tiny_styles'),
        getButtonImage('icondefault', 'tiny_styles'),
        getString('menuitem_styles', 'tiny_styles'),
        getButtonImage('preview', 'tiny_styles'),
        getButtonImage('apply', 'tiny_styles'),
        getButtonImage('check', 'tiny_styles'),
        getButtonImage('graduate', 'tiny_styles'),
        getButtonImage('laptop', 'tiny_styles'),
        getButtonImage('magnifying', 'tiny_styles'),
        getButtonImage('pen', 'tiny_styles'),
        getButtonImage('school', 'tiny_styles'),
        getButtonImage('square', 'tiny_styles'),
        getButtonImage('flag', 'tiny_styles'),
        getButtonImage('brush', 'tiny_styles'),
        getButtonImage('info', 'tiny_styles'),
        getButtonImage('download', 'tiny_styles'),
        getButtonImage('book', 'tiny_styles'),
        getButtonImage('folder', 'tiny_styles'),
    ]);

    return (editor) => {

        editor.ui.registry.addIcon(icon, buttonImage.html);
        editor.ui.registry.addIcon('labelIcon', labelImage.html);
        editor.ui.registry.addIcon('boxIcon', boxImage.html);
        editor.ui.registry.addIcon('defaultIcon', defaultImage.html);
        editor.ui.registry.addIcon('previewIcon', previewImage.html);
        editor.ui.registry.addIcon('applyIcon', applyImage.html);
        editor.ui.registry.addIcon('checkIcon', checkImage.html);
        editor.ui.registry.addIcon('graduateIcon', graduateImage.html);
        editor.ui.registry.addIcon('laptopIcon', laptopImage.html);
        editor.ui.registry.addIcon('magnifyingIcon', magnifyingImage.html);
        editor.ui.registry.addIcon('penIcon', penImage.html);
        editor.ui.registry.addIcon('schoolIcon', schoolImage.html);
        editor.ui.registry.addIcon('squareIcon', squareImage.html);
        editor.ui.registry.addIcon('flagIcon', flagImage.html);
        editor.ui.registry.addIcon('brushIcon', brushImage.html);
        editor.ui.registry.addIcon('infoIcon', infoImage.html);
        editor.ui.registry.addIcon('downloadIcon', downloadImage.html);
        editor.ui.registry.addIcon('bookIcon', bookImage.html);
        editor.ui.registry.addIcon('folderIcon', folderImage.html);

        const icons = {
            label: 'labelIcon',
            box: 'boxIcon',
            default: 'defaultIcon',
            preview: 'previewIcon',
            apply: 'applyIcon',
            check: 'checkIcon',
            graduate: 'graduateIcon',
            laptop: 'laptopIcon',
            magnifying: 'magnifyingIcon',
            pen: 'penIcon',
            school: 'schoolIcon',
            square: 'squareIcon',
            flag: 'flagIcon',
            brush: 'brushIcon',
            info: 'infoIcon',
            download: 'downloadIcon',
            book: 'bookIcon',
            folder: 'folderIcon',
        };

        editor.ui.registry.addMenuButton('tiny_styles_button', {
            icon: icon,
            tooltip: mainMenuLabel,
            fetch: (callback) => {
                callback(buildCategoryItems(editor, categories, icons));
            }
        });

        editor.ui.registry.addNestedMenuItem('tiny_styles_nestedmenu', {
            icon: icon,
            text: mainMenuLabel,
            getSubmenuItems: () => buildCategoryItems(editor, categories, icons),
        });

    };
};
