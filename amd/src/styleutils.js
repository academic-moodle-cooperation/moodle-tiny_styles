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
 * Shared style-related utilities used by both commands.js and menu.js.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns true if node is a tiny_styles inline styled span.
 *
 * @param {Node} node
 * @returns {boolean}
 */
export function isStyledInlineElement(node) {
    if (!node || node.tagName !== 'SPAN') {
        return false;
    }
    if (node.className) {
        return true;
    }
    if (node.style && node.style.getPropertyValue('--tiny-styles-custom-id')) {
        return true;
    }
    if (node.getAttribute && node.getAttribute('data-mce-style')) {
        return node.getAttribute('data-mce-style').includes('--tiny-styles-custom-id');
    }
    return false;
}

/**
 * Returns true if the cursor is inside a word,
 * or a real selection exists.
 *
 * @param {Object} editor TinyMCE editor instance.
 * @returns {boolean}
 */
export function isInWord(editor) {
    const range = editor.selection.getRng();
    if (!range.collapsed) {
        return true;
    }
    const container = range.startContainer;
    if (container.nodeType !== Node.TEXT_NODE) {
        return false;
    }
    const offset = range.startOffset;
    const text = container.textContent;
    const charBefore = offset > 0 ? text[offset - 1] : '';
    const charAfter = offset < text.length ? text[offset] : '';
    return /\S/.test(charBefore) && /\S/.test(charAfter);
}
