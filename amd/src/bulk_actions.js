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
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    return {
        init: function() {
            $(document).ready(function() {
                $('#bulk-actions-dropdown').on('change', function() {
                    var action = $(this).val();
                    if (!action) {
                        return;
                    }
                    // Gets selected element IDs.
                    var selected = [];
                    $('input[name="selected_elements[]"]:checked').each(function() {
                        selected.push($(this).val());
                    });
                    if (selected.length === 0) {
                        $('#bulk-action-warning').show();
                        $(this).val('');
                        return;
                    } else {
                        $('#bulk-action-warning').hide();
                    }
                    // Confirm deletion.
                    if (action === 'delete' && !confirm('Are you sure you want to delete the selected elements?')) {
                        $(this).val('');
                        return;
                    }

                    // Retrieves category id from M.cfg or URL.
                    var catid = M.cfg.catid || new URLSearchParams(window.location.search).get('catid');
                    if (!catid) {
                        return;
                    }

                    var payload = {
                        action: action,
                        elementids: selected,
                        categoryid: parseInt(catid)
                    };

                    var ajaxUrl = M.cfg.wwwroot +
                        '/lib/editor/tiny/plugins/styles/ajax/bulk_element_action.php?sesskey=' +
                        encodeURIComponent(M.cfg.sesskey);
                    $.ajax({
                        url: ajaxUrl,
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        dataType: 'json'
                    }).done(function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }).fail(function(xhr, status, error) {
                        alert(error);
                    });
                });
            });
        }
    };
});
