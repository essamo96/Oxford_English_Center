@extends('admin.layout.master')

@section('title', 'إعدادات الحضور والغياب')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إعدادات الحضور</li>
@stop

@section('page-content')
@php $active_menu = 'attendance_settings'; @endphp

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bold"><i class="bi bi-shield-check text-info me-2"></i>إعدادات أخذ الحضور</h3>
                </div>
            </div>
            <div class="card-body py-4">
                @include('admin.layout.masterLayouts.error')

                <form action="{{ route('admin.attendance.settings.save') }}" method="post">
                    @csrf

                    <div class="mb-6 p-4 rounded bg-light-info border border-info border-dashed">
                        <div class="fs-7 text-muted mb-2">عنوان IP الحالي — يمكنك إضافته مباشرة بنقرة واحدة</div>
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="fw-bold">جهازك: <span class="text-primary" id="client_ip_val">{{ $client_ip ?: '—' }}</span></span>
                            <button type="button" class="btn btn-sm btn-light-primary" data-add-ip="{{ $client_ip }}">
                                <i class="bi bi-plus-circle me-1"></i> أضف IP جهازي
                            </button>
                            <span class="fw-bold ms-3">الخادم: <span class="text-primary" id="server_ip_val">{{ $server_ip ?: '—' }}</span></span>
                            <button type="button" class="btn btn-sm btn-light-success" data-add-ip="{{ $server_ip }}">
                                <i class="bi bi-hdd-network me-1"></i> أضف IP الخادم
                            </button>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                            <span>عناوين IP المسموح بها (شبكة المركز)</span>
                            <button type="button" class="btn btn-sm btn-light-danger" id="clear_ips"><i class="bi bi-eraser me-1"></i> مسح الكل</button>
                        </label>
                        <textarea name="allowed_ips" id="allowed_ips" rows="4" class="form-control" placeholder="مثال:&#10;192.168.1.0/24&#10;212.34.56.78">{{ old('allowed_ips', $setting->allowed_ips) }}</textarea>
                        <div class="form-text">عنوان واحد أو نطاق CIDR في كل سطر (أو مفصولة بفواصل). اتركها فارغة للسماح من أي شبكة.</div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold">سماحية الوقت قبل/بعد المحاضرة (دقائق)</label>
                        <input type="number" name="grace_minutes" min="0" max="240" class="form-control"
                               value="{{ old('grace_minutes', $setting->grace_minutes) }}">
                        <div class="form-text">تتيح للمدرس أخذ الحضور قبل/بعد وقت المحاضرة بهذا العدد من الدقائق.</div>
                    </div>

                    <div class="separator my-5"></div>

                    <label class="form-check form-switch form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="checkbox" name="enforce_ip" value="1" {{ $setting->enforce_ip ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold ms-3">إلزام الاتصال بشبكة المركز (IP)</span>
                    </label>

                    <label class="form-check form-switch form-check-custom form-check-solid mb-4">
                        <input class="form-check-input" type="checkbox" name="enforce_time" value="1" {{ $setting->enforce_time ? 'checked' : '' }}>
                        <span class="form-check-label fw-bold ms-3">إلزام أخذ الحضور خلال وقت المحاضرة فقط</span>
                    </label>

                    <div class="text-end mt-6">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> حفظ الإعدادات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        function ipList() {
            return ($('#allowed_ips').val() || '').split(/[\s,;]+/).map(s => s.trim()).filter(Boolean);
        }
        $('[data-add-ip]').on('click', function () {
            const ip = ($(this).data('add-ip') || '').toString().trim();
            if (!ip) { Swal.fire('تنبيه', 'لا يوجد عنوان IP متاح.', 'warning'); return; }
            const list = ipList();
            if (list.includes(ip)) { Swal.fire('موجود', 'هذا العنوان مضاف مسبقاً.', 'info'); return; }
            list.push(ip);
            $('#allowed_ips').val(list.join('\n'));
        });
        $('#clear_ips').on('click', function () { $('#allowed_ips').val(''); });
    });
</script>
@stop
