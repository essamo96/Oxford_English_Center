@extends('admin.layout.master')
@section('title', 'إدارة مواعيد المجموعات (Calendar)')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الصفحة الرئيسية</a>
    </li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">التقويم الدراسي</li>
@stop

@section('css')
    <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .fc-event { cursor: pointer; }
        .conflict-event { border: 2px solid #F64E60 !important; box-shadow: 0 0 0 1px #F64E60; }
        .fc-content { line-height: 1.2; }
        .cal-legend { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; }
        .cal-legend .dot { width: 12px; height: 12px; border-radius: 3px; display: inline-block; margin-inline-end: .35rem; }
        #kt_calendar_app .fc-day-today { background: rgba(54,153,255,.06) !important; }
    </style>
@stop

@section('page-content')
    <div class="card">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-4"></i>
                    <h3 class="card-label fw-bold text-gray-800">تقويم المجموعات الدراسية</h3>
                </div>
            </div>
            <div class="card-toolbar">
                <div class="d-flex flex-stack gap-3">
                    <div class="w-250px">
                        <input type="text" id="student_filter" class="form-control form-control-solid" placeholder="ابحث عن طالب (الاسم، الجوال، الإيميل)..." />
                    </div>
                    <div class="w-200px">
                        <select id="teacher_filter" class="form-select form-select-solid" data-control="select2" data-placeholder="تصفية حسب المدرس">
                            <option value=""></option>
                            <option value="0">كل المدرسين</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary" id="refresh_calendar">
                        <i class="ki-duotone ki-arrows-circle fs-2"></i> تحديث
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="cal-legend mb-5 text-gray-600 fs-7">
                <span><span class="dot" style="background:#3699FF"></span> جلسة عادية</span>
                <span><span class="dot" style="background:#F64E60"></span> تعارض في جدول المدرس</span>
                <span class="text-muted">اضغط على أي جلسة لعرض التفاصيل ورابط المجموعة.</span>
            </div>
            <div id="kt_calendar_app"></div>
        </div>
    </div>

    <!-- Modal: View Event Details -->
    <div class="modal fade" id="kt_modal_view_event" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <div class="modal-header border-0 justify-content-end">
                    <div class="btn btn-icon btn-sm btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body pt-0 pb-20 px-lg-17">
                    <div class="d-flex id-target align-items-center mb-9">
                        <div class="symbol symbol-60px me-5">
                            <span class="symbol-label bg-light-primary">
                                <i class="ki-duotone ki-calendar-8 fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <h1 class="text-dark fw-bold" id="event_title">---</h1>
                            <div class="d-flex align-items-center">
                                <span class="badge badge-light-success fw-bold fs-7 px-3 py-2" id="event_program">---</span>
                                <span class="badge badge-light-danger fw-bold fs-7 px-3 py-2 ms-2 d-none" id="conflict_warning">
                                    <i class="bi bi-exclamation-triangle-fill fs-7 me-1 text-danger"></i> يوجد تعارض
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row row-cols-lg-2 g-10">
                        <div class="col">
                            <div class="d-flex align-items-center mb-7">
                                <i class="bi bi-person fs-1 text-muted me-5"></i>
                                <div class="fs-6">
                                    <div class="fw-bold text-gray-800">المدرس</div>
                                    <div class="text-gray-400" id="event_teacher">---</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-7">
                                <i class="bi bi-clock fs-1 text-muted me-5"></i>
                                <div class="fs-6">
                                    <div class="fw-bold text-gray-800">الوقت</div>
                                    <div class="text-gray-400" id="event_time">---</div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center mb-7">
                                <i class="bi bi-people fs-1 text-muted me-5"></i>
                                <div class="fs-6">
                                    <div class="fw-bold text-gray-800">عدد الطلاب</div>
                                    <div class="text-gray-400"><span id="event_students">0</span> طالب</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-7">
                                <i class="bi bi-geo-alt fs-1 text-muted me-5"></i>
                                <div class="fs-6">
                                    <div class="fw-bold text-gray-800">رابط المجموعة</div>
                                    <a href="#" class="text-primary fw-semibold" id="event_link" target="_blank">فتح (Zoom/Drive)</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
    <script>
        var calendarEl = document.getElementById('kt_calendar_app');
        var calendar;

        document.addEventListener('DOMContentLoaded', function() {
            calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                initialView: 'dayGridMonth',
                locale: 'ar',
                direction: 'rtl',
                height: 'auto',
                navLinks: true,
                selectable: false,
                editable: false,
                dayMaxEvents: true,
                nowIndicator: true,
                slotMinTime: '07:00:00',
                slotMaxTime: '23:00:00',
                expandRows: true,
                eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: true },
                eventClassNames: function(arg) {
                    return (arg.event.extendedProps && arg.event.extendedProps.conflict) ? ['conflict-event'] : [];
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: '{{ route("calendar.events") }}',
                        type: 'GET',
                        data: {
                            start: moment(fetchInfo.startStr).format('YYYY-MM-DD'),
                            end: moment(fetchInfo.endStr).format('YYYY-MM-DD'),
                            teacher_id: $('#teacher_filter').val(),
                            student_search: $('#student_filter').val()
                        },
                        success: function(response) {
                            if (response && response.error) {
                                if (response.type === 'not_enrolled') {
                                    Swal.fire({
                                        title: 'تنبيه',
                                        text: response.message,
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'إضافة لمجموعة',
                                        cancelButtonText: 'إلغاء',
                                        customClass: {
                                            confirmButton: "btn btn-primary",
                                            cancelButton: "btn btn-light"
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed && response.student_encrypted_id) {
                                            // Redirect to group addition page
                                            window.location.href = "{{ url('/admin/students/groups/add') }}/" + response.student_encrypted_id;
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'ملاحظة',
                                        text: response.message,
                                        icon: 'info',
                                        confirmButtonText: 'حسناً',
                                        customClass: { confirmButton: "btn btn-primary" }
                                    });
                                }
                                successCallback([]);
                            } else {
                                successCallback(response);
                            }
                        },
                        error: function(err) {
                            failureCallback(err);
                        }
                    });
                },
                eventContent: function(arg) {
                    let conflictIcon = (arg.event.extendedProps && arg.event.extendedProps.conflict) ? '⚠️ ' : '';
                    let teacherName = (arg.event.extendedProps && arg.event.extendedProps.teacher) ? arg.event.extendedProps.teacher : 'غير محدد';
                    let eventTitle = arg.event.title || 'بدون عنوان';
                    
                    return {
                        html: `<div class="fc-content p-1">
                                    <div class="fc-title fw-bold">${conflictIcon}${eventTitle}</div>
                                    <div class="fc-description fs-8 opacity-75">${teacherName}</div>
                               </div>`
                    };
                },
                eventDidMount: function(info) {
                    let props = info.event.extendedProps || {};
                    let teacher = props.teacher || 'غير محدد';
                    let students = props.students || 0;
                    let program = props.program || 'بدون برنامج';
                    let conflict = props.conflict ? '⚠️ يوجد تعارض!' : '';

                    let tooltipContent = `
                        المدرس: ${teacher}
                        عدد الطلاب: ${students}
                        البرنامج: ${program}
                        ${conflict}
                    `;
                    $(info.el).tooltip({
                        title: tooltipContent.trim(),
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    });
                },
                eventClick: function(info) {
                    let props = info.event.extendedProps || {};
                    $('#event_title').text(info.event.title || '---');
                    $('#event_program').text(props.program || '---');
                    $('#event_teacher').text(props.teacher || '---');
                    $('#event_students').text(props.students || 0);

                    let start = moment(info.event.start).format('hh:mm A');
                    let end = info.event.end ? moment(info.event.end).format('hh:mm A') : '';
                    $('#event_time').text(end ? (start + ' - ' + end) : start);

                    // Wire the Zoom/Drive link (or disable it when none exists).
                    let $link = $('#event_link');
                    if (props.link) {
                        $link.attr('href', props.link).removeClass('disabled text-muted').addClass('text-primary');
                    } else {
                        $link.attr('href', '#').removeClass('text-primary').addClass('disabled text-muted');
                    }

                    if (props.conflict) {
                        $('#conflict_warning').removeClass('d-none');
                    } else {
                        $('#conflict_warning').addClass('d-none');
                    }

                    $('#kt_modal_view_event').modal('show');
                },
                loading: function(isLoading) {
                    $('#refresh_calendar').prop('disabled', isLoading)
                        .find('i').toggleClass('spinner-border spinner-border-sm', isLoading);
                }
            });

            calendar.render();

            $('#teacher_filter').on('change', function() {
                calendar.refetchEvents();
            });

            // Search on Enter, plus a debounced auto-search while typing.
            let searchTimer = null;
            $('#student_filter').on('keyup', function(e) {
                clearTimeout(searchTimer);
                if (e.which === 13) {
                    calendar.refetchEvents();
                    return;
                }
                let val = $(this).val().trim();
                // Refetch when cleared, or once the query is specific enough.
                if (val.length === 0 || val.length >= 3) {
                    searchTimer = setTimeout(function() {
                        calendar.refetchEvents();
                    }, 500);
                }
            });

            $('#refresh_calendar').on('click', function() {
                calendar.refetchEvents();
            });
        });
    </script>
@stop
