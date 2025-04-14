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
 * @category    admin
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str'],
    function($, Ajax, Notification) {

        return {
            init: function() {
                $(document).on('click', '.duplicate-element', function(event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var $button = $(this);
                    $button.prop('disabled', true);

                    var elementid = $button.data('id');

                    var catid = M.cfg.catid || new URLSearchParams(window.location.search).get('catid');
                    if (!catid) {
                        $button.prop('disabled', false);
                        return;
                    }

                    var payload = {
                        action: 'duplicate',
                        elementids: [elementid],
                        categoryid: parseInt(catid)
                    };

                    var ajaxUrl = M.cfg.wwwroot +
                        '/lib/editor/tiny/plugins/styles/ajax/bulk_element_action.php?sesskey=' +
                        encodeURIComponent(M.cfg.sesskey);

                    //AJAX request to bulk actions php to duplicate the element
                    $.ajax({
                        url: ajaxUrl,
                        method: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify(payload)
                    }).done(function(data) {
                        if (data.success) {
                            location.reload();
                        } else {
                            Notification.alert( data.message);
                        }
                    }).fail(function() {
                        alert('Failed to duplicate element');
                    }).always(function() {
                        $button.prop('disabled', false);
                    });
                });
            }
        };
    });