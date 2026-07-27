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
import Config from 'core/config';
import {get_string as getString} from 'core/str';
import {icon} from "./common";
import {show as showMenu, hide as hideMenu, isVisible as isMenuVisible} from './menu';
import {isStyledInlineElement} from './styleutils';
// Import PreviewElement from "./preview_element"; // uncomment to enable preview

/**
 * Fetches categories dynamically using AJAX.
 * @returns {Promise<Array>} List of categories.
 */
async function fetchCategories() {
    const requests = [{
        methodname: 'tiny_styles_fetch_categories',
        args: {contextid: Config.contextid},
    }];
    try {
        const [data] = await Ajax.call(requests);
        return data;
    } catch (err) {
        return [];
    }
}


/**
 * Applies a bootstrap or custom style to the selected text.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {Object} styleDef Object containing the style definition.
 * @param {string} styleDef.className The CSS class or custom CSS to apply.
 * @param {boolean} styleDef.block Whether the style is a block-level element.
 * @param {boolean} styleDef.custom Whether the style uses custom CSS properties.
 * @param {int} styleDef.id A unique identifier for the style.
 */
function applyStyle(editor, styleDef) {
    const {className, block, custom, id} = styleDef;

    const selectedHtml = editor.selection.getContent({format: 'html'});
    const selectedNode = editor.selection.getNode();

    // Check if cursor is inside a styled span BEFORE early return
    const styledSpanParent = !block ? editor.dom.getParent(selectedNode, function(node) {
        return isStyledInlineElement(node);
    }) : null;

    // Only return early if no selection AND not in a word AND not a block style
    if (!selectedHtml.trim() && !block && !styledSpanParent) {
        if (!expandSelectionToWord(editor)) {
            return;
        }
    }

    if (block) {
        const listParent = editor.dom.getParent(selectedNode, 'UL,OL');
        if (listParent) {
            return applyListBlockStyle(editor, styleDef, listParent);
        }

        // Check for list & text mixed content
        const selectedBlocks = editor.selection.getSelectedBlocks();
        const hasLists = selectedBlocks.some(block =>
            block.tagName === 'LI' || block.tagName === 'UL' || block.tagName === 'OL'
        );
        const hasTextBlocks = selectedBlocks.some(block =>
            ['P', 'DIV', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'].includes(block.tagName)
        );

        if (hasLists && hasTextBlocks) {
            return applyMixedBlockStyle(editor, styleDef, selectedBlocks);
        }
        return applyBlockStyle(editor, styleDef);
    }

    const listParent = editor.dom.getParent(selectedNode, 'UL,OL');
    if (listParent) {
        return applyListItemStyle(editor, styleDef, selectedNode);
    }

    // Use the styledSpanParent already checked earlier
    if (styledSpanParent) {
        const spanTextContent = styledSpanParent.textContent || styledSpanParent.innerText;

        // Replace the entire span with new styling
        const newWrapper = editor.dom.create('span');

        // Original span's text content to preserve formatting
        newWrapper.textContent = spanTextContent;

        if (custom) {
            const newCss = className + '; --tiny-styles-custom-id: ' + id;
            editor.dom.setAttrib(newWrapper, 'style', newCss);
            editor.dom.setAttrib(newWrapper, 'data-mce-style', newCss);
        } else {
            editor.dom.setAttrib(newWrapper, 'class', className);
        }

        editor.dom.replace(newWrapper, styledSpanParent);

        // Space after the new span and position cursor after the space
        handleSpaceAfterInlineSpan(editor, newWrapper);

        editor.focus();
        return;
    }

    // Normal inline styling
    const cleanSelectedHtml = editor.selection.getContent({format: 'html'});
    return applyInlineStyle(editor, styleDef, cleanSelectedHtml);
}

/**
 * Applies styling to a selection which includes text and a list.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {Object} styleDef Object containing the style definition.
 * @param {Object} selectedBlocks Selected text elements
 * @returns {boolean}
 */
function applyMixedBlockStyle(editor, styleDef, selectedBlocks) {
    const {className, custom, id} = styleDef;

    // Actual elements to wrap
    const elementsToWrap = [];

    for (let i = 0; i < selectedBlocks.length; i++) {
        const block = selectedBlocks[i];

        if (block.tagName === 'LI') {
            // Get the parent list
            const list = editor.dom.getParent(block, 'UL,OL');
            if (list && !elementsToWrap.includes(list)) {
                elementsToWrap.push(list);
            }
        } else if (block.tagName === 'UL' || block.tagName === 'OL') {
            if (!elementsToWrap.includes(block)) {
                elementsToWrap.push(block);
            }
        } else {
            elementsToWrap.push(block);
        }
    }

    if (elementsToWrap.length === 0) {
        return false;
    }

    const wrapperDiv = editor.dom.create('div');
    if (custom) {
        const newCss = className + '; --tiny-styles-custom-id: ' + id;
        editor.dom.setAttrib(wrapperDiv, 'style', newCss);
        editor.dom.setAttrib(wrapperDiv, 'data-mce-style', newCss);
    } else {
        editor.dom.setAttrib(wrapperDiv, 'class', className);
    }

    // Insert wrapper after the last element
    const lastElement = elementsToWrap[elementsToWrap.length - 1];
    editor.dom.insertAfter(wrapperDiv, lastElement);

    // Move all elements into the wrapper
    elementsToWrap.forEach(element => {
        wrapperDiv.appendChild(element);
    });

    const nextParagraph = editor.dom.create('p', {}, '');
    editor.dom.insertAfter(nextParagraph, wrapperDiv);
    editor.selection.setCursorLocation(nextParagraph, 0);
    editor.focus();
    return true;
}

/**
 * Applies block-level styling directly to the list element (UL/OL)
 * @param {Object} editor TinyMCE editor instance
 * @param {Object} styleDef Style definition object
 * @param {Node} listParent The list element (UL/OL) to style
 * @returns {boolean} True if styling was applied
 */
function applyListBlockStyle(editor, styleDef, listParent) {
    const {className, custom, id} = styleDef;

    // Check if list is already wrapped in a styled div
    const parentDiv = listParent.parentElement;
    if (parentDiv && parentDiv.tagName === 'DIV' && isStyledBlockElement(parentDiv)) {
        // Update existing wrapper's styling
        if (custom) {
            const newCss = className + '; --tiny-styles-custom-id: ' + id;
            editor.dom.setAttrib(parentDiv, 'style', newCss);
            editor.dom.setAttrib(parentDiv, 'data-mce-style', newCss);
            editor.dom.setAttrib(parentDiv, 'class','');
        } else {
            editor.dom.setAttrib(parentDiv, 'class', className);
            editor.dom.setAttrib(parentDiv, 'style','');
            editor.dom.setAttrib(parentDiv, 'data-mce-style','');
        }
    } else {
        const wrapperDiv = editor.dom.create('div');

        if (custom) {
            const newCss = className + '; --tiny-styles-custom-id: ' + id;
            editor.dom.setAttrib(wrapperDiv, 'style', newCss);
            editor.dom.setAttrib(wrapperDiv, 'data-mce-style', newCss);
        } else {
            editor.dom.setAttrib(wrapperDiv, 'class', className);
        }

        editor.dom.insertAfter(wrapperDiv, listParent);
        wrapperDiv.appendChild(listParent);
    }

    editor.focus();
    return true;
}

/**
 * Applies inline styling to list items with proper span replacement logic
 * @param {Object} editor TinyMCE editor instance
 * @param {Object} styleDef Style definition object
 * @param {Node} selectedNode The currently selected node
 * @returns {boolean} True if styling was applied
 */
function applyListItemStyle(editor, styleDef, selectedNode) {
    const {className, custom, id} = styleDef;
    const selectedHtml = editor.selection.getContent({format: 'html'});

    // Get all selected list items
    const selectedBlocks = editor.selection.getSelectedBlocks();
    const selectedListItems = selectedBlocks.filter(block => block.tagName === 'LI');

    // If no list items in selection, check if cursor is inside one
    if (selectedListItems.length === 0) {
        const currentListItem = editor.dom.getParent(selectedNode, 'LI');
        if (currentListItem) {
            selectedListItems.push(currentListItem);
        }
    }

    if (selectedListItems.length === 0) {
        return false;
    }

    if (selectedHtml.trim() && selectedListItems.length > 1) {
        // User selected text across multiple list items
        return applyStyleToMultipleListItems(editor, styleDef, selectedListItems);
    } else if (selectedHtml.trim() && selectedListItems.length === 1) {
        // Single list item with selected text
        const styledSpanParent = editor.dom.getParent(selectedNode, function(node) {
            return isStyledInlineElement(node);
        });

        if (styledSpanParent) {
            // Replace existing styled span
            const spanTextContent = styledSpanParent.textContent || styledSpanParent.innerText;
            const newWrapper = editor.dom.create('span');

            newWrapper.textContent = spanTextContent;

            if (custom) {
                const newCss = className + '; --tiny-styles-custom-id: ' + id;
                editor.dom.setAttrib(newWrapper, 'style', newCss);
                editor.dom.setAttrib(newWrapper, 'data-mce-style', newCss);
            } else {
                editor.dom.setAttrib(newWrapper, 'class', className);
            }

            editor.dom.replace(newWrapper, styledSpanParent);

            // Position cursor after the styled span
            handleSpaceAfterInlineSpan(editor, newWrapper);

            editor.focus();
            return true;
        }

        // No existing span, new styling to selected text
        const cleanSelectedHtml = editor.selection.getContent({format: 'html'});
        applyInlineStyle(editor, styleDef, cleanSelectedHtml);
        return true;
    } else if (!selectedHtml.trim() && selectedListItems.length === 1) {
        // No selection but cursor might be in a styled span
        const styledSpanParent = editor.dom.getParent(selectedNode, function(node) {
            return isStyledInlineElement(node);
        });

        if (styledSpanParent) {
            // Replace existing styled span even without selection
            const spanTextContent = styledSpanParent.textContent || styledSpanParent.innerText;
            const newWrapper = editor.dom.create('span');

            newWrapper.textContent = spanTextContent;

            if (custom) {
                const newCss = className + '; --tiny-styles-custom-id: ' + id;
                editor.dom.setAttrib(newWrapper, 'style', newCss);
                editor.dom.setAttrib(newWrapper, 'data-mce-style', newCss);
            } else {
                editor.dom.setAttrib(newWrapper, 'class', className);
            }

            editor.dom.replace(newWrapper, styledSpanParent);

            // Position cursor after the styled span
            handleSpaceAfterInlineSpan(editor, newWrapper);

            editor.focus();
            return true;
        }
    }
    return false;
}

/**
 * Handles styling when text is selected across multiple list items
 * @param {Object} editor TinyMCE editor instance
 * @param {Object} styleDef Style definition object
 * @param {Array} selectedListItems Array of selected list item elements
 * @returns {boolean} True if styling was applied
 */
function applyStyleToMultipleListItems(editor, styleDef, selectedListItems) {
    const {className, custom, id} = styleDef;
    const selection = editor.selection;
    const range = selection.getRng();
    const originalRange = range.cloneRange();

    // Apply styling to all selected list items entirely
    selectedListItems.forEach((li) => {
        // Remove existing inline styling
        const existingStyledSpans = li.querySelectorAll('span');
        existingStyledSpans.forEach(span => {
            if (isStyledInlineElement(span)) {
                span.outerHTML = span.innerHTML;
            }
        });

        const span = editor.dom.create('span');
        span.innerHTML = li.innerHTML;

        if (custom) {
            const newCss = className + '; --tiny-styles-custom-id: ' + id;
            editor.dom.setAttrib(span, 'style', newCss);
            editor.dom.setAttrib(span, 'data-mce-style', newCss);
        } else {
            editor.dom.setAttrib(span, 'class', className);
        }
        li.innerHTML = '';
        li.appendChild(span);
    });
    // Restore original selection
    selection.setRng(originalRange);
    editor.focus();
    return true;
}

/**
 * Enhanced clearStyling function to handle list block styles on UL/OL elements
 * @param {Object} editor TinyMCE editor instance
 */
function clearListStyling(editor) {
    const selection = editor.selection;
    const selectedNode = selection.getNode();
    const listParent = editor.dom.getParent(selectedNode, 'UL,OL');
    if (listParent) {
        // Check if there's selected content within list items
        const selectedHtml = editor.selection.getContent({format: 'html'});
        if (selectedHtml.trim()) {
            // Check for inline styles in list items
            const selectedBlocks = editor.selection.getSelectedBlocks();
            const selectedListItems = selectedBlocks.filter(block => block.tagName === 'LI');
            if (selectedListItems.length === 0) {
                const currentListItem = editor.dom.getParent(selectedNode, 'LI');
                if (currentListItem) {
                    selectedListItems.push(currentListItem);
                }
            }
            let inlineStylesRemoved = false;
            selectedListItems.forEach(li => {
                const styledSpans = li.querySelectorAll('span');
                styledSpans.forEach(span => {
                    if (isStyledInlineElement(span)) {
                        span.outerHTML = span.innerHTML;
                        inlineStylesRemoved = true;
                    }
                });
            });
            if (inlineStylesRemoved) {
                return true;
            }
            // Check for block styling directly on the list element
            const parentDiv = listParent.parentElement;
            if (parentDiv && parentDiv.tagName === 'DIV' && isStyledBlockElement(parentDiv)) {
                // Unwrap the list from the styled div
                editor.dom.insertAfter(listParent, parentDiv);
                editor.dom.remove(parentDiv);
                return true;
            }
        }
    }
    return false;
}

/**
 * Checks if the element is a styled block element.
 * @param {Node} node selected item
 */
function isStyledBlockElement(node) {
    if (!node || !node.tagName) {
 return false;
}

    const blockTags = ['DIV', 'P', 'SECTION', 'ARTICLE', 'ASIDE', 'UL', 'OL'];
    if (!blockTags.includes(node.tagName.toUpperCase())) {
 return false;
}

    // Check className
    if (node.className) {
 return true;
}

    // Check style property
    if (node.style && node.style.getPropertyValue('--tiny-styles-custom-id')) {
 return true;
}

    // Check data-mce-style attribute for custom style ID
    if (node.getAttribute && node.getAttribute('data-mce-style')) {
        const dataMceStyle = node.getAttribute('data-mce-style');
        if (dataMceStyle.includes('--tiny-styles-custom-id')) {
 return true;
}
    }

    return false;
}

/**
 * Expands a collapsed cursor position to cover the surrounding word.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @returns {boolean}
 */
function expandSelectionToWord(editor) {
    const range = editor.selection.getRng();
    if (!range.collapsed) {
        return true;
    }
    const container = range.startContainer;
    if (container.nodeType !== Node.TEXT_NODE) {
        return false;
    }
    const text = container.textContent;
    const offset = range.startOffset;

    let start = offset;
    while (start > 0 && /\S/.test(text[start - 1])) {
        start--;
    }
    let end = offset;
    while (end < text.length && /\S/.test(text[end])) {
        end++;
    }
    if (start === end) {
        return false;
    }
    const newRange = editor.dom.createRng();
    newRange.setStart(container, start);
    newRange.setEnd(container, end);
    editor.selection.setRng(newRange);
    return true;
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
            const newCss = className + '; --tiny-styles-custom-id: ' + id;
            editor.dom.setAttrib(newParagraph, 'style', newCss);
            editor.dom.setAttrib(newParagraph, 'data-mce-style', newCss);
        } else {
            editor.dom.setAttrib(newParagraph, 'class', className);
        }

        // Replace the selected blocks
        const firstBlock = targetBlocks[0];
        editor.dom.insertAfter(newParagraph, firstBlock);

        targetBlocks.forEach(block => {
            editor.dom.remove(block);
        });

        // Position cursor at the end of the styled block so user can continue typing
        const lastChild = newParagraph.lastChild;
        if (lastChild) {
            if (lastChild.nodeType === Node.TEXT_NODE) {
                selection.setCursorLocation(lastChild, lastChild.length);
            }
        }
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
        newWrapper.style.setProperty('--tiny-styles-custom-id', id.toString());
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

    const listResult = clearListStyling(editor);
    if (listResult) {
        return true;
    }
    const styledDiv = editor.dom.getParent(selectedNode, function(node) {
        return node.tagName === 'DIV' && isStyledBlockElement(node);
    });
    if (styledDiv) {
        // Check if this div contains lists or multiple elements (mixed content)
        const children = Array.from(styledDiv.childNodes).filter(node =>
            node.nodeType === Node.ELEMENT_NODE
        );
        const hasLists = children.some(child =>
            child.tagName === 'UL' || child.tagName === 'OL'
        );
        // If it contains lists or multiple elements, unwrap them
        if (hasLists || children.length > 1) {
            // Unwrap all content from the div
            let insertPoint = styledDiv;
            children.forEach(child => {
                editor.dom.insertAfter(child, insertPoint);
                insertPoint = child;
            });
            // Remove the now-empty styled div
            editor.dom.remove(styledDiv);
            // Position cursor in the first unwrapped element
            if (children.length > 0) {
                selection.setCursorLocation(children[0], 0);
            }
            editor.focus();
            return true;
        }
    }

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
 * Handles space insertion after inline styled spans to prevent multiple spaces
 * from accumulating when users change styles repeatedly.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @param {HTMLElement} spanElement The styled span element.
 */
function handleSpaceAfterInlineSpan(editor, spanElement) {
    const nextNode = spanElement.nextSibling;

    if (nextNode && nextNode.nodeType === Node.TEXT_NODE) {
        const textContent = nextNode.textContent;

        // If only one space adds another
        if (textContent.length >= 1 &&
            (textContent[0] === '\u00A0' || textContent[0] === ' ')) {
            editor.selection.setCursorLocation(nextNode, 1);
            return;
        } else {
            const newText = textContent.substring(0, 1) + '\u00A0' + textContent.substring(1);
            nextNode.textContent = newText;
            editor.selection.setCursorLocation(nextNode, 1);
            return;
        }
    }

    // No spaces adds a space and positions cursor
    const spaceNode = document.createTextNode('\u00A0');
    editor.dom.insertAfter(spaceNode, spanElement);
    editor.selection.setCursorLocation(spaceNode, 1);
}

/**
 * Asynchronous function to scan the custom styles for updates/deletions.
 *
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
                    customStylesMap[elem.id] = elem;
                }
            });
        }
    });

    const customElements = editor.getBody().querySelectorAll('[style*="--tiny-styles-custom-id"]');

    customElements.forEach(element => {
        // Custom style identifier by computed style.
        const computed = window.getComputedStyle(element);
        const customStyleId = computed.getPropertyValue('--tiny-styles-custom-id').trim();

        if (!customStyleId) {
            return;
        }

        // Styling removed if the style has been removed, re-named or hidden.
        const styleDefinition = customStylesMap[customStyleId];
        if (!styleDefinition) {
            // Keeps the id in the styling for recovering hidden styles.
            const minimalCss = `--tiny-styles-custom-id: ${customStyleId};`;
            editor.dom.setAttrib(element, 'style', minimalCss);
            editor.dom.setAttrib(element, 'data-mce-style', minimalCss);

            // Todo: delete styling completely?
            // editor.dom.removeAttrib(element, 'style');
            // editor.dom.removeAttrib(element, 'data-mce-style');

        } else {
            // If inline style does not match it's updated.
            if (element.style.cssText !== styleDefinition.cssclasses) {
                const newCss = styleDefinition.cssclasses + '; --tiny-styles-custom-id: ' + styleDefinition.id;
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
        mainMenuLabel,
        clearLabel,
    ] = await Promise.all([
        fetchCategories(),
        getButtonImage('icon', 'tiny_styles'),
        getString('menuitem_styles', 'tiny_styles'),
        getString('editor:clearstyle', 'tiny_styles'),
    ]);

    return (editor) => {

        editor.ui.registry.addIcon(icon, buttonImage.html);

        /**
         * Helper to creates an empty paragraph for visibility.
         * @param {Object} editor TinyMCE editor instance
         * @returns {HTMLElement} New paragraph element
         */
        const createEmptyParagraph = (editor) => {
            const newPara = editor.dom.create('p');
            newPara.appendChild(editor.dom.create('br'));
            return newPara;
        };

        /**
         * Helper to positions cursor in element.
         * @param {Object} editor TinyMCE editor instance
         * @param {HTMLElement} element Element to position cursor in
         */
        const focusParagraph = (editor, element) => {
            editor.selection.setCursorLocation(element, 0);
            editor.focus();
        };

        /**
         * Helper to checks if cursor has content before and after in current block.
         * @param {Object} editor TinyMCE editor instance
         * @param {Object} range Current selection range
         * @param {HTMLElement} currentBlock Current block element
         * @returns {Object} Object with contentBefore and contentAfter strings
         */
        const getContentAroundCursor = (editor, range, currentBlock) => {
            const beforeRange = editor.dom.createRng();
            beforeRange.setStart(currentBlock, 0);
            beforeRange.setEnd(range.startContainer, range.startOffset);
            const contentBefore = beforeRange.toString().trim();

            const afterRange = editor.dom.createRng();
            afterRange.setStart(range.endContainer, range.endOffset);
            afterRange.setEnd(currentBlock, currentBlock.childNodes.length);
            const contentAfter = afterRange.toString().trim();

            return {contentBefore, contentAfter};
        };

        /**
         * Pushes styled block down.
         * @param {Object} editor TinyMCE editor
         * @param {HTMLElement} styledBlock Current block element
         */
        const handlePushBlockDown = (editor, styledBlock) => {
            const newPara = createEmptyParagraph(editor);
            styledBlock.parentNode.insertBefore(newPara, styledBlock);
            focusParagraph(editor, newPara);
        };

        let continuedTyping = false;

        // Handles pressing Enter in styled blocks.
        editor.on('keydown', (e) => {
            // Removes empty space after BR insertion when user continues typing.
            if (continuedTyping && e.key.length === 1) {
                const range = editor.selection.getRng();

                // Only handles element containers.
                if (range.startContainer.nodeType !== Node.ELEMENT_NODE) {
                    continuedTyping = false;
                    return;
                }

                const container = range.startContainer;
                const offset = range.startOffset;

                if (offset === 0) {
                    continuedTyping = false;
                    return;
                }

                const nodeBefore = container.childNodes[offset - 1];

                // Checks if previous node is text node ending with nbsp.
                if (nodeBefore?.nodeType === Node.TEXT_NODE) {
                    const text = nodeBefore.textContent;

                    if (text.length > 0 && text.charCodeAt(text.length - 1) === 160) {
                        nodeBefore.textContent = text.substring(0, text.length - 1);

                        // Re-adds BR if at end of container.
                        if (offset >= container.childNodes.length) {
                            editor.selection.setContent('<br>');
                        }
                    }
                }
                continuedTyping = false;
            }

            // Handles Enter key in styled text blocks.
            if (e.key === 'Enter' && !e.shiftKey) {
                const node = editor.selection.getNode();

                const styledBlock = editor.dom.getParent(node, (el) => {
                    return isStyledBlockElement(el);
                }, editor.getBody());

                if (!styledBlock) {
                    continuedTyping = false;
                    return;
                }

                const listElement = editor.dom.getParent(node, 'ul,ol,li', editor.getBody());
                if (listElement) {
                    continuedTyping = false;
                    return;
                }

                e.preventDefault();
                continuedTyping = false;

                const range = editor.selection.getRng();

                // Check if we're in an empty paragraph inside a styled block.
                const currentPara = editor.dom.getParent(node, 'p', styledBlock);
                if (currentPara && currentPara !== styledBlock) {
                    const paraText = currentPara.textContent.trim();
                    const onlyHasBR = currentPara.childNodes.length === 1 &&
                        currentPara.childNodes[0].nodeName === 'BR';

                    if (paraText === '' || onlyHasBR) {
                        editor.dom.remove(currentPara);

                        const newPara = createEmptyParagraph(editor);
                        editor.dom.insertAfter(newPara, styledBlock);
                        focusParagraph(editor, newPara);
                        return;
                    }
                }

                const {contentBefore, contentAfter} = getContentAroundCursor(editor, range, styledBlock);

                // Beginning of styled block creates a paragraph before.
                if (contentBefore.length === 0 && contentAfter.length > 0) {
                    handlePushBlockDown(editor, styledBlock);
                    return;
                }

                // Checks for trailing BR to exit the block.
                let elementToCheck = styledBlock;
                if (currentPara && currentPara !== styledBlock) {
                    elementToCheck = currentPara;
                }

                const children = Array.from(elementToCheck.childNodes);
                let hasTrailingBR = false;

                for (let i = children.length - 1; i >= 0; i--) {
                    const child = children[i];
                    if (child.nodeName === 'BR') {
                        hasTrailingBR = true;
                        break;
                    } else if (child.nodeType === Node.TEXT_NODE && child.textContent.trim() === '') {
                        continue;
                    } else {
                        break;
                    }
                }

                const isAtEnd = contentAfter.length === 0;

                if (hasTrailingBR && isAtEnd) {
                    // Second Enter at end exits the styled block.
                    for (let i = children.length - 1; i >= 0; i--) {
                        const child = children[i];
                        if (child.nodeName === 'BR' ||
                            (child.nodeType === Node.TEXT_NODE && child.textContent.trim() === '')) {
                            editor.dom.remove(child);
                        } else {
                            break;
                        }
                    }

                    const newPara = createEmptyParagraph(editor);
                    editor.dom.insertAfter(newPara, styledBlock);
                    focusParagraph(editor, newPara);
                    return;
                }

                // Inserts BR and an empty space to stay in the styled paragraph.
                editor.selection.setContent('<br>&nbsp;');
                continuedTyping = true;
            }

            // Removes an empty styled block with a single Backspace.
            if (e.key === 'Backspace') {
                const node = editor.selection.getNode();

                const styledBlock = editor.dom.getParent(node, (el) => {
                    return isStyledBlockElement(el);
                }, editor.getBody());

                if (!styledBlock || styledBlock.textContent.trim() !== '') {
                    return;
                }

                e.preventDefault();
                const prev = styledBlock.previousElementSibling;
                const parent = styledBlock.parentNode;
                const nextSib = styledBlock.nextSibling;
                editor.dom.remove(styledBlock);

                if (prev) {
                    const range = editor.dom.createRng();
                    range.selectNodeContents(prev);
                    range.collapse(false);
                    editor.selection.setRng(range);
                    editor.focus();
                } else {
                    const newPara = createEmptyParagraph(editor);
                    parent.insertBefore(newPara, nextSib);
                    focusParagraph(editor, newPara);
                }
            }
        });

        const toggleStyles = () => {
            if (isMenuVisible()) {
                hideMenu();
            } else {
                showMenu(
                    editor,
                    categories,
                    (styleDef) => applyStyle(editor, styleDef),
                    () => clearStyling(editor),
                    clearLabel
                );
            }
        };

        editor.ui.registry.addButton('tiny_styles_button', {
            icon: icon,
            tooltip: mainMenuLabel,
            onAction: toggleStyles,
        });

        editor.ui.registry.addMenuItem('tiny_styles_nestedmenu', {
            icon: icon,
            text: mainMenuLabel,
            onAction: toggleStyles,
        });

    };
};
