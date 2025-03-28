define([], function() {
    const moveRow = (row, direction) => {
        if (!row) {
            return;
        }
        const sibling = direction === 'up'
            ? row.previousElementSibling
            : row.nextElementSibling;

        if (!sibling || sibling.nodeType !== 1) {
            return;
        }
        if (direction === 'up') {
            row.parentNode.insertBefore(row, sibling);
        } else {
            row.parentNode.insertBefore(sibling, row);
        }
    };

    const init = () => {
        document.querySelectorAll('.moveup, .movedown').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();

                const action = button.dataset.action;
                const id = parseInt(button.dataset.id);
                if (isNaN(id)) {
                    console.error('Invalid id value:', button.dataset.id);
                    return;
                }
                const row = button.closest('tr');
                moveRow(row, action === 'moveup' ? 'up' : 'down');

                const payload = {
                    action: action,
                    id: id
                };

                try {
                    const response = await fetch(
                        M.cfg.wwwroot +
                        '/lib/editor/tiny/plugins/styles/ajax/sortcategories.php?sesskey='
                        + encodeURIComponent(M.cfg.sesskey),
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(payload),
                            credentials: 'same-origin'
                        }
                    );
                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Server responded with error:', errorText);
                    } else {
                        const data = await response.json();
                        console.log('Server response:', data);
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                }
            });
        });
    };

    return { init };
});
