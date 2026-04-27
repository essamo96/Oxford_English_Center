<div class="mw-lg-650px mx-auto">
    <!--begin::Header-->
    <div class="d-flex flex-column mb-10 align-items-center">
        <div class="symbol symbol-120px symbol-circle mb-5 shadow-sm border border-4 border-white">
            <img src="{{ $teacher->image ? url($teacher->image) : asset('assets/media/avatars/blank.png') }}" alt="image" style="object-fit: cover;" />
        </div>
        <div class="text-center">
            <h1 class="text-gray-900 fw-bold fs-1 mb-1">{{ $teacher->name }}</h1>
            <span class="text-muted fw-semibold fs-5 d-block">{{ $teacher->email }}</span>
            <div class="d-flex flex-center mt-3">
                <span class="badge badge-light-success fw-bold px-4 py-3">مدرس معتمد</span>
            </div>
        </div>
    </div>
    <!--end::Header-->

    <!--begin::Stats-->
    <div class="row g-6 mb-10">
        <div class="col-6">
            <div class="card card-dashed flex-center min-w-175px my-3 p-6 bg-light-primary border-primary border-opacity-25">
                <span class="fs-4 fw-semibold text-primary pb-1 d-block">إجمالي المجموعات</span>
                <span class="fs-2hx fw-bold text-gray-900">{{ $groupsCount }}</span>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-dashed flex-center min-w-175px my-3 p-6 bg-light-info border-info border-opacity-25">
                <span class="fs-4 fw-semibold text-info pb-1 d-block">رقم الجوال</span>
                <span class="fs-2 fw-bold text-gray-900">{{ $teacher->mobile ?? 'N/A' }}</span>
            </div>
        </div>
    </div>
    <!--end::Stats-->

    <!--begin::Details-->
    <div class="row mb-10">
        <div class="col-12">
            <div class="bg-gray-100 rounded-3 p-8 border border-dashed border-gray-300">
                <div class="d-flex align-items-center mb-6">
                    <i class="ki-duotone ki-calendar-8 fs-1 text-primary me-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                    <div class="d-flex flex-column">
                        <span class="text-gray-500 fw-bold fs-7">تاريخ الانضمام</span>
                        <span class="text-gray-800 fw-bold fs-5">{{ \Carbon\Carbon::parse($teacher->join_date)->format('M d, Y') }}</span>
                    </div>
                </div>
                
                <div class="d-flex align-items-center">
                     <i class="ki-duotone ki-map fs-1 text-primary me-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                    <div class="d-flex flex-column">
                        <span class="text-gray-500 fw-bold fs-7">تاريخ الميلاد</span>
                        <span class="text-gray-800 fw-bold fs-5">{{ $teacher->dob ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Details-->

    <!--begin::Actions-->
    <div class="d-flex flex-stack pt-5">
        <div class="me-2">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إغلاق</button>
        </div>
        <div>
            <a href="{{ route('teachers.view') }}" class="btn btn-info me-3">عرض كافة المدرسين</a>
            <a href="{{ route('teachers.edit', ['id' => Crypt::encrypt($teacher->id)]) }}" class="btn btn-primary">تعديل البيانات</a>
        </div>
    </div>
    <!--end::Actions-->
</div>
