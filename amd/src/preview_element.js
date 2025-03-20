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

define([
    'jquery',
    'core/modal_factory',
    'core/modal_events'
], function($, ModalFactory, ModalEvents) {
    "use strict";

    /**
     * Builds a preview snippet with placeholder text and applies
     * a given CSS class or style preview style look.
     *
     * @param {string} name - name for the style.
     * @param {string} cssclasses - CSS string or classes to be applied.
     * @param {string} type - 'block' or 'inline'
     * @returns {string} HTML snippet to display in modal.
     */
    function buildPreviewHtml(name, cssclasses, type) {
        // Example snippet.
        const snippetBlock = `
            <p>Lorem ipsum <strong>dolor</strong> sit amet, consetetur sadipscing elitr,
               sed diam nonumy eirmod tempor.</p>
            <p>Invidunt ut labore et dolore magna aliquyam erat:</p>
            <ul>
               <li>Item one</li>
               <li>Item two</li>
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
    }

    /**
     * Opens a preview dialog for the given style definition.
     *
     * @param {string} name - Name for the style.
     * @param {string} cssclasses - CSS string or classes to be applied.
     * @param {string} type - 'block' or 'inline'.
     */
    function showPreview(name, cssclasses, type) {
        const isFullCssDefinition = cssclasses.includes('{') && cssclasses.includes('}');
        const previewhtml = buildPreviewHtml(name, cssclasses, type);

        ModalFactory.create({
            type: ModalFactory.types.DEFAULT,
            title: `Preview "${name}"`,
            body: previewhtml
        }).then(function(modal) {
            if (isFullCssDefinition) {
                const styleEl = document.createElement('style');
                styleEl.textContent = cssclasses;
                modal.getRoot().append(styleEl);
            }
            modal.show();

            modal.getRoot().on(ModalEvents.hidden, function() {
            });
        });
    }

    function init(selector) {
        $(document).on('click', selector, function(e) {
            e.preventDefault();
            const $link = $(this);
            const name = $link.data('name');
            const cssclasses = $link.data('cssclass');
            const type = $link.data('type');
            showPreview(name, cssclasses, type);
        });
    }

    return {
        init: init,
        showPreview: showPreview
    };
});
