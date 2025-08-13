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
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import Ajax from 'core/ajax';
import {get_string as getString} from 'core/str';
import {icon} from "./common";
// Import PreviewElement from "./preview_element"; // uncomment to enable preview

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
        if (cat.menumode === 'divider') {
            items.push({type: 'separator'});
            return;
        }

        // Inline menumode type
        if (cat.menumode === 'inline' && Array.isArray(cat.elements)) {
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

    // A separator and remove style button
    items.push({type: 'separator'});
    items.push({
        type: 'menuitem',
        text: 'Clear Styling',
        icon: icons.remove,

        onAction: () => {
           clearStyling(editor);
        }
    });
    return items;
}

/**
 * Applies a bootstrap or custom style to the selected text.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {Object} styleDef Object containing the style definition.
 * @param {string} styleDef.className The CSS class or custom CSS to apply.
 * @param {boolean} styleDef.block Whether the style is a block-level element.
 * @param {boolean} styleDef.custom Whether the style uses custom CSS properties.
 * @param {string} styleDef.id A unique identifier for the style.
 */
function applyStyle(editor, styleDef) {
    const {className, block, custom, id} = styleDef;

    const selectedHtml = editor.selection.getContent({format: 'html'});
    if (!selectedHtml.trim() && !block) {
        return;
    }

    const selectedNode = editor.selection.getNode();

    if (block) {
        return applyBlockStyle(editor, styleDef);
    }

    const styledSpanParent = editor.dom.getParent(selectedNode, function(node) {
        return isStyledInlineElement(node);
    });

    if (styledSpanParent) {
        const spanTextContent = styledSpanParent.textContent || styledSpanParent.innerText;

        // Replace the entire span with new styling
        const newWrapper = document.createElement('span');

        if (custom) {
            newWrapper.style.cssText = className;
            newWrapper.style.setProperty('--custom-style-id', id);
        } else {
            newWrapper.className = className;
        }

        // Original span's text content to preserve formatting
        newWrapper.textContent = spanTextContent;
        editor.dom.replace(newWrapper, styledSpanParent);

        // Space after the new span and position cursor after the space
        const spaceNode = document.createTextNode('\u00A0');
        editor.dom.insertAfter(spaceNode, newWrapper);
        const range = editor.dom.createRng();
        range.setStartAfter(spaceNode);
        range.setEndAfter(spaceNode);
        editor.selection.setRng(range);

        editor.focus();
        return;
    }

    // Normal inline styling
    const cleanSelectedHtml = editor.selection.getContent({format: 'html'});
    return applyInlineStyle(editor, styleDef, cleanSelectedHtml);
}

/**
 * Checks if the element is a styled block element.
 * @param {Node} node selected item
 */
function isStyledBlockElement(node) {
    if (!node || !node.tagName) {
 return false;
}

    const blockTags = ['DIV', 'P', 'SECTION', 'ARTICLE', 'ASIDE'];
    if (!blockTags.includes(node.tagName.toUpperCase())) {
 return false;
}

    // Check className
    if (node.className) {
 return true;
}

    // Check style property
    if (node.style && node.style.getPropertyValue('--custom-style-id')) {
 return true;
}

    // Check data-mce-style attribute for custom style ID
    if (node.getAttribute && node.getAttribute('data-mce-style')) {
        const dataMceStyle = node.getAttribute('data-mce-style');
        if (dataMceStyle.includes('--custom-style-id')) {
 return true;
}
    }

    return false;
}

/**
 * Checks if a DOM node is a styled inline element.
 * Now also checks data-mce-style attribute for custom style IDs.
 *
 * @param {Node} node The DOM node to check.
 * @returns {boolean} True if the node is a styled inline element.
 */
function isStyledInlineElement(node) {
    if (!node || node.tagName !== 'SPAN') {
 return false;
}

    // Check className
    if (node.className) {
 return true;
}

    // Check style property
    if (node.style && node.style.getPropertyValue('--custom-style-id')) {
 return true;
}

    // Check data-mce-style attribute for custom style ID
    if (node.getAttribute && node.getAttribute('data-mce-style')) {
        const dataMceStyle = node.getAttribute('data-mce-style');
        if (dataMceStyle.includes('--custom-style-id')) {
 return true;
}
    }

    return false;
}

/**
 * Applies block type styling to the selected content.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {Object} styleDef Object containing the style definition.
 */
function applyBlockStyle(editor, styleDef) {
    const {className, custom, id} = styleDef;
    const selection = editor.selection;
    const targetBlockTypes = [
                            'P', 'DIV', 'H1',
                            'H2', 'H3', 'H4',
                            'H5', 'H6', 'BLOCKQUOTE',
                            'PRE', 'SECTION',
                            'ARTICLE', 'ASIDE'
                        ];
    const selectedBlocks = selection.getSelectedBlocks();

    const targetBlocks = selectedBlocks.filter(block =>
        targetBlockTypes.includes(block.tagName)
    );

    // Extract text content from each block preserving any inline formatting
    const textContents = targetBlocks.map(block => {
        return block.innerHTML.trim();
    }).filter(content => content.length > 0);

    if (textContents.length > 0) {
        // Keeps structure of combined content with <br> separators
        const combinedContent = textContents.join('<br><br>');
        const newParagraph = editor.dom.create('p', {}, combinedContent);

        // Apply styling to the new paragraph
        if (custom) {
            newParagraph.style.cssText = className;
            newParagraph.style.setProperty('--custom-style-id', id);
        } else {
            newParagraph.className = className;
        }

        // Replace the selected blocks
        const firstBlock = targetBlocks[0];
        editor.dom.insertAfter(newParagraph, firstBlock);

        targetBlocks.forEach(block => {
            editor.dom.remove(block);
        });

        // A new paragraph after to avoid continuous styling
        const nextParagraph = editor.dom.create('p', {}, '');
        editor.dom.insertAfter(nextParagraph, newParagraph);
        selection.setCursorLocation(nextParagraph, 0);
    }

    editor.focus();
}

/**
 * Applies inline type styling to the selected HTML content.
 * Creates a new span element with the specified styling and positions cursor afterward.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {Object} styleDef Object containing the style definition.
 * @param {string} selectedHtml The HTML content to apply styling to.
 */
function applyInlineStyle(editor, styleDef, selectedHtml) {
    const {className, custom, id} = styleDef;

    const container = document.createElement('div');
    container.innerHTML = selectedHtml;
    Array.from(container.childNodes).forEach(stripText);

    const newWrapper = document.createElement('span');

    if (custom) {
        newWrapper.style.cssText = className;
        newWrapper.style.setProperty('--custom-style-id', id);
    } else {
        newWrapper.className = className;
    }

    while (container.firstChild) {
        newWrapper.appendChild(container.firstChild);
    }

    newWrapper.setAttribute('data-temp-inline-style', 'true');
    editor.selection.setContent(newWrapper.outerHTML + '&nbsp;');

    const insertedSpan = editor.dom.select('[data-temp-inline-style="true"]')[0];
    if (insertedSpan) {
        editor.dom.setAttrib(insertedSpan, 'data-temp-inline-style', null);

        const nextNode = insertedSpan.nextSibling;
        if (nextNode && nextNode.nodeType === Node.TEXT_NODE) {
            const range = editor.dom.createRng();
            range.setStart(nextNode, 1);
            range.setEnd(nextNode, 1);
            editor.selection.setRng(range);
        }
    }
    editor.focus();
}

/**
 * Recursively removes class attributes from DOM elements.
 * Helper function to clean existing styling from content.
 *
 * @param {Node} root The root element to process.
 */
function stripText(root) {
    if (root.nodeType === Node.ELEMENT_NODE) {
        root.removeAttribute('class');

        // Also remove custom styles
        if (root.style) {
            root.style.cssText = '';
        }
        root.removeAttribute('style');
        root.removeAttribute('data-mce-style');

        Array.from(root.childNodes).forEach(stripText);
    }
}

/**
 * Clears styling from the selected text or current cursor position.
 * Uses TinyMCE's built-in formatting system for reliable style removal.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @returns {boolean} True if styling was removed, false if no styling was found.
 */
