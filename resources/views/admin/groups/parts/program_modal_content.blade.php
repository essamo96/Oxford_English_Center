<div class="mw-lg-600px mx-auto">
    <div class="mb-13 text-center">
        <h1 class="mb-3">تفاصيل البرنامج التعليمي</h1>
        <div class="text-muted fw-semibold fs-5">بيانات البرنامج: <span class="text-primary fw-bold">{{ $program->title }}</span></div>
    </div>

    <div class="card bg-light-primary border-0 mb-10 overflow-hidden">
        <div class="card-body p-8">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-70px me-6 shadow-sm">
                    <img src="{{ $program->image ? url($program->image) : asset('assets/media/svg/files/blank-image.svg') }}" class="rounded" alt="" />
                </div>
                <div class="d-flex flex-column">
                    <span class="fs-3 fw-bold text-gray-900 mb-1">{{ $program->title }}</span>
                    <span class="fs-6 text-muted fw-semibold">{{ $program->descs ? mb_substr(strip_tags($program->descs), 0, 100) . '...' : 'لا يوجد وصف متاح' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-9 mb-8">
        <div class="col-md-12">
            <div class="bg-gray-100 p-6 rounded border border-dashed border-gray-400">
                <h4 class="fs-5 fw-semibold text-gray-800 mb-4">إحصائيات وقيم</h4>
                <div class="row">
                    <div class="col-6 mb-5">
                        <span class="text-muted d-block fs-7 fw-bold">إجمالي المجموعات</span>
                        <span class="fs-3 fw-bold text-primary">{{ $groupsCount }} مجموعة</span>
                    </div>
                    <div class="col-6 mb-5">
                        <span class="text-muted d-block fs-7 fw-bold">الحالة</span>
                        <span class="badge badge-light-success fw-bold">نشط</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-10">
        <h4 class="fs-5 fw-semibold text-gray-800 mb-4">وصف البرنامج</h4>
        <div class="text-gray-600 fs-6 lh-lg">
            {!! $program->descs !!}
        </div>
    </div>

    <div class="d-flex gap-3 justify-content-center mt-10">
        <a href="{{ route('programs.view') }}" class="btn btn-primary w-100 shadow-sm">
            <i class="bi bi-collection-fill me-2"></i> عرض كافة البرامج
        </a>
        <a href="{{ route('programs.edit', ['id' => Crypt::encrypt($program->id)]) }}" class="btn btn-light-info w-100 shadow-sm">
            <i class="bi bi-pencil-square me-2"></i> تعديل البرنامج
        </a>
    </div>
</div>
