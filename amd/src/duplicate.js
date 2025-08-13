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
 * Enables duplicating a single element using together with bulk_element_action.php
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';

/**
 * Initialize duplicate functionality
 */
export const init = () => {
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.duplicate-element');
        if (!button) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();

        // Disable the button
        button.disabled = true;

        const elementId = button.dataset.id;
        const catid = M.cfg.catid || new URLSearchParams(window.location.search).get('catid');
        if (!catid) {
            button.disabled = false;
            return;
        }

        const payload = {
            action: 'duplicate',
            elementids: [elementId],
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
                    location.reload();
                } else {
                    Notification.alert('', data.message);
                }
            })
            .catch(() => {
                alert('Failed to duplicate element');
            })
            .finally(() => {
                button.disabled = false;
            });
    });
};