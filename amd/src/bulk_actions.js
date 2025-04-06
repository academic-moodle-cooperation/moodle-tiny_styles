define(['jquery'], function($) {
    return {
        init: function() {
            $(document).ready(function() {
                $('#bulk-actions-dropdown').on('change', function() {
                    var action = $(this).val();
                    if (!action) {
                        return;
                    }
                    // Selected element IDs.
                    var selected = [];
                    $('input[name="selected_elements[]"]:checked').each(function() {
                        selected.push($(this).val());
                    });
                    if (selected.length === 0) {
                        $(this).val('');
                        return;
                    }
                    // Confirm deletion.
                    // todo: change to a moodle core pop up
                    if (action === 'delete' && !confirm('Are you sure you want to delete the selected elements?')) {
                        $(this).val('');
                        return;
                    }

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
