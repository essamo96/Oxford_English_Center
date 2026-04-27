{{--
    ======================================================
    Email Composer - Unified Campaign Email Utility
    ======================================================
    Usage:
      window.EmailComposer.send({
        type: 'bulk'|'single',
        recipients: [emails] or id (for single),
        url: route_url,
        title: 'Dialog title',
        defaultSubject: '',
        defaultBody: '',
        onSuccess: function(response) {}  // optional
      });
    ======================================================
--}}
<script>
window.EmailComposer = {

    /**
     * Open SweetAlert2 dialog, collect data, send AJAX, and trigger CampaignMonitor.
     *
     * @param {object} options
     */
    send: function(options) {
        var self = this;

        Swal.fire({
            title: options.title || 'Send Email Notification',
            icon: 'info',
            html: `
                <div class="fv-row mb-4 text-start">
                    <label class="form-label fw-semibold required">Subject Line:</label>
                    <input type="text" class="form-control form-control-solid" id="ec-subject" placeholder="Enter email subject..." value="${options.defaultSubject || ''}">
                </div>
                <div class="fv-row mb-4 text-start">
                    <label class="form-label fw-semibold required">Message Body:</label>
                    <textarea class="form-control form-control-solid" id="ec-body" rows="5" placeholder="Compose your message here...">${options.defaultBody || ''}</textarea>
                </div>
                <div class="fv-row text-start">
                    <label class="form-label fw-semibold">Attachment <span class="text-muted fs-8">(Optional)</span>:</label>
                    <input type="file" class="form-control form-control-solid" id="ec-file">
                </div>`,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-send me-1"></i> Send Now',
            cancelButtonText: 'Cancel',
            customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' },
            buttonsStyling: false,
            focusConfirm: false,
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            preConfirm: () => {
                const subject = document.getElementById('ec-subject').value.trim();
                const body    = document.getElementById('ec-body').value.trim();
                const file    = document.getElementById('ec-file').files[0];
                if (!subject || !body) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }
                return { subject, body, file };
            }
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var formData = new FormData();
            formData.append('title',   result.value.subject);
            formData.append('message', result.value.body);
            formData.append('_token',  $('meta[name="csrf-token"]').attr('content'));

            if (result.value.file) formData.append('file', result.value.file);

            // Append recipients
            if (options.type === 'single') {
                formData.append('id', options.id);
            } else if (options.type === 'bulk') {
                $.each(options.recipients, function(i, email) {
                    formData.append('emails[]', email);
                });
            }

            $.ajax({
                type: 'POST',
                url: options.url,
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: () => Swal.showLoading(),
                success: function(response) {
                    Swal.close();
                    // Trigger Campaign Monitor if campaign returned
                    if (response.campaign_id) {
                        var total = (options.type === 'single') ? 1 : (options.recipients ? options.recipients.length : 1);
                        window.EmailCampaignMonitor.start(response.campaign_id, response.total_recipients || total, response.redirect_url);
                        toastr.info('Campaign launched successfully. Monitoring progress...');
                    } else {
                        toastr.success(response.message || 'Email sent successfully');
                    }
                    // Custom callback
                    if (typeof options.onSuccess === 'function') options.onSuccess(response);
                },
                error: function(xhr) {
                    Swal.close();
                    var msg = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'An error occurred during sending!';
                    Swal.fire({
                        text: msg, icon: 'error', buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            });
        });
    },

    /**
     * Helper to collect checked emails from the page
     * @param {string} selector - CSS selector for checkboxes (default: '.checkboxes:checked')
     */
    collectEmails: function(selector) {
        selector = selector || '.checkboxes:checked';
        var emails = [];
        $(selector).each(function() {
            var email = $(this).data('email');
            if (email) emails.push(email);
        });
        return emails;
    }
};
</script>
