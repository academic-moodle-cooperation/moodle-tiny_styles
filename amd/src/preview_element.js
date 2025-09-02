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
 * Separate pop up window for previewing a style.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';

/**
 * Builds a preview snippet with placeholder text and applies
 * a given CSS class or style preview style look.
 *
 * @param {string} name - name for the style.
 * @param {string} cssclasses - CSS string or classes to be applied.
 * @param {string} type - 'block' or 'inline'
 * @returns {string} HTML snippet to display in modal.
 */
const buildPreviewHtml = (name, cssclasses, type) => {
    // Example snippet.
    const snippetBlock = `
        <p>Lorem ipsum <strong>dolor</strong> sit amet, consetetur sadipscing elitr,
           sed diam nonumy eirmod tempor.</p>
        <p>Invidunt ut labore et dolore magna aliquyam erat:</p>
        <ul>
           <li>Auto</li>
           <li>Auto</li>
        </ul>`;

    const isFullCssDefinition = cssclasses.includes('{') && cssclasses.includes('}');

    // If full CSS definition, extract the class name.
    if (isFullCssDefinition) {
        const classMatch = cssclasses.match(/[.][a-zA-Z0-9_-]+/);
        if (classMatch && classMatch.length > 0) {
            const extractedClassName = classMatch[0].substring(1);
            if (extractedClassName) {
                if (type === 'inline') {
                    return snippetBlock.replace(
                        '<strong>dolor</strong>',
                        `<span class="${extractedClassName}">dolor</span>`
                    );
                } else {
                    return `<div class="${extractedClassName}">${snippetBlock}</div>`;
                }
            }
        }
    }

    let attribute = `class="${cssclasses}"`;

    if (cssclasses.includes(':') && cssclasses.includes(';')) {
        attribute = `style="${cssclasses}"`;
    }

    if (type === 'inline') {
        return snippetBlock.replace(
            '<strong>dolor</strong>',
            `<span ${attribute}>dolor</span>`
        );
    } else {
        return `<div ${attribute}>${snippetBlock}</div>`;
    }
};

/**
 * Opens a preview dialog for the given style definition.
 *
 * @param {string} name - Name for the style.
 * @param {string} cssclasses - CSS string or classes to be applied.
 * @param {string} type - 'block' or 'inline'.
 */
const showPreview = async(name, cssclasses, type) => {
    const isFullCssDefinition = cssclasses.includes('{') && cssclasses.includes('}');
    const previewhtml = buildPreviewHtml(name, cssclasses, type);

    try {
        const modal = await ModalFactory.create({
            type: ModalFactory.types.DEFAULT,
            title: `Preview "${name}"`,
            body: previewhtml
        });

        if (isFullCssDefinition) {
            const styleEl = document.createElement('style');
            styleEl.textContent = cssclasses;
            modal.getRoot()[0].appendChild(styleEl);
        }

        modal.show();

        modal.getRoot()[0].addEventListener(ModalEvents.hidden, () => {});
    } catch (error) {}
};

/**
 * Initialize preview functionality for selected elements
 *
 * @param {string} selector - CSS selector for clickable preview elements
 */
export const init = (selector) => {
    document.addEventListener('click', (e) => {
        const target = e.target.closest(selector);
        if (!target) {
            return;
        }

        e.preventDefault();

        const name = target.dataset.name;
        const cssclasses = target.dataset.cssclass;
        const type = target.dataset.type;

        showPreview(name, cssclasses, type);
    });
};

// Export showPreview for external use
export const previewElement = {
    showPreview
};