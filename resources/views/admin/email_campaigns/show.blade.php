@extends('admin.layout.master')

@section('title', 'تفاصيل حملة الإرسال')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('admin.email_campaigns.index') }}" class="text-muted text-hover-info">حملات البريد</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">تفاصيل الحملة</li>
@stop

@section('page-content')
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-4">
            <div class="card card-flush h-lg-100 shadow-sm">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">بيانات الحملة</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6">معلومات الموضوع والمرسل</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-stack mr-4">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">الموضوع:</div>
                        <div class="d-flex align-items-center">
                            <span class="text-gray-900 fw-bold fs-6">{{ $campaign->subject }}</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack mr-4">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">المرسل:</div>
                        <div class="text-gray-900 fw-bold fs-6">{{ $campaign->admin->name ?? 'النظام' }}</div>
                    </div>
                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack mr-4">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">بدأت في:</div>
                        <div class="text-gray-900 fw-bold fs-6">{{ $campaign->started_at->format('Y-m-d H:i') }}</div>
                    </div>
                    @if($campaign->completed_at)
                    <div class="separator separator-dashed my-3"></div>
                    <div class="d-flex flex-stack mr-4">
                        <div class="text-gray-700 fw-semibold fs-6 me-2">انتهت في:</div>
                        <div class="text-gray-900 fw-bold fs-6">{{ $campaign->completed_at->format('Y-m-d H:i') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card card-flush h-lg-100 shadow-sm" id="progress_card">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">حالة الإرسال الحالية</span>
                        <span class="text-warning mt-1 fw-semibold fs-6" id="estimated_time">جارِ الحساب...</span>
                    </h3>
                    <div class="card-toolbar">
                        <span class="badge" id="campaign_status_badge"></span>
                    </div>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-column mb-7">
                        <div class="d-flex flex-stack mb-2">
                            <span class="text-gray-400 fw-bold fs-7">نسبة الإنجاز</span>
                            <span class="text-gray-400 fw-bold fs-7" id="progress_percent">0%</span>
                        </div>
                        <div class="progress h-15px w-100 bg-light-primary">
                            <div class="progress-bar bg-primary" id="progress_bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="row g-5">
                        <div class="col-4">
                            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 mb-3 text-center">
                                <div class="fs-6 text-gray-800 fw-bold" id="total_count">{{ $campaign->total_recipients }}</div>
                                <div class="fw-semibold text-gray-400">الإجمالي</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border border-success border-dashed rounded min-w-100px py-3 px-4 mb-3 text-center bg-light-success">
                                <div class="fs-6 text-success fw-bold" id="sent_count">{{ $campaign->sent_count }}</div>
                                <div class="fw-semibold text-success">نجح</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border border-danger border-dashed rounded min-w-100px py-3 px-4 mb-3 text-center bg-light-danger">
                                <div class="fs-6 text-danger fw-bold" id="failed_count">{{ $campaign->failed_count }}</div>
                                <div class="fw-semibold text-danger">فشل</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-5">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold text-gray-800">سجل عمليات الحملة</span>
                <span class="text-gray-400 mt-1 fw-semibold fs-6">تفاصيل نجاح أو فشل كل بريد</span>
            </h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped" id="logs_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>المستلم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الحالة</th>
                            <th>ملاحظات / خطأ</th>
                            <th>الوقت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->logs as $log)
                        <tr>
                            <td>{{ $log->recipient_name ?? '---' }}</td>
                            <td>{{ $log->recipient_email }}</td>
                            <td>
                                @if($log->status == 'success')
                                    <span class="badge badge-light-success">نجاح</span>
                                @elseif($log->status == 'pending')
                                    <span class="badge badge-light-warning">قيد الانتظار</span>
                                @else
                                    <span class="badge badge-light-danger">فشل</span>
                                @endif
                            </td>
                            <td>
                                @if($log->status == 'failed')
                                    <span class="text-danger fs-7">{{ $log->error_message ?: 'خطأ غير معروف' }}</span>
                                @elseif($log->status == 'pending')
                                    <span class="text-muted fs-7">في قائمة الانتظار...</span>
                                @else
                                    <span class="text-success fs-7">تم الإرسال بنجاح</span>
                                @endif
                            </td>
                            <td>{{ $log->created_at->format('H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $('#logs_table').DataTable({
        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json" },
        order: [[4, 'desc']]
    });

    var campaignId = "{{ $campaign->id }}";
    var isCompleted = {{ in_array($campaign->status, ['completed', 'completed_with_errors', 'failed']) ? 'true' : 'false' }};

    function updateStatus() {
        if (isCompleted) return;

        $.get("{{ url('admin/email-campaigns/status') }}/" + campaignId, function(res) {
            $('#sent_count').text(res.sent);
            $('#failed_count').text(res.failed);
            $('#progress_percent').text(res.percentage + '%');
            $('#progress_bar').css('width', res.percentage + '%').attr('aria-valuenow', res.percentage);
            
            // Estimated time calculation
            if (!res.completed) {
                let remaining = res.total - (res.sent + res.failed);
                let emailsPerMin = 40; // Estimated
                let totalSeconds = Math.ceil((remaining / emailsPerMin) * 60);
                
                let min = Math.floor(totalSeconds / 60);
                let sec = totalSeconds % 60;
                $('#estimated_time').text('الوقت المتبقي المتوقع: ' + min + ' دقيقة و ' + sec + ' ثانية');
            } else {
                $('#estimated_time').text('اكتملت العملية');
                location.reload(); // Reload to show final status and logs
            }

            // Status Badge
            let badgeClass = 'badge-light-primary';
            let statusText = res.status;
            switch(res.status) {
                case 'pending': badgeClass = 'badge-light-warning'; statusText = 'قيد الانتظار'; break;
                case 'sending': badgeClass = 'badge-light-info'; statusText = 'جاري الإرسال'; break;
                case 'completed': badgeClass = 'badge-light-success'; statusText = 'اكتمل بنجاح'; break;
                case 'completed_with_errors': badgeClass = 'badge-light-warning'; statusText = 'اكتمل مع أخطاء'; break;
                case 'failed': badgeClass = 'badge-light-danger'; statusText = 'فشل'; break;
            }
            $('#campaign_status_badge').removeClass().addClass('badge ' + badgeClass).text(statusText);

            if (res.completed) isCompleted = true;
        });
    }

    if (!isCompleted) {
        setInterval(updateStatus, 3000); // Pulse every 3 seconds
        updateStatus();
    } else {
        // Initial setup for completed campaigns
        updateStatus();
    }
</script>
@stop
