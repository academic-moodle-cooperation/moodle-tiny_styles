define([], function() {
    const moveRow = (row, direction) => {
        const sibling = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;
        if (!row || !sibling || sibling.nodeType !== 1) {
            return false;
        }

        if (direction === 'up') {
            row.parentNode.insertBefore(row, sibling);
        } else {
            row.parentNode.insertBefore(sibling, row);
        }
        return true;
    };

    const init = () => {
        document.querySelectorAll('.move-up, .move-down').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();

                const direction = button.classList.contains('move-up') ? 'up' : 'down';
                const elementid = parseInt(button.dataset.id);
                if (isNaN(elementid)) {
                    return;
                }

                const row = button.closest('tr');
                if (!moveRow(row, direction)) {
                    // Visual update failed.
                    return;
                }

                // Category ID from URL or from M.cfg
                const catid = M.cfg.catid || new URLSearchParams(window.location.search).get('catid');
                if (!catid) {
                    return;
                }

                const payload = {
                    elementid: elementid,
                    categoryid: parseInt(catid),
                    direction: direction
                };

                try {
                    const ajaxUrl = M.cfg.wwwroot +
                        '/lib/editor/tiny/plugins/styles/ajax/sortelements.php?sesskey=' +
                        encodeURIComponent(M.cfg.sesskey);

                    const response = await fetch(ajaxUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload),
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        alert(response.status);
                    }
                } catch (e) {
                    alert(e.message);
                }
            });
        });
    };
    return { init };
});