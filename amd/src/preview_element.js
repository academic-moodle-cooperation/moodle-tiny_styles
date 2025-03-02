define([
    'jquery',
    'core/modal_factory',
    'core/modal_events'
], function($, ModalFactory, ModalEvents) {
    "use strict";

    // Returns the snippet with Lorem Ipsum text,
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

                const previewhtml = buildPreviewHtml(name, cssclasses, type);

                ModalFactory.create({
                    type: ModalFactory.types.DEFAULT,
                    title: `Preview "${name}"`,
                    body: previewhtml
                })
                    .then(function(modal) {
                        modal.show();

                        modal.getRoot().on(ModalEvents.hidden, function() {
                        });
                    });
            });
        }
    };
});
