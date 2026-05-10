<!-- Student Profile Modal Content -->
<div class="d-flex flex-column flex-xl-row">
    <!-- Sidebar -->
    <div class="flex-column flex-lg-row-auto w-100 w-xl-300px mb-10">
        <div class="card card-flush shadow-none bg-transparent">
            <div class="card-body pt-5 text-center">
                <!-- Avatar -->
                <div class="symbol symbol-120px symbol-circle mb-7">
                    @php
                        $avatar = ($student->image && file_exists(public_path($student->image))) 
                                  ? asset($student->image) 
                                  : (($student->img && file_exists(public_path($student->img))) 
                                     ? asset($student->img) 
                                     : asset('assets/media/avatars/blank.png'));
                    @endphp
                    <img src="{{ $avatar }}" alt="image" style="object-fit: cover; border: 4px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                </div>
                
                <!-- Name & Email -->
                <a href="#" class="fs-3 text-gray-800 text-hover-primary fw-bold mb-1">{{ $student->name }}</a>
                <div class="fs-7 fw-semibold text-muted mb-6">{{ $student->email ?: 'بدون بريد إلكتروني' }}</div>

                <!-- Info Badges -->
                <div class="d-flex flex-center flex-wrap mb-5">
                    <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                        <div class="fs-6 fw-bold text-gray-700">{{ $student->status == 1 ? 'نشط' : 'غير نشط' }}</div>
                        <div class="fw-semibold text-muted fs-8">الحالة</div>
                    </div>
                    <div class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                        <div class="fs-6 fw-bold text-gray-700">{{ $student->gender == 'male' || $student->gender == '1' ? 'ذكر' : 'أنثى' }}</div>
                        <div class="fw-semibold text-muted fs-8">الجنس</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-lg-row-fluid ms-xl-10">
        <div class="card card-flush shadow-none bg-transparent">
            <div class="card-body pt-0">
                <h4 class="fw-bold mb-7 text-info">
                    <i class="ki-duotone ki-user fs-2 text-info me-2"><span class="path1"></span><span class="path2"></span></i> البيانات الشخصية
                </h4>
                <div class="row row-cols-1 row-cols-sm-2 mb-10">
                    <div class="col">
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2 text-muted">رقم الجوال</label>
                            <div class="fs-6 fw-bold text-gray-800">{{ $student->mobile }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2 text-muted">تاريخ الميلاد</label>
                            <div class="fs-6 fw-bold text-gray-800">{{ $student->dob ?: '---' }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2 text-muted">الوظيفة / التخصص</label>
                            <div class="fs-6 fw-bold text-gray-800">{{ $student->job ?: '---' }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="fv-row mb-7">
                            <label class="fs-6 fw-semibold mb-2 text-muted">تاريخ الانضمام</label>
                            <div class="fs-6 fw-bold text-gray-800">{{ $student->created_at->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>

                <h4 class="fw-bold mb-5 text-info">
                    <i class="ki-duotone ki-people fs-2 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> المجموعات المسجل بها
                </h4>
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-7 gy-3">
                        <thead>
                            <tr class="text-start text-muted fw-bold fs-8 text-uppercase gs-0">
                                <th class="min-w-100px">المجموعة</th>
                                <th class="min-w-100px">البرنامج</th>
                                <th class="min-w-80px">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @forelse($student->gropes as $sg)
                                <tr>
                                    <td>
                                        <span class="text-gray-800 fw-bold">{{ $sg->group->name ?? '---' }}</span>
                                    </td>
                                    <td>{{ $sg->group->program->title ?? '---' }}</td>
                                    <td>
                                        @if($sg->group && $sg->group->status == 1)
                                            <span class="badge badge-light-success fw-bold px-4 py-3">نشطة</span>
                                        @else
                                            <span class="badge badge-light-danger fw-bold px-4 py-3">منتهية</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-5">لا يوجد مجموعات حالية</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($student->note)
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mt-5">
                        <i class="ki-duotone ki-information fs-2tx text-warning me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">ملاحظات</h4>
                                <div class="fs-6 text-gray-700">{{ $student->note }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
