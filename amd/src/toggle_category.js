define([], function() {
    /**
     * Updates the icon and tooltip based on the new state.
     *
     * @param {HTMLElement} button
     * @param {number} newState - 1 for enabled (eye), 0 for disabled (eye-slash)
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

    /**
     * Initializes the toggle functionality for category enable/disable icons.
     */
    const init = () => {
        const buttons = document.querySelectorAll('.toggle-enable');
        buttons.forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();

                const categoryid = parseInt(button.dataset.id);
                if (isNaN(categoryid)) {
                    return;
                }
                // Button disabled to prevent multiple clicks.
                button.disabled = true;
                const payload = { categoryid: categoryid };

                const ajaxUrl = M.cfg.wwwroot +
                    '/lib/editor/tiny/plugins/styles/ajax/toggle_category.php?sesskey=' +
                    encodeURIComponent(M.cfg.sesskey);

                try {
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

                    const data = await response.json();
                    if (data.success) {
                        updateIcon(button, data.newstate);
                    } else {
                        alert(data.message);
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
