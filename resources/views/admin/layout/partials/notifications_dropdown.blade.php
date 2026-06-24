@php
    $branchId = auth()->guard('admin')->user()?->branch_id;
    $nd = \App\Support\NotifyCounts::allForView($branchId ? (int)$branchId : null);
    $total = $nd['total_notify_count'];
@endphp
@if($total > 0)
<div class="tab-content" id="rt-notif-tab-content">
    <div class="tab-pane fade show active" id="kt_topbar_notifications_1" role="tabpanel">
        <div class="scroll-y mh-325px my-5 px-8">
            @foreach($nd['notify_students'] as $st)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-primary text-primary fw-bold">{{ mb_substr($st->name,0,1) }}</span></div>
                    <div class="mb-0 me-2">
                        <a href="{{ route('notifications.mark_read', ['type'=>'booking','id'=>$st->id]) }}" class="fs-6 text-gray-800 text-hover-info fw-bold">طلب عضوية: {{ $st->name }}</a>
                        <div class="text-gray-400 fs-7">طالب جديد ينتظر التفعيل</div>
                    </div>
                </div>
                <span class="badge badge-light fs-8 text-muted">{{ $st->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
            @foreach($nd['notify_closed_clases'] as $cl)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-danger"><i class="ki-duotone ki-notification-on fs-2 text-danger"><span class="path1"></span><span class="path2"></span></i></span></div>
                    <div class="mb-0 me-2">
                        <a href="{{ route('notifications.mark_read', ['type'=>'closed_class','id'=>$cl->id]) }}" class="fs-6 text-gray-800 text-hover-info fw-bold">إغلاق مجموعة: {{ $cl->Groups->name ?? 'مجموعة' }}</a>
                        <div class="text-gray-400 fs-7">{{ $cl->Teacher->name ?? 'غير معروف' }} — {{ $cl->closed_date }}</div>
                    </div>
                </div>
                <span class="badge badge-light-danger fs-8">تنبيه</span>
            </div>
            @endforeach
            @foreach($nd['notify_student_payments'] as $pay)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-success"><i class="ki-duotone ki-dollar fs-2 text-success"><span class="path1"></span><span class="path2"></span></i></span></div>
                    <div class="mb-0 me-2">
                        <a href="{{ route('admin.financial.student-payments') }}" class="fs-6 text-gray-800 text-hover-info fw-bold">دفعة جديدة: {{ $pay->student->name ?? 'طالب' }}</a>
                        <div class="text-gray-400 fs-7">₪ {{ number_format($pay->amount_paid, 2) }} — {{ $pay->group->name ?? 'خارج المجموعة' }}</div>
                    </div>
                </div>
                <span class="badge badge-light-success fs-8">{{ $pay->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
            @if(count($nd['notify_students'])==0 && count($nd['notify_closed_clases'])==0 && count($nd['notify_student_payments'])==0)
            <div class="text-center py-8 text-gray-400 fs-7">لا توجد تنبيهات</div>
            @endif
        </div>
    </div>
    <div class="tab-pane fade" id="kt_topbar_notifications_2" role="tabpanel">
        <div class="scroll-y mh-325px my-5 px-8">
            @foreach($nd['notify_Groups'] as $gp)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center me-2">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-info"><i class="ki-duotone ki-chart-line fs-2 text-info"><span class="path1"></span><span class="path2"></span></i></span></div>
                    <div class="mb-0 me-2"><a href="#" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $gp->name }}</a><div class="text-gray-400 fs-7">تقدم: {{ $gp->progress }}%</div></div>
                </div>
                <span class="badge badge-light-info fs-8">{{ $gp->progress }}%</span>
            </div>
            @endforeach
            @if(count($nd['notify_Groups'])==0)<div class="text-center py-8 text-gray-400 fs-7">لا توجد تحديثات</div>@endif
        </div>
    </div>
    <div class="tab-pane fade" id="kt_topbar_notifications_3" role="tabpanel">
        <div class="scroll-y mh-325px my-5 px-8">
            @foreach($nd['notify_Students_Admin_Messages'] as $ms)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center me-2">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-warning"><i class="ki-duotone ki-message-text-2 fs-2 text-warning"><span class="path1"></span><span class="path2"></span></i></span></div>
                    <div class="mb-0 me-2"><a href="{{ route('notifications.mark_read', ['type'=>'student_message','id'=>$ms->student_id]) }}" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $ms->student->name ?? 'طالب' }}</a><div class="text-gray-400 fs-7 text-truncate w-150px">{{ $ms->content }}</div></div>
                </div>
                <span class="badge badge-light-warning fs-8">رسالة طالب</span>
            </div>
            @endforeach
            @foreach($nd['notify_Teachers_Admin_Messages'] as $mt)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center me-2">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-success"><i class="ki-duotone ki-sms fs-2 text-success"><span class="path1"></span><span class="path2"></span></i></span></div>
                    <div class="mb-0 me-2"><a href="{{ route('notifications.mark_read', ['type'=>'teacher_message','id'=>$mt->teacher_id]) }}" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $mt->teacher->name ?? 'معلم' }}</a><div class="text-gray-400 fs-7 text-truncate w-150px">{{ $mt->content }}</div></div>
                </div>
                <span class="badge badge-light-success fs-8">رسالة معلم</span>
            </div>
            @endforeach
            @foreach($nd['notify_contacts'] as $ct)
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center me-2">
                    <div class="symbol symbol-35px me-4"><span class="symbol-label bg-light-primary"><i class="ki-duotone ki-sms fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i></span></div>
                    <div class="mb-0 me-2"><a href="{{ route('notifications.mark_read', ['type'=>'contact','id'=>$ct->id]) }}" class="fs-6 text-gray-800 text-hover-info fw-bold">{{ $ct->name ?? 'زائر' }}</a><div class="text-gray-400 fs-7 text-truncate w-150px">{{ $ct->subject ?? 'اتصل بنا' }}</div></div>
                </div>
                <span class="badge badge-light-primary fs-8">اتصل بنا</span>
            </div>
            @endforeach
            @if(count($nd['notify_Students_Admin_Messages'])==0 && count($nd['notify_Teachers_Admin_Messages'])==0 && count($nd['notify_contacts'])==0)
            <div class="text-center py-8 text-gray-400 fs-7">لا توجد رسائل جديدة</div>
            @endif
        </div>
    </div>
</div>
@else
<div id="rt-notif-empty" class="d-flex flex-column px-9 items-center justify-content-center py-10">
    <div class="text-center">
        <img class="mw-100 mh-200px mb-5 theme-light-show" src="{{ asset('assets/media/illustrations/sketchy-1/1.png') }}" alt="">
        <img class="mw-100 mh-200px mb-5 theme-dark-show" src="{{ asset('assets/media/illustrations/sketchy-1/1-dark.png') }}" alt="">
        <div class="fw-bold fs-6 text-gray-800">لا توجد إشعارات جديدة</div>
        <div class="text-gray-400 fs-7 mt-1">أنت مطلع على كل شيء حالياً</div>
    </div>
</div>
@endif
<div class="py-3 text-center border-top">
    <a href="{{ route('dashboard.view') }}" class="btn btn-color-gray-600 btn-active-color-primary">عرض الكل <i class="ki-duotone ki-arrow-right fs-5"><span class="path1"></span><span class="path2"></span></i></a>
</div>
