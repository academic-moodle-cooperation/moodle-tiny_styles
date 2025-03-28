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
 * Handles importing a json file for new categories and styles.
 *
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export const init = () => {

    const button = document.getElementById('uploadjson');
    if (!button) {
        return;
    }

    button.addEventListener('click', async function () {
        const fileInput = document.getElementById('importfile');
        const file = fileInput.files[0];
        if (!file) {
            return;
        }

        try {
            const text = await file.text();
            const jsonData = JSON.parse(text);

            const response = await fetch(
                M.cfg.wwwroot
                + '/admin/settings.php?section=tiny_styles_admin&action=import&sesskey='
                + encodeURIComponent(M.cfg.sesskey),
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(jsonData)
                });

            if (!response.ok) {
                const responseText = await response.text();
                throw new Error(`Server error: ${response.status} - ${response.statusText}\n${responseText}`);
            }

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert('Server error: ' + result.message);
            }
        } catch (e) {
            alert("Upload failed: " + e.message);
        }
    });
};