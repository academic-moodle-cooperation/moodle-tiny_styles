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

define(['jquery'], function($) {
    return {
        init: function() {
            // Open and close the popup on button
            $('#open-icon-popup').on('click', function() {
                $('#icon-popup').show();
            });
            $('#close-icon-popup').on('click', function() {
                $('#icon-popup').hide();
            });

            // User clicks an icon on the grid.
            $('.icon-grid-item').on('click', function() {
                var iconFile = $(this).data('icon');
                $('input[name="selectedicon"]').val(iconFile);
                $('#button-icon-name').text(iconFile);
                $('#icon-popup').hide();
            });

            // Initialize button text if there's a pre-selected icon
            var initialIcon = $('input[name="selectedicon"]').val();
            if (initialIcon && initialIcon.length > 0) {
                $('#button-icon-name').text(initialIcon);
            }

        }
    };
});
