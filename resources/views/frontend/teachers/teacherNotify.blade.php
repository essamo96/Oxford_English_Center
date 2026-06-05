<div class="dash-card dash-card--flush ajax-content">
    <div class="detail-header" style="padding: 22px; border-radius: 12px 12px 0 0;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="m-0" style="color:#fff; font-weight: 700;"><i class="fa fa-envelope-o" style="color:var(--gold-mid);"></i> Admin Messages &amp; Notices</h3>
            <button class="dash-btn dash-btn--sm" onclick="location.reload()">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </button>
        </div>
    </div>

    <div style="padding: 18px;">
        @if(count($notifys) > 0)
            <div class="dash-list">
                @foreach ($notifys as $notify)
                    <div class="dash-list__item" style="align-items: flex-start; border-left: 4px solid {{ $notify->read_at ? 'var(--border-color)' : 'var(--gold-mid)' }};">
                        <div class="dash-list__ic"><i class="fa fa-info-circle"></i></div>
                        <div class="dash-list__main">
                            <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:8px;">
                                <div class="dash-list__title">{{ $notify->data['title'] }}</div>
                                <span class="small text-muted">{{ $notify->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="m-0" style="color: var(--text-secondary); line-height: 1.6; margin-top: 5px;">{{ $notify->data['body'] }}</p>
                            <div class="mt-10 small" style="color: var(--text-secondary); font-weight: 600;">
                                <i class="fa fa-user" style="color:var(--d-accent);"></i> From: {{ $notify->data['sender_name'] }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fa fa-envelope-open-o"></i>
                <p>No messages or notices found at the moment.</p>
            </div>
        @endif
    </div>
</div>
