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
 * Enables moving the categories up and down seamlessly.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['core/ajax', 'core/notification'], function(Ajax, Notification) {
    const moveRow = (row, direction) => {
        if (!row) {
            return;
        }
        const sibling = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;

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
            button.addEventListener('click', async(event) => {
                event.preventDefault();

                const action = button.dataset.action;
                const id = parseInt(button.dataset.id);
                if (isNaN(id)) {
                    return;
                }
                const row = button.closest('tr');
                moveRow(row, action === 'moveup' ? 'up' : 'down');

                try {
                    await Ajax.call([{
                        methodname: 'tiny_styles_sort_categories',
                        args: {
                            action: action,
                            categoryid: id
                        }
                    }])[0];

                } catch (error) {
                    Notification.exception(error);
                }
            });
        });
    };

    return {init};
});
