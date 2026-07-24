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
 * Replaces native browser confirm dialogs on delete links with a Moodle modal.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import {get_strings as getStrings} from 'core/str';

/**
 * Initialise Moodle modal delete confirmation on all delete links.
 */
export const init = async() => {
    const [deleteLabel, cancelLabel] = await getStrings([
        {key: 'delete', component: 'tiny_styles'},
        {key: 'cancel'},
    ]);

    document.addEventListener('click', (e) => {
        const link = e.target.closest('[data-action="confirm-delete"]');
        if (!link) {
            return;
        }
        e.preventDefault();

        const message = link.dataset.confirm ?? '';
        const deleteUrl = link.href;

        Notification.confirm(
            deleteLabel,
            message,
            deleteLabel,
            cancelLabel,
            () => {
                window.location.href = deleteUrl;
            }
        );
    });
};