function clearStyling(editor) {
    const selection = editor.selection;
    const selectedNode = selection.getNode();

    // Inline styling spans
    let styledSpanParent = null;

    // Selected node itself is a styled span
    if (selectedNode && selectedNode.tagName === 'SPAN' && isStyledInlineElement(selectedNode)) {
        styledSpanParent = selectedNode;
    } else {
        // Cursor is inside a styled span
        styledSpanParent = editor.dom.getParent(selectedNode, function(node) {
            const isStyled = isStyledInlineElement(node);
            return isStyled;
        });
    }

    // Cursor is positioned inside a styled span
    if (!styledSpanParent) {
        const range = selection.getRng();
        if (range && range.startContainer) {
            // Range start container is inside a styled span
            const spanParent = editor.dom.getParent(range.startContainer, function(node) {
                const isStyled = isStyledInlineElement(node);
                return isStyled;
            });
            if (spanParent) {
                styledSpanParent = spanParent;
            }

        }
    }

    // Replace the styled span with plain text
    if (styledSpanParent) {
        const textContent = styledSpanParent.textContent || styledSpanParent.innerText;
        const textNode = document.createTextNode(textContent);

        editor.dom.replace(textNode, styledSpanParent);

        // Position cursor after the text
        const range = editor.dom.createRng();
        range.setStartAfter(textNode);
        range.setEndAfter(textNode);
        selection.setRng(range);

        editor.focus();
        return true;
    }

    // Block styling paragraphs
    const blockParent = editor.dom.getParent(selectedNode, function(node) {
        const isStyled = isStyledBlockElement(node);
        return isStyled;
    });

    if (blockParent) {
        const innerHTML = blockParent.innerHTML;

        // Check if content contains <br><br> - split into separate paragraphs
        if (innerHTML.includes('<br><br>')) {
            const parts = innerHTML.split('<br><br>');

            // First clean paragraph and additional paragraphs for remaining parts
            const firstParagraph = editor.dom.create('p', {}, parts[0]);
            editor.dom.insertAfter(firstParagraph, blockParent);

            let currentParagraph = firstParagraph;
            for (let i = 1; i < parts.length; i++) {
                const newParagraph = editor.dom.create('p', {}, parts[i]);
                editor.dom.insertAfter(newParagraph, currentParagraph);
                currentParagraph = newParagraph;
            }

            //  Remove the original styled block
            editor.dom.remove(blockParent);

            // Position cursor in the first paragraph
            selection.setCursorLocation(firstParagraph, 0);
        } else {
            // Create one clean paragraph
            const cleanParagraph = editor.dom.create('p', {}, innerHTML);
            editor.dom.insertAfter(cleanParagraph, blockParent);
            editor.dom.remove(blockParent);
            selection.setCursorLocation(cleanParagraph, 0);
        }
        editor.focus();
        return true;
    }
    return false;
}

/**
 * Asynchronous function to scan the custom styles for updates/deletions.
 * @param {Object} editor - The TInyMCE editor instance.
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

            // Todo: delete styling completely?
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
export const getSetup = async() => {

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
        removeImage,
    ] = await Promise.all([
        fetchCategories(),
        getButtonImage('icon', 'tiny_styles'),
        getButtonImage('label', 'tiny_styles'),
        getButtonImage('box', 'tiny_styles'),
        getButtonImage('default', 'tiny_styles'),
        getString('menuitem_styles', 'tiny_styles'),
        getButtonImage('preview', 'tiny_styles'),
        getButtonImage('paint', 'tiny_styles'),
        getButtonImage('check', 'tiny_styles'),
        getButtonImage('graduate', 'tiny_styles'),
        getButtonImage('laptop', 'tiny_styles'),
        getButtonImage('magnify', 'tiny_styles'),
        getButtonImage('pen', 'tiny_styles'),
        getButtonImage('school', 'tiny_styles'),
        getButtonImage('square', 'tiny_styles'),
        getButtonImage('flag', 'tiny_styles'),
        getButtonImage('brush', 'tiny_styles'),
        getButtonImage('info', 'tiny_styles'),
        getButtonImage('download', 'tiny_styles'),
        getButtonImage('book', 'tiny_styles'),
        getButtonImage('folder', 'tiny_styles'),
        getButtonImage('remove', 'tiny_styles'),
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
        editor.ui.registry.addIcon('removeIcon', removeImage.html);

        const icons = {
            label: 'labelIcon',
            box: 'boxIcon',
            "default": 'defaultIcon',
            preview: 'previewIcon',
            paint: 'applyIcon',
            check: 'checkIcon',
            graduate: 'graduateIcon',
            laptop: 'laptopIcon',
            magnify: 'magnifyingIcon',
            pen: 'penIcon',
            school: 'schoolIcon',
            square: 'squareIcon',
            flag: 'flagIcon',
            brush: 'brushIcon',
            info: 'infoIcon',
            download: 'downloadIcon',
            book: 'bookIcon',
            folder: 'folderIcon',
            remove: 'removeIcon',
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
