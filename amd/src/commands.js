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
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
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
    const { className, block, custom, id } = styleDef;

    const selectedHtml = editor.selection.getContent({ format: 'html' });
    if (!selectedHtml.trim()) {
        return;
    }

    const selectedNode = editor.selection.getNode();
    
    if (block) {
        removeExistingStylesOfType(editor, true, selectedNode);
        return applyBlockStyle(editor, styleDef, selectedNode);
    }
    
    const styledSpanParent = editor.dom.getParent(selectedNode, function(node) {
        return isStyledInlineElement(node);
    });
    
    if (styledSpanParent) {
        const selectedText = editor.selection.getContent({ format: 'text' });
        const spanTextContent = styledSpanParent.textContent || styledSpanParent.innerText;
        
        const normalizedSelectedText = normalizeText(selectedText);
        const normalizedSpanText = normalizeText(spanTextContent);
        
        // Check if selecting the entire span content
        if (normalizedSelectedText === normalizedSpanText || 
            selectedText.length === spanTextContent.length) {
                        
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
            
            // Add space after the new span and position cursor after the space
            const spaceNode = document.createTextNode('\u00A0');
            editor.dom.insertAfter(spaceNode, newWrapper);
            const range = editor.dom.createRng();
            range.setStartAfter(spaceNode);
            range.setEndAfter(spaceNode);
            editor.selection.setRng(range);
            
            editor.focus();
            return;
        } 
    }
    
    // Normal inline styling
    removeExistingStylesOfType(editor, false, selectedNode, styledSpanParent);
    const cleanSelectedHtml = editor.selection.getContent({ format: 'html' });
    return applyInlineStyle(editor, styleDef, cleanSelectedHtml);
}

/**
 * Normalizes text content for consistent comparison.
 * 
 * @param {string} text The text to normalize.
 * @returns {string} The normalized text.
 */
function normalizeText(text) {
    return text.trim().replace(/\s+/g, ' ');
}

/**
 * Removes existing styles of the specified type from the selection.
 * 
 * @param {Object} editor TinyMCE editor instance.
 * @param {boolean} isBlockStyle Whether to remove block styles (true) or inline styles (false).
 * @param {Node} selectedNode The currently selected DOM node.
 * @param {Node} styledSpanParent The parent styled span element.
 */
function removeExistingStylesOfType(editor, isBlockStyle, selectedNode, styledSpanParent) {
    const selection = editor.selection;
    
    // Passed selectedNode if available
    const node = selectedNode || selection.getNode();
    
    if (isBlockStyle) {
        const blockParent = editor.dom.getParent(node, function(node) {
            return isStyledBlockElement(node);
        });
        
        if (blockParent) {
            removeStyleFromElement(editor, blockParent);
        }
    } else {
        // Passed styledSpanParent if available
        const spanParent = styledSpanParent || editor.dom.getParent(node, function(node) {
            return isStyledInlineElement(node);
        });
        
        if (spanParent) {
            const selectedText = selection.getContent({ format: 'text' });
            const spanTextContent = spanParent.textContent || spanParent.innerText;
            
            if (normalizeText(selectedText) === normalizeText(spanTextContent)) {
                removeStyleFromElement(editor, spanParent);
            } else {
                // Only part of the span is selected
                const selectedHtml = selection.getContent({ format: 'html' });
                
                // A temporary container to clean the selected content
                const container = document.createElement('div');
                container.innerHTML = selectedHtml;
                
                // Remove any styled spans from the selected content
                const styledSpans = container.querySelectorAll('span');
                styledSpans.forEach(span => {
                    if (isStyledInlineElement(span)) {
                        while (span.firstChild) {
                            span.parentNode.insertBefore(span.firstChild, span);
                        }
                        span.remove();
                    }
                });
                
                selection.setContent(container.innerHTML);
            }
        } else {
            // No styled span parent, checks if selected HTML contains styled spans
            const selectedHtml = selection.getContent({ format: 'html' });
            
            if (selectedHtml.includes('<span')) {
                const container = document.createElement('div');
                container.innerHTML = selectedHtml;
                
                const styledSpans = container.querySelectorAll('span');
                styledSpans.forEach(span => {
                    if (isStyledInlineElement(span)) {
                        while (span.firstChild) {
                            span.parentNode.insertBefore(span.firstChild, span);
                        }
                        span.remove();
                    }
                });
                
                selection.setContent(container.innerHTML);
            }
        }
    }
}

/**
 * Checks if a DOM node is a styled block element.
 * 
 * @param {Node} node The DOM node to check.
 * @returns {boolean} True if the node is a styled block element.
 */
function isStyledBlockElement(node) {
    if (!node || !node.tagName) return false;

    const blockTags = ['DIV', 'P', 'SECTION', 'ARTICLE', 'ASIDE'];
    if (!blockTags.includes(node.tagName.toUpperCase())) return false;
    
    return node.className || 
           (node.style && node.style.getPropertyValue('--custom-style-id'));
}

/**
 * Checks if a DOM node is a styled inline element.
 * 
 * @param {Node} node The DOM node to check.
 * @returns {boolean} True if the node is a styled inline element.
 */
function isStyledInlineElement(node) {
    if (!node || node.tagName !== 'SPAN') return false;
    
    return node.className || 
           (node.style && node.style.getPropertyValue('--custom-style-id'));
}

/**
 * Removes styling from a specific DOM element.
 * 
 * @param {Object} editor TinyMCE editor instance.
 * @param {Element} element The DOM element to remove styling from.
 */
function removeStyleFromElement(editor, element) {
    if (element.className || (element.style && element.style.getPropertyValue('--custom-style-id'))) {
        if (element.tagName === 'P') {
            element.className = '';
            element.style.cssText = '';
        } else {
            while (element.firstChild) {
                editor.dom.insertBefore(element.firstChild, element);
            }
            editor.dom.remove(element);
        }
    }
}

/**
 * Applies block type styling to the selected content.
 * 
 * @param {Object} editor TinyMCE editor instance.
 * @param {Object} styleDef Object containing the style definition.
 * @param {Node} selectedNode The currently selected DOM node.
 */
function applyBlockStyle(editor, styleDef, selectedNode) {
    const { className, custom, id } = styleDef;
    
    const paragraph = editor.dom.getParent(selectedNode, 'p');
    
    if (paragraph) {
        if (custom) {
            paragraph.style.cssText = className;
            paragraph.style.setProperty('--custom-style-id', id);
        } else {
            paragraph.className = className;
        }
        
        const newParagraph = editor.dom.create('p', {}, '');
        editor.dom.insertAfter(newParagraph, paragraph);
        editor.selection.setCursorLocation(newParagraph, 0);
        
    } else {
        const selectedHtml = editor.selection.getContent({ format: 'html' });
        const newWrapper = document.createElement('div');
        
        if (custom) {
            newWrapper.style.cssText = className;
            newWrapper.style.setProperty('--custom-style-id', id);
        } else {
            newWrapper.className = className;
        }
        
        newWrapper.innerHTML = selectedHtml;
        editor.selection.setContent(newWrapper.outerHTML);
        
        const newParagraph = editor.dom.create('p', {}, '');
        editor.dom.insertAfter(newParagraph, newWrapper);
        editor.selection.setCursorLocation(newParagraph, 0);
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
    const { className, custom, id } = styleDef;
    
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
        Array.from(root.childNodes).forEach(stripText);
    }
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

            // todo: delete styling completely?
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
