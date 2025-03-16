// Separate file for the elements menu preview dialog
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
     * @returns a dialog with styled text snippetS
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

        // Full CSS definition
        let className = '';
        if (isFullCssDefinition) {
            // Extract class name from the CSS definition
            const classMatch = cssclasses.match(/[.][a-zA-Z0-9_-]+/);
            if (classMatch && classMatch.length > 0) {
                className = classMatch[0].substring(1);
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

        // Separate styling for custom styles.
        if (cssclasses.includes(':') && cssclasses.includes(';')) {
            attribute = `style="${cssclasses}"`;
        }

        if (type === 'inline') {
            return snippetBlock.replace('<strong>dolor</strong>', `<span ${attribute}>dolor</span>`);
        } else {
            return `<div ${attribute}>${snippetBlock}</div>`;
        }

    }

    return {
        init: function(selector) {
            $(document).on('click', selector, function(e) {
                e.preventDefault();

                const $link = $(this);
                const name = $link.data('name');
                const cssclasses = $link.data('cssclass');
                const type = $link.data('type');

                const isFullCssDefinition = cssclasses.includes('{') && cssclasses.includes('}');

                const previewhtml = buildPreviewHtml(name, cssclasses, type);

                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: `Preview "${name}"`,
                    body: previewhtml
                })
                    .then(function(modal) {

                        if (isFullCssDefinition) {
                            const styleEl = document.createElement('style');
                            styleEl.textContent = cssclasses;
                            modal.getRoot().append(styleEl);
                        }

                        modal.show();
                        modal.getRoot().on(ModalEvents.hidden, function() {
                        });
                    });
            });
        }
    };
});
