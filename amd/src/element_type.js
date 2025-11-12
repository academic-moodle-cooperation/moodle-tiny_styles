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
 * Automatically updates the element type (inline/block) based on CSS class selection.
 *
 * @ package tiny_styles
 * @author Karri Pajarinen
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check if selected CSS class contains 'alert' or 'badge' and update type field accordingly.
 */
const checkForAlertClass = () => {
    const selectedClass = document.getElementById('id_cssclasses').value;
    if (selectedClass && selectedClass.indexOf('alert') !== -1) {
        document.getElementById('id_type').value = 'block';
    }
    if (selectedClass && selectedClass.indexOf('badge') !== -1) {
        document.getElementById('id_type').value = 'inline';
    }
};

/**
 * Helper function to ensure DOM is ready before executing.
 * @param {Function} fn Function to execute when DOM is ready
 */
const docReady = (fn) => {
    // If document is already loaded, run the function
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(fn, 1); // Slight delay to ensure DOM is fully available
        return;
    }

    // Otherwise, wait for DOMContentLoaded
    document.addEventListener('DOMContentLoaded', fn);
};

/**
 * Initialize element type detection functionality
 */
export const init = () => {
    docReady(() => {
        const cssClassesField = document.getElementById('id_cssclasses');

        if (cssClassesField) {
            cssClassesField.addEventListener('change', () => {
                checkForAlertClass();
            });

            // Run immediately after DOM is ready
            checkForAlertClass();
        } else {
            // If the element wasn't found, run again after a short delay
            setTimeout(() => {
                const retryField = document.getElementById('id_cssclasses');
                if (retryField) {
                    retryField.addEventListener('change', () => {
                        checkForAlertClass();
                    });
                    checkForAlertClass();
                }
            }, 100);
        }
    });
};
