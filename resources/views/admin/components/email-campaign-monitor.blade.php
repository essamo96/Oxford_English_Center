{{-- Email Sending Status Component - Global, reusable, non-blocking --}}
<div id="email-campaign-monitor" style="display:none;" class="position-fixed bottom-0 end-0 m-5" style="z-index:1060; min-width:380px; max-width:420px;">
    <div class="card shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
        {{-- Header --}}
        <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between" id="ecm-header" style="background: linear-gradient(135deg, #002147 0%, #003d7a 100%); cursor:pointer;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-envelope-paper-fill text-white fs-4"></i>
                <span class="fw-bold text-white fs-7" id="ecm-title">Sending Campaign...</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-icon btn-sm btn-color-white" id="ecm-minimize" title="Minimize">
                    <i class="bi bi-dash-lg"></i>
                </button>
                <button class="btn btn-icon btn-sm btn-color-white" id="ecm-close" title="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        {{-- Body --}}
        <div class="card-body p-4" id="ecm-body">
            {{-- Status Badge --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge badge-light-info fs-8" id="ecm-status-badge">🟡 In Progress</span>
                <span class="text-muted fs-8" id="ecm-eta">ETA: Calculating...</span>
            </div>

            {{-- Progress Bar --}}
            <div class="progress h-10px mb-3" style="border-radius: 6px;">
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="ecm-progress-bar" role="progressbar" style="width: 0%"></div>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <span class="text-gray-600 fs-8" id="ecm-progress-text">0%</span>
                <span class="text-gray-600 fs-8" id="ecm-speed">—</span>
            </div>

            {{-- Counters --}}
            <div class="row g-3 text-center">
                <div class="col-4">
                    <div class="border border-dashed border-gray-300 rounded py-2 px-1">
                        <div class="fw-bold text-gray-800 fs-6" id="ecm-total">0</div>
                        <div class="text-gray-500 fs-8">Total</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border border-dashed border-success rounded py-2 px-1 bg-light-success">
                        <div class="fw-bold text-success fs-6" id="ecm-sent">0</div>
                        <div class="text-success fs-8">Sent</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border border-dashed border-danger rounded py-2 px-1 bg-light-danger">
                        <div class="fw-bold text-danger fs-6" id="ecm-failed">0</div>
                        <div class="text-danger fs-8">Failed</div>
                    </div>
                </div>
            </div>

            {{-- Error Details Button --}}
            <div id="ecm-error-section" class="mt-3" style="display:none;">
                <button class="btn btn-sm btn-light-danger w-100" id="ecm-show-errors">
                    <i class="bi bi-exclamation-triangle me-1"></i> View Failure Details
                </button>
            </div>

            {{-- View Campaign Link --}}
            <div id="ecm-link-section" class="mt-3 text-center" style="display:none;">
                <a href="#" id="ecm-campaign-link" class="btn btn-sm btn-light-primary w-100">
                    <i class="bi bi-eye me-1"></i> View Campaign Details
                </a>
            </div>
        </div>
    </div>
</div>

<script>
window.EmailCampaignMonitor = {
    campaignId: null,
    pollInterval: null,
    startTime: null,
    isMinimized: false,

    start: function(campaignId, totalRecipients, redirectUrl) {
        this.campaignId = campaignId;
        this.startTime = Date.now();

        var $monitor = $('#email-campaign-monitor');
        $monitor.show().css('z-index', 1060);

        $('#ecm-total').text(totalRecipients || '—');
        $('#ecm-sent').text('0');
        $('#ecm-failed').text('0');
        $('#ecm-progress-bar').css('width', '0%');
        $('#ecm-progress-text').text('0%');
        $('#ecm-status-badge').text('🟡 In Progress').removeClass().addClass('badge badge-light-info fs-8');
        $('#ecm-title').text('Sending Campaign...');
        $('#ecm-error-section').hide();
        $('#ecm-body').show();

        if (redirectUrl) {
            $('#ecm-campaign-link').attr('href', redirectUrl);
            $('#ecm-link-section').show();
        }

        this.poll();
        var self = this;
        this.pollInterval = setInterval(function() { self.poll(); }, 3000);
    },

    poll: function() {
        var self = this;
        if (!this.campaignId) return;

        $.get('/admin/email-campaigns/status/' + this.campaignId, function(res) {
            var percent = res.percentage || 0;
            var processed = res.sent + res.failed;
            var elapsed = (Date.now() - self.startTime) / 1000;
            var speed = elapsed > 0 ? Math.round((processed / elapsed) * 60) : 0;

            // Update UI
            $('#ecm-sent').text(res.sent);
            $('#ecm-failed').text(res.failed);
            $('#ecm-progress-bar').css('width', percent + '%');
            $('#ecm-progress-text').text(percent + '%');
            $('#ecm-speed').text(speed + ' emails/min');

            // ETA
            if (!res.completed && speed > 0) {
                var remaining = res.total - processed;
                var etaSeconds = Math.ceil((remaining / speed) * 60);
                var min = Math.floor(etaSeconds / 60);
                var sec = etaSeconds % 60;
                $('#ecm-eta').text('ETA: ' + min + 'm ' + sec + 's');
            }

            // Completion
            if (res.completed) {
                clearInterval(self.pollInterval);
                self.pollInterval = null;

                $('#ecm-progress-bar').removeClass('progress-bar-striped progress-bar-animated');
                $('#ecm-eta').text('Completed');

                if (res.status === 'completed') {
                    $('#ecm-status-badge').text('🟢 Success').removeClass().addClass('badge badge-light-success fs-8');
                    $('#ecm-title').text('Campaign Sent ✓');
                    $('#ecm-progress-bar').removeClass('bg-primary').addClass('bg-success');
                    toastr.success('Campaign finished: ' + res.sent + ' emails sent.');
                } else if (res.status === 'completed_with_errors') {
                    $('#ecm-status-badge').text('🟠 Completed with Warnings').removeClass().addClass('badge badge-light-warning fs-8');
                    $('#ecm-title').text('Finished with Errors ⚠');
                    $('#ecm-progress-bar').removeClass('bg-primary').addClass('bg-warning');
                    $('#ecm-error-section').show();
                    toastr.warning('Completed with ' + res.failed + ' failures.');
                } else if (res.status === 'failed') {
                    $('#ecm-status-badge').text('🔴 Campaign Failed').removeClass().addClass('badge badge-light-danger fs-8');
                    $('#ecm-title').text('Critical Failure ✗');
                    $('#ecm-progress-bar').removeClass('bg-primary').addClass('bg-danger');
                    $('#ecm-error-section').show();
                    toastr.error('Campaign failed to process.');
                }
            }
        });
    },

    stop: function() {
        if (this.pollInterval) clearInterval(this.pollInterval);
        this.campaignId = null;
        $('#email-campaign-monitor').fadeOut(300);
    }
};

$(function() {
    // Minimize/Restore
    $('#ecm-minimize, #ecm-header').on('click', function(e) {
        if (e.target.id === 'ecm-close' || $(e.target).closest('#ecm-close').length) return;
        var $body = $('#ecm-body');
        $body.slideToggle(200);
    });

    // Close
    $('#ecm-close').on('click', function(e) {
        e.stopPropagation();
        window.EmailCampaignMonitor.stop();
    });

    // Show error details
    $('#ecm-show-errors').on('click', function() {
        var url = $('#ecm-campaign-link').attr('href');
        if (url && url !== '#') {
            window.open(url, '_blank');
        }
    });
});
</script>
