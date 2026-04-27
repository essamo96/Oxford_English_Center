<div class="mw-lg-600px mx-auto">
    <div class="mb-13 text-center">
        <h1 class="mb-3">تفاصيل المجموعة</h1>
        <div class="text-muted fw-semibold fs-5">بيانات المجموعة: <span class="text-primary fw-bold">{{ $group->name }}</span></div>
    </div>

    <div class="row g-9 mb-8">
        <div class="col-md-6">
            <div class="d-flex flex-stack">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-primary text-primary fw-bold">
                            <i class="ki-duotone ki-book-open fs-2 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold fs-6">البرنامج</span>
                        <span class="text-muted fw-semibold fs-7">{{ $group->program->title ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-stack">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-info text-info fw-bold">
                            <i class="ki-duotone ki-user fs-2 text-info"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold fs-6">المدرس</span>
                        <span class="text-muted fw-semibold fs-7">{{ $group->teacher->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-9 mb-8">
        <div class="col-md-6">
            <div class="d-flex flex-stack">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-warning text-warning fw-bold">
                            <i class="ki-duotone ki-calendar-8 fs-2 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold fs-6">المواعيد</span>
                        <span class="text-muted fw-semibold fs-7">{{ $group->ctime->days ?? 'N/A' }} - {{ $group->ctime->times ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-stack">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-40px symbol-circle me-4">
                        <span class="symbol-label bg-light-success text-success fw-bold">
                            <i class="ki-duotone ki-people fs-2 text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fw-bold fs-6">عدد الطلاب</span>
                        <span class="text-muted fw-semibold fs-7">{{ $studentsCount }} طالب مضاف</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="separator separator-dashed my-8"></div>

    <div class="mb-5">
        <h4 class="fs-5 fw-semibold text-gray-800 mb-3">تفاصيل إضافية</h4>
        <div class="bg-light p-4 rounded border border-dashed border-gray-300">
            <div class="mb-3 d-flex align-items-center">
                <span class="bullet bullet-vertical h-20px bg-primary me-3"></span>
                <span class="text-gray-700 fw-bold me-2">رابط Zoom:</span>
                @if($group->zoom)
                    <a href="{{ $group->zoom }}" target="_blank" class="text-primary text-hover-underline">{{ $group->zoom }}</a>
                @else
                    <span class="text-muted italic">غير متوفر</span>
                @endif
            </div>
            <div class="mb-0 d-flex align-items-center">
                <span class="bullet bullet-vertical h-20px bg-success me-3"></span>
                <span class="text-gray-700 fw-bold me-2">تاريخ البدء/الانتهاء:</span>
                <span class="text-gray-600">{{ $group->start_date }} إلى {{ $group->end_date }}</span>
            </div>
        </div>
    </div>

    <div class="d-flex gap-3 justify-content-center mt-10">
        <a href="{{ route('groups.student.view', ['id' => Crypt::encrypt($group->id)]) }}" class="btn btn-primary w-100 shadow-sm">
            <i class="bi bi-people-fill me-2"></i> إدارة الطلاب
        </a>
        <a href="{{ route('groups.edit', ['id' => Crypt::encrypt($group->id)]) }}" class="btn btn-light-info w-100 shadow-sm">
            <i class="bi bi-pencil-square me-2"></i> تعديل البيانات
        </a>
    </div>
</div>
