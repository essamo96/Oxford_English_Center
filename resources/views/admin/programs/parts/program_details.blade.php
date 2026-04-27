<div class="row g-6 g-xl-9">
    @foreach($groups as $group)
        @php
            $teacher = $group->teacher;
            // Count students for this specific group
            $studentCount = \App\Models\GroupStudents::where('group_id', $group->id)
                ->whereHas('student', function($q) {
                    $q->where('status', 1)->where('delaying', 0);
                })->count();
            
            $bgImage = !empty($group->image) ? asset($group->image) : (!empty($program->image) ? asset($program->image) : asset('assets/media/stock/600x600/img-65.jpg'));
            $teacherImage = !empty($teacher->image) ? asset($teacher->image) : asset('assets/media/avatars/300-3.jpg');
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card card-flush h-xl-100 shadow-sm border border-2 border-gray-200">
                <!--begin::Body-->
                <div class="card-body py-9">
                    <!--begin::Row-->
                    <div class="row gx-9 h-100">
                        <!--begin::Col-->
                        <div class="col-sm-12 mb-5">
                            <!--begin::Image-->
                            <div class="bgi-no-repeat bgi-position-center bgi-size-cover card-rounded min-h-200px h-100" style="background-size: 100% 100%;background-image:url('{{ $bgImage }}')"></div>
                            <!--end::Image-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-sm-12">
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-column h-100">
                                <!--begin::Header-->
                                <div class="mb-5">
                                    <!--begin::Headin-->
                                    <div class="d-flex flex-stack mb-4">
                                        <!--begin::Title-->
                                        <div class="flex-shrink-0 me-5">
                                            <span class="text-gray-400 fs-7 fw-bold me-2 d-block lh-1 pb-1">إسم البرنامج</span>
                                            <span class="text-gray-800 fs-2 fw-bold text-primary">{{ $program->title }}</span>
                                        </div>
                                        <!--end::Title-->
                                        <span class="badge {{ $group->status == 1 ? 'badge-light-success' : 'badge-light-danger' }} flex-shrink-0 align-self-center py-3 px-4 fs-7">
                                            {{ $group->status == 1 ? 'مفعل' : 'غير مفعل' }}
                                        </span>
                                    </div>
                                    <div class="mb-4">
                                        <span class="text-gray-600 fs-4 fw-bold">{{ $group->name }}</span>
                                    </div>
                                    <!--end::Heading-->
                                    <!--begin::Items-->
                                    <div class="d-flex align-items-center flex-wrap d-grid gap-2">
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center me-5">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-35px symbol-circle me-3">
                                                <img src="{{ $teacherImage }}" alt="{{ $teacher->name ?? 'N/A' }}">
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Info-->
                                            <div class="m-0">
                                                <span class="fw-semibold text-gray-400 d-block fs-8">المدرس</span>
                                                <span class="fw-bold text-gray-800 text-hover-info fs-7">{{ $teacher->name ?? 'غير محدد' }}</span>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Item-->
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center">
                                            <!--begin::Symbol-->
                                            <div class="symbol symbol-35px symbol-circle me-3">
                                                <span class="symbol-label bg-light-info">
                                                    <i class="ki-duotone ki-people fs-4 text-info">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                </span>
                                            </div>
                                            <!--end::Symbol-->
                                            <!--begin::Info-->
                                            <div class="m-0">
                                                <span class="fw-semibold text-gray-400 d-block fs-8">الطلاب</span>
                                                <span class="fw-bold text-gray-800 fs-7">{{ $studentCount }} طالب</span>
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Header-->
                                <!--begin::Body-->
                                <div class="mb-6">
                                    <div class="d-flex mb-4">
                                        <div class="border border-gray-300 border-dashed rounded min-w-100px w-100 py-2 px-4 me-4">
                                            <span class="fs-7 text-gray-700 fw-bold">{{ $group->start_date ?? 'N/A' }}</span>
                                            <div class="fw-semibold text-gray-400 fs-8">تاريخ البدء</div>
                                        </div>
                                        <div class="border border-gray-300 border-dashed rounded min-w-100px w-100 py-2 px-4">
                                            <span class="fs-7 text-gray-700 fw-bold">{{ $group->ctime->days ?? '' }} {{ $group->ctime->times ?? 'N/A' }}</span>
                                            <div class="fw-semibold text-gray-400 fs-8">المواعيد</div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Body-->
                                <!--begin::Footer-->
                                <div class="d-flex flex-stack mt-auto bd-highlight">
                                    <a href="{{ url("admin/groups/teacher/students/" . Crypt::encrypt($group->id)) }}" class="btn btn-sm btn-primary">
                                        عرض الطلاب
                                        <i class="ki-duotone ki-arrow-right fs-4 ms-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                    <a href="{{ route('program.groups.view', Crypt::encrypt($program->id)) }}" class="text-primary opacity-75-hover fs-7 fw-semibold">
                                        إدارة المجموعة
                                    </a>
                                </div>
                                <!--end::Footer-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Body-->
            </div>
        </div>
    @endforeach
</div>
