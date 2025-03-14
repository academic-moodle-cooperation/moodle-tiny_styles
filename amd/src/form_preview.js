// for the element creation page its own js method
define([
    'jquery',
    'core/modal_factory',
    'core/modal_events'
], function($, ModalFactory, ModalEvents) {
    "use strict";

    /**
     * Returns the preview content with chosen styling before saving changes
     *
     * If a custom CSS set style.
     * Otherwise set class.
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

        let attribute = `class="${cssclasses}"`;
        if (cssclasses.includes(':') && cssclasses.includes(';')) {
            // Probably inline CSS.
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
         * Initializes the button that opens the preview dialog.
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

                    const previewhtml = buildPreviewHtml(nameVal, cssVal, typeVal);

                    ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: 'Preview "' + nameVal + '"',
                        body: previewhtml
                    })
                        .then(function(modal) {
                            modal.show();
                            modal.getRoot().on(ModalEvents.hidden, function() {
                            });
                        });
                });
            });
        }
    };
});
