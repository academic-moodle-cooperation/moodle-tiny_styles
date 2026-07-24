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
 * Enables the bulk actions on the elements page.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import {get_strings as getStrings} from 'core/str';

/**
 * Show a Moodle modal confirmation.
 *
 * @param {string} message The confirmation question
 * @param {string} deleteLabel Label for the confirm button
 * @param {string} cancelLabel Label for the cancel button
 * @returns {Promise<boolean>}
 */
const confirmDelete = (message, deleteLabel, cancelLabel) => new Promise((resolve) => {
    Notification.confirm(
        deleteLabel,
        message,
        deleteLabel,
        cancelLabel,
        () => resolve(true),
        () => resolve(false)
    );
});

/**
 * Initialize bulk actions functionality
 */
export const init = async() => {
    const [deleteLabel, cancelLabel] = await getStrings([
        {key: 'delete', component: 'tiny_styles'},
        {key: 'cancel'},
    ]);

    // Reset all checkboxes on page load (fixes Firefox form state persistence)
    document.querySelectorAll('input[name="selected_elements[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    // Also reset select-all checkbox if present
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.checked = false;
    }

    const dropdown = document.getElementById('bulk-actions-dropdown');

    if (!dropdown) {
        return;
    }

    dropdown.addEventListener('change', async(event) => {
        const action = event.target.value;
        if (!action) {
            return;
        }

        // Get selected element IDs using native selectors
        const selected = [];
        document.querySelectorAll('input[name="selected_elements[]"]:checked').forEach(checkbox => {
            selected.push(parseInt(checkbox.value));
        });

        const warningElement = document.getElementById('bulk-action-warning');

        if (selected.length === 0) {
            if (warningElement) {
                warningElement.style.display = 'block';
            }
            dropdown.value = '';
            return;
        } else if (warningElement) {
            warningElement.style.display = 'none';
        }
        const stringsEl = document.getElementById('bulk-action-strings');
        const msg = stringsEl ? stringsEl.getAttribute('data-confirm-delete') : 'Are you sure you want to delete the elements?';

        // Confirm deletion.
        if (action === 'delete') {
            const confirmed = await confirmDelete(msg, deleteLabel, cancelLabel);
            if (!confirmed) {
                dropdown.value = '';
                return;
            }
        }

        // Get category ID from M.cfg or URL parameters
        const catid = M.cfg.catid || new URLSearchParams(window.location.search).get('catid');
        if (!catid) {
            return;
        }

        try {
            const response = await Ajax.call([{
                methodname: 'tiny_styles_bulk_element_action',
                args: {
                    action: action,
                    elementids: selected,
                    categoryid: parseInt(catid)
                }
            }])[0];

            if (response.success) {
                window.location.href = window.location.pathname + window.location.search;
            }

        } catch (error) {
            Notification.exception(error);
        }
    });
};