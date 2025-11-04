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
 * Enables toggling the elements enabled/disabled seamlessly.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['core/ajax', 'core/notification'], function(Ajax, Notification) {
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
            button.title = M.util.get_string('hide', 'core');
        } else {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            button.title = M.util.get_string('show', 'core');
        }
    };

    const init = () => {
        document.querySelectorAll('.toggle-enable-element').forEach(button => {
            button.addEventListener('click', async(event) => {
                event.preventDefault();

                const elementid = parseInt(button.dataset.id);
                if (isNaN(elementid)) {
                    return;
                }

                // Button disabled to prevent multiple clicks.
                button.disabled = true;

                try {
                    const response = await Ajax.call([{
                        methodname: 'tiny_styles_toggle_element',
                        args: {
                            elementid: elementid
                        }
                    }])[0];

                    if (response.success) {
                        updateIcon(button, response.newstate);
                    }

                } catch (error) {
                    Notification.exception(error);
                } finally {
                    button.disabled = false;
                }
            });
        });
    };

    return {init};
});