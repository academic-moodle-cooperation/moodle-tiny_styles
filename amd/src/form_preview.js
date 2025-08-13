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
 * Enables a popup preview window in the element creation.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';

/**
 * Returns the snippet with Lorem Ipsum text, in a dialog
 * @param {string} name name of the style
 * @param {string} cssclasses css styling or a css class
 * @param {string} type inline or block
 * @returns {string} a HTML snippet with styled text
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

    // Full CSS definition, get the class name
    let className = '';
    if (isFullCssDefinition) {
        // Extract class name from the CSS definition
        const classMatch = cssclasses.match(/[.][a-zA-Z0-9_-]+/);
        if (classMatch && classMatch.length > 0) {
            className = classMatch[0].substring(1); // Remove the leading dot
        }

        // Use the extracted name
        if (className) {
            if (type === 'inline') {
                return snippetBlock.replace(
                    '<strong>dolor</strong>',
                    `<span class="${className}">dolor</span>`
                );
            } else {
                return `<div class="${className}">${snippetBlock}</div>`;
            }
        }
    }

    let attribute = `class="${cssclasses}"`;
    if (cssclasses.includes(':') && cssclasses.includes(';') && !isFullCssDefinition) {
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
 * Initialize preview button functionality
 */
export const init = () => {
    const previewBtn = document.getElementById('btn-preview-element');

    // Form fields after preview click
    previewBtn.addEventListener('click', async(e) => {
        e.preventDefault();

        const nameField = document.getElementById('id_name');
        const typeField = document.getElementById('id_type');
        const cssClassesField = document.getElementById('id_cssclasses');
        const manualConfigField = document.getElementById('id_manualconfig');

        const nameVal = nameField.value;
        const typeVal = typeField.value;
        let cssVal = cssClassesField.value;

        if (cssVal === 'Manual style' && manualConfigField) {
            cssVal = manualConfigField.value;
        }

        const isFullCssDefinition = cssVal.includes('{') && cssVal.includes('}');
        const previewhtml = buildPreviewHtml(nameVal, cssVal, typeVal);

        try {
            const modal = await ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: `Preview "${nameVal}"`,
                body: previewhtml
            });

            if (isFullCssDefinition) {
                const styleEl = document.createElement('style');
                styleEl.textContent = cssVal;

                // Trying to access dom element
                const modalRoot = modal.getRoot();
                if (modalRoot && modalRoot[0]) {
                    modalRoot[0].appendChild(styleEl);
                } else if (modalRoot) {
                    // Already a dom element
                    modalRoot.appendChild(styleEl);
                }
            }

            modal.show();

            const modalElement = modal.getRoot();
            if (modalElement && modalElement[0]) {
                // JQuery object
                modalElement[0].addEventListener(ModalEvents.hidden, () => {
                    // Intentionally empty
                });
            } else if (modalElement) {
                // DOM element
                modalElement.addEventListener(ModalEvents.hidden, () => {
                    // Intentionally empty
                });
            }
        } catch (error) {
            alert(error);
        }
    });
};