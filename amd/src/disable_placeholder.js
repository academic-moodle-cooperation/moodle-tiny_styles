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
 * Disables the empty placeholder option in the CSS classes dropdown.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Disable the empty placeholder option in the select dropdown.
 */
const disableEmptyOption = () => {
    const select = document.querySelector('select[name="cssclasses"]');
    if (select) {
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === '') {
                select.options[i].disabled = true;
                break;
            }
        }
    }
};

/**
 * Helper function to ensure DOM is ready before executing.
 * @param {Function} fn Function to execute when DOM is ready
 */
const docReady = (fn) => {
    // If document is already loaded, run the function
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(fn, 1);
        return;
    }
    // Wait for document.
    document.addEventListener('DOMContentLoaded', fn);
};

/**
 * Initialize placeholder disabling functionality
 */
export const init = () => {
    docReady(() => {
        disableEmptyOption();
    });
};
