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
 * Enables a pop up preview window.
 *
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/modal_factory',
    'core/modal_events'
], function($, ModalFactory, ModalEvents) {
    "use strict";

    /**
     * Returns the snippet with Lorem Ipsum text, in a dialog
     * @param {string} name name of the style
     * @param {string} cssclasses css styling or a css class
     * @param {string} type inline or block
     * @returns a dialog with styled text snippet
     */
    function buildPreviewHtml(name, cssclasses, type) {
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
    }

    return {
        /**
         * Initializes the button that opens a preview dialog.
         * @param {string} previewButtonSelector - "#btn-preview-element"
         */
        init: function(previewButtonSelector) {
            $(document).ready(function() {
                const $previewBtn = $(previewButtonSelector);

                if (!$previewBtn.length) {
                    return;
                }

                // Form fields after preview click.
                $previewBtn.on('click', function(e) {
                    e.preventDefault();

                    const nameVal = $('#id_name').val();
                    const typeVal = $('#id_type').val();
                    let cssVal    = $('#id_cssclasses').val();

                    if (cssVal === 'Manual style sheet') {
                        cssVal = $('#id_manualconfig').val();
                    }

                    const isFullCssDefinition = cssVal.includes('{') && cssVal.includes('}');

                    const previewhtml = buildPreviewHtml(nameVal, cssVal, typeVal);

                    ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: 'Preview "' + nameVal + '"',
                        body: previewhtml
                    })
                        .then(function(modal) {

                            if (isFullCssDefinition) {
                                const styleEl = document.createElement('style');
                                styleEl.textContent = cssVal;
                                modal.getRoot().append(styleEl);
                            }

                            modal.show();
                            modal.getRoot().on(ModalEvents.hidden, function() {
                            });
                        });
                });
            });
        }
    };
});
