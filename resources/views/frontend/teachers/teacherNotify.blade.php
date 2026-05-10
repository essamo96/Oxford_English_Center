<div class="info-card" style="padding: 0; overflow: hidden; border: none; background: transparent; box-shadow: none;">
    <div class="detail-header" style="background: linear-gradient(135deg, #1a2744 0%, #2d3748 100%); padding: 25px; border-radius: 12px 12px 0 0; color: white;">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="m-0" style="color: white; font-weight: 700;"><i class="fa fa-envelope-o text-accent"></i> Admin Messages & Notices</h3>
            <button class="btn btn-sm modern-back-btn" onclick="location.reload()">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </button>
        </div>
    </div>

    <div class="dashboard-content" style="margin-top: 0; border-radius: 0 0 12px 12px;">
        @if(count($notifys) > 0)
            @foreach ($notifys as $notify)
                <div class="modern-notice {{ $notify->read_at ? '' : 'unread' }}" style="background: #fff; border: 1px solid #eee; margin-bottom: 15px; padding: 20px; border-radius: 12px; display: flex; gap: 20px; border-left: 5px solid {{ $notify->read_at ? '#eee' : 'var(--accent)' }};">
                    <div class="notice-icon-box" style="width: 50px; height: 50px; background: var(--bg-light); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="fa fa-info-circle"></i>
                    </div>
                    <div style="flex-grow: 1;">
                        <div class="d-flex justify-content-between align-items-start mb-5 flex-wrap">
                            <h4 class="m-0" style="font-weight: 700; color: var(--primary); font-size: 16px;">{{ $notify->data['title'] }}</h4>
                            <span class="small text-muted">{{ $notify->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="m-0" style="color: #4a5568; line-height: 1.6; margin-top: 5px;">{{ $notify->data['body'] }}</p>
                        <div class="mt-10 small" style="color: var(--primary); font-weight: 600;">
                            <i class="fa fa-user text-accent"></i> From: {{ $notify->data['sender_name'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center p-50">
                <i class="fa fa-envelope-open-o fa-4x text-muted mb-20" style="opacity: 0.3;"></i>
                <p class="text-muted">No messages or notices found at the moment.</p>
            </div>
        @endif
    </div>
</div>
