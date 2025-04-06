define([], function() {
    /**
     * Toggle the visibility icon based on new state
     *
     * @param {HTMLElement} button - The button element
     * @param {number} newState - The new visibility state (1 for visible, 0 for hidden)
     */
    const updateIcon = (button, newState) => {
        const icon = button.querySelector('.enabled-icon');

        if (!icon) {
            return;
        }
        if (newState === 1) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            button.title = M.str.core.hide;
        } else {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            button.title = M.str.core.show;
        }
    };

    const init = () => {
        document.querySelectorAll('.toggle-enable').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();

                const elementid = parseInt(button.dataset.id);
                if (isNaN(elementid)) {
                    return;
                }

                // Button disabled to prevent multiple clicks.
                button.disabled = true;

                const payload = {
                    elementid: elementid,
                };

                try {
                    const ajaxUrl = M.cfg.wwwroot +
                        '/lib/editor/tiny/plugins/styles/ajax/toggle_element.php?sesskey=' +
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
                        return;
                    }

                    const text = await response.text();

                    try {
                        const data = JSON.parse(text);

                        if (!data.success) {
                            alert(data.message || 'Unexpected error');
                        } else {
                            updateIcon(button, data.newstate);
                        }
                    } catch (e) {
                        alert(+ e.message);
                    }
                } catch (e) {
                    alert(e.message);
                } finally {
                    button.disabled = false;
                }
            });
        });
    };

    return { init };
});