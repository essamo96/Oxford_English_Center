@if ($x == 2)
    <div class="dropdown">
        <button class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            العمليات
        </button>

        <div class="dropdown-menu menu menu-sub menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-225px py-4"
            data-kt-menu="true">
            @can('admin.groups.edit')
                <div class="menu-item px-3">
                    <a href="{{ route('groups.edit', ['id' => Crypt::encrypt($id)]) }}" class="menu-link px-3">
                        <span class="menu-icon"><i class="ki-duotone ki-pencil fs-4 text-primary"><span class="path1"></span><span class="path2"></span></i></span>
                        <span class="menu-title">تعديل</span>
                    </a>
                </div>
            @endcan
            @can('admin.groups.student.view')
                <div class="menu-item px-3">
                    <a href="{{ route('groups.student.view', ['id' => Crypt::encrypt($id)]) }}" class="menu-link px-3">
                        <span class="menu-icon"><i class="ki-duotone ki-people fs-4 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                        <span class="menu-title">طلاب المجموعة</span>
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a href="{{ route('groups.student.degree', ['id' => Crypt::encrypt($id)]) }}" class="menu-link px-3">
                        <span class="menu-icon"><i class="ki-duotone ki-book-open fs-4 text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                        <span class="menu-title">درجات الطلاب</span>
                    </a>
                </div>
                @if ($teacher_id)
                    <div class="menu-item px-3">
                        <a href="{{ route('groups.student.attendance', ['teacher_id' => $teacher_id, 'group_id' => $id]) }}"
                            class="menu-link px-3">
                            <span class="menu-icon"><i class="ki-duotone ki-calendar-tick fs-4 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i></span>
                            <span class="menu-title">سجل الحضور والغياب</span>
                        </a>
                    </div>
                @endif
                <div class="menu-item px-3">
                    <a href="{{ route('groups.student.evaluation', ['id' => Crypt::encrypt($id)]) }}"
                        class="menu-link px-3">
                        <span class="menu-icon"><i class="ki-duotone ki-star fs-4 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                        <span class="menu-title">تقييمات الطلاب</span>
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a class="menu-link px-3" style="cursor:pointer;"
                        onclick="sendMaseageToGroupe('{{ Crypt::encrypt($id) }}')">
                        <span class="menu-icon"><i class="ki-duotone ki-sms fs-4 text-warning"><span class="path1"></span><span class="path2"></span></i></span>
                        <span class="menu-title">إرسال إيميل</span>
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a class="menu-link px-3" style="cursor:pointer;"
                        onclick="showGroupModal('{{ $id }}')">
                        <span class="menu-icon"><i class="ki-duotone ki-eye fs-4 text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                        <span class="menu-title">عرض التفاصيل</span>
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a class="menu-link px-3" style="cursor:pointer;"
                        onclick="openQrGenModal({{ $id }}, 'admin', {{ json_encode($group_name ?? '') }})">
                        <span class="menu-icon"><i class="bi bi-qr-code-scan fs-4 text-warning"></i></span>
                        <span class="menu-title">إنشاء QR للتشعيب</span>
                    </a>
                </div>

            @endcan
            @can('admin.groups.delete')
                <div class="separator my-2 opacity-75"></div>
                <div class="menu-item px-3">
                    <a class="menu-link px-3" href="javascript:;" data-href="{{ Crypt::encrypt($id) }}"
                        data-bs-toggle="modal" data-bs-target="#confirm">
                        <span class="menu-icon"><i class="ki-duotone ki-trash fs-4 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                        <span class="menu-title text-danger">حذف</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>
@elseif($x == 1)
    <div class="progress h-20px w-100px mx-auto" title="{{ $progress }}%">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;"
            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">{{ $progress }}%</div>
    </div>
@endif
