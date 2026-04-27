 
$(document).on('click', '.status', function () {

    var id = $(this).data('href');

    $.ajax({
        type: 'POST',
        url: '<?= Route::has($status_route ?? $active_menu . '.status') ? route($status_route ?? $active_menu . '.status') : '#' ?>',
        data: {
            id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function (data) {
            toastr[data.status](data.message);
            
            // If activation triggered a welcome campaign, show monitor
            if (data.campaign_id && window.EmailCampaignMonitor) {
                window.EmailCampaignMonitor.start(
                    data.campaign_id,
                    data.total_recipients || 1,
                    data.redirect_url || null
                );
            }

            // Reload the table silently so the new icon/color shows up
            if (typeof table !== 'undefined') {
                table.ajax.reload(null, false);
            }
        },
        error: function () {
            Swal.fire({
                title: "Oops...",
                text: "Something went wrong!",
                icon: "error"
            });
        }
    });

});
    

