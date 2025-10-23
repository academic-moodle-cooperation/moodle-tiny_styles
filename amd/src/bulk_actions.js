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

/**
 * Initialize bulk actions functionality
 */
export const init = () => {

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

    dropdown.addEventListener('change', (event) => {
        const action = event.target.value;
        if (!action) {
            return;
        }

        // Get selected element IDs using native selectors
        const selected = [];
        document.querySelectorAll('input[name="selected_elements[]"]:checked').forEach(checkbox => {
            selected.push(checkbox.value);
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

        // Confirm deletion using native confirm dialog
        if (action === 'delete' && !confirm(msg)) {
            dropdown.value = '';
            return;
        }

        // Get category ID from M.cfg or URL parameters
        const catid = M.cfg.catid || new URLSearchParams(window.location.search).get('catid');
        if (!catid) {
            return;
        }

        const payload = {
            action: action,
            elementids: selected,
            categoryid: parseInt(catid)
        };

        const ajaxUrl = M.cfg.wwwroot +
            '/lib/editor/tiny/plugins/styles/ajax/bulk_element_action.php?sesskey=' +
            encodeURIComponent(M.cfg.sesskey);

        fetch(ajaxUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = window.location.pathname + window.location.search;
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => {
                alert(error.message || 'An error occurred');
            });
    });
};