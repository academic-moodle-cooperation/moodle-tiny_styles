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
 * JS support to select an icon for a category
 *
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Initialize icon selector functionality
 *
 * @return {void}
 */
export const init = () => {
    // Open and close the popup on button
    const openIconPopupBtn = document.getElementById('open-icon-popup');
    const closeIconPopupBtn = document.getElementById('close-icon-popup');
    const iconPopup = document.getElementById('icon-popup');
    const selectedIconInput = document.querySelector('input[name="selectedicon"]');
    const buttonIconName = document.getElementById('button-icon-name');

    if (openIconPopupBtn) {
        openIconPopupBtn.addEventListener('click', () => {
            if (iconPopup) {
                iconPopup.style.display = 'block';
            }
        });
    }

    if (closeIconPopupBtn) {
        closeIconPopupBtn.addEventListener('click', () => {
            if (iconPopup) {
                iconPopup.style.display = 'none';
            }
        });
    }

    // User clicks an icon on the grid
    const iconGridItems = document.querySelectorAll('.icon-grid-item');
    iconGridItems.forEach(item => {
        item.addEventListener('click', () => {
            const iconFile = item.dataset.icon;

            if (selectedIconInput) {
                selectedIconInput.value = iconFile;
            }

            if (buttonIconName) {
                buttonIconName.textContent = iconFile;
            }

            if (iconPopup) {
                iconPopup.style.display = 'none';
            }
        });
    });

    // Button text for existing icon
    if (selectedIconInput && buttonIconName) {
        const initialIcon = selectedIconInput.value;
        if (initialIcon && initialIcon.length > 0) {
            buttonIconName.textContent = initialIcon;
        }
    }
};