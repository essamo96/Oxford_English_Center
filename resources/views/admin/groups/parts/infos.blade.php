<div class="d-flex flex-column flex-xl-row">
    <!-- Sidebar -->
    <div class="flex-column flex-lg-row-auto w-100 w-xl-350px mb-10">
        <div class="card mb-5 mb-xl-8">
            <div class="card-body pt-15">
                <div class="d-flex flex-center flex-column mb-5">
                    <div class="symbol symbol-100px symbol-circle mb-7">
                        <img src="{{ $infos->image ? asset($infos->image) : asset('assets/admin/media/avatars/300-1.jpg') }}" alt="image" />
                    </div>
                    <a href="#" class="fs-3 text-gray-800 text-hover-info fw-bold mb-1">{{ $infos->name }}</a>
                    <div class="fs-5 fw-semibold text-muted mb-6">{{ $infos->program->title }}</div>
                </div>

                <div class="d-flex flex-stack fs-4 py-3">
                    <div class="fw-bold rotate collapsible" data-bs-toggle="collapse" href="#kt_customer_view_details" role="button" aria-expanded="false" aria-controls="kt_customer_view_details">
                        تفاصيل المجموعة
                        <span class="ms-2 rotate-180">
                            <i class="ki-duotone ki-down fs-3"></i>
                        </span>
                    </div>
                </div>
                <div class="separator separator-dashed my-3"></div>
                <div id="kt_customer_view_details" class="collapse show">
                    <div class="py-5 fs-6">
                        <div class="fw-bold mt-5 text-primary">المدرس</div>
                        <div class="text-gray-600">{{ $infos->teacher->name }}</div>

                        <div class="fw-bold mt-5 text-primary">الأيام</div>
                        <div class="text-gray-600">{{ $infos->ctime->days }}</div>

                        <div class="fw-bold mt-5 text-primary">الوقت</div>
                        <div class="text-gray-600">{{ $infos->ctime->times }}</div>

                        <div class="fw-bold mt-5 text-primary">تاريخ الإنشاء</div>
                        <div class="text-gray-600">{{ $infos->created_at->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-lg-row-fluid ms-lg-15">
        <div class="row g-6 g-xl-9 mb-6 mb-xl-9">
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-dashed border-primary">
                    <div class="card-body d-flex flex-center flex-column py-5">
                        <span class="fs-2hx fw-bold text-primary">{{ $count_student->count() }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">إجمالي الطلاب</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-dashed border-danger">
                    <div class="card-body d-flex flex-center flex-column py-5">
                        <span class="fs-2hx fw-bold text-danger">{{ $countUnActiveStudent }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">غير فعالين</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-dashed border-warning">
                    <div class="card-body d-flex flex-center flex-column py-5">
                        <span class="fs-2hx fw-bold text-warning">{{ $countdelayStudent }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">متأخرين</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-dashed border-info">
                    <div class="card-body d-flex flex-center flex-column py-5">
                        <span class="fs-2x fw-bold text-info">M:{{ $countmailStudent }} | F:{{ $countfemalStudent }}</span>
                        <span class="fs-6 fw-semibold text-gray-400">الجنس</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-none">
            <div class="card-body p-0">
                <div class="mb-10">
                    <h3 class="fw-bold text-gray-800 mb-5">مستوى التقدم</h3>
                    <div class="d-flex flex-stack mb-2">
                        <span class="text-muted me-2 fs-7 fw-bold">
                            @if ($infos->progress == 30 || $infos->progress == null)
                                Units 1 to 3
                            @elseif ($infos->progress == 60)
                                Units 4 to 6
                            @elseif ($infos->progress == 90)
                                Units 7 to 9
                            @else
                                Units 10
                            @endif
                        </span>
                        <span class="text-gray-800 fs-6 fw-bold">{{ $infos->progress ?: 25 }}%</span>
                    </div>
                    <div class="progress h-10px w-100 bg-light-primary">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $infos->progress ?: 25 }}%" aria-valuenow="{{ $infos->progress ?: 25 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
