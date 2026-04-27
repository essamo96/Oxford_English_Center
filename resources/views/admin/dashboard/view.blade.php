@extends('admin.layout.master')
@section('title')
    لوحة التحكم - الإحصائيات المتقدمة
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الصفحة الرئيسية</a>
    </li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">الإحصائيات</li>
@stop

@section('page-content')
<!-- Principal Dashboard Row -->
<div class="row g-5 g-xl-10">
    <!--begin::Col: Statistics Hub -->
    <div class="col-xl-4 mb-xl-10">
        <!--begin::Lists Widget 19-->
        <div class="card card-flush h-xl-100 shadow-sm border-0">
            <!--begin::Heading-->
            <div class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-250px" style="background-image:url('{{ asset('assets/media/svg/shapes/top-green.png') }}')" data-bs-theme="light">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column text-white pt-15">
                    <span class="fw-bold fs-2x mb-3">نظرة عامة</span>
                    <div class="fs-4 text-white">
                        <span class="opacity-75">لديك حالياً</span>
                        <span class="position-relative d-inline-block">
                            <span class="link-white opacity-75-hover fw-bold d-block mb-1">{{ $active_groups_count }} مجموعات</span>
                            <span class="position-absolute opacity-50 bottom-0 start-0 border-2 border-body border-bottom w-100"></span>
                        </span>
                        <span class="opacity-75">نشطة بالكامل</span>
                    </div>
                </h3>
                <!--end::Title-->
            </div>
            <!--end::Heading-->
            <!--begin::Body-->
            <div class="card-body mt-n20">
                <!--begin::Stats-->
                <div class="mt-n20 position-relative">
                    <!--begin::Row-->
                    <div class="row g-3 g-lg-6">
                        <!--begin::Col-->
                        <div class="col-6">
                            <!--begin::Items-->
                            <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5">
                                <div class="symbol symbol-30px mb-8">
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-people fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </span>
                                </div>
                                <div class="m-0">
                                    <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1" data-kt-countup="true" data-kt-countup-value="{{ $total_students_count }}">{{ $total_students_count }}</span>
                                    <span class="text-gray-500 fw-semibold fs-6">إجمالي الطلاب</span>
                                </div>
                            </div>
                        </div>
                        <!--begin::Col-->
                        <div class="col-6">
                            <!--begin::Items-->
                            <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5">
                                <div class="symbol symbol-30px mb-8">
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-element-11 fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                    </span>
                                </div>
                                <div class="m-0">
                                    <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1" data-kt-countup="true" data-kt-countup-value="{{ $active_groups_count }}">{{ $active_groups_count }}</span>
                                    <span class="text-gray-500 fw-semibold fs-6">المجموعات</span>
                                </div>
                            </div>
                        </div>
                        <!--begin::Col-->
                        <div class="col-6">
                            <!--begin::Items-->
                            <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5">
                                <div class="symbol symbol-30px mb-8">
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-calendar-tick fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                                    </span>
                                </div>
                                <div class="m-0">
                                    <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1" data-kt-countup="true" data-kt-countup-value="{{ $groups_today_count }}">{{ $groups_today_count }}</span>
                                    <span class="text-gray-500 fw-semibold fs-6">مجموعات اليوم</span>
                                </div>
                            </div>
                        </div>
                        <!--begin::Col-->
                        <div class="col-6">
                            <!--begin::Items-->
                            <div class="bg-gray-100 bg-opacity-70 rounded-2 px-6 py-5">
                                <div class="symbol symbol-30px mb-8">
                                    <span class="symbol-label">
                                        <i class="ki-duotone ki-chart-line-star fs-1 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                    </span>
                                </div>
                                <div class="m-0">
                                    <span class="text-gray-700 fw-bolder d-block fs-2qx lh-1 ls-n1 mb-1">{{ $avg_students_per_group }}</span>
                                    <span class="text-gray-500 fw-semibold fs-6">متوسط الطلاب</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Body-->
        </div>
        <!--end::Lists Widget 19-->
    </div>
    <!--end::Col-->

    <!--begin::Col: Sliders & Engage -->
    <div class="col-xl-8 mb-5 mb-xl-10">
        <!--begin::Row-->
        <div class="row g-5 g-xl-10">
            <!--begin::Col-->
            <div class="col-xl-6 mb-xl-10">
                <!--begin::Slider Widget: Student Distribution -->
                <div id="kt_student_status_slider" class="card card-flush carousel carousel-custom carousel-stretch slide h-xl-100 shadow-sm" data-bs-ride="carousel" data-bs-interval="5000">
                    <!--begin::Header-->
                    <div class="card-header pt-5">
                        <h4 class="card-title d-flex align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">حالة الطلاب</span>
                            <span class="text-gray-400 mt-1 fw-bold fs-7">توزيع الطلاب حسب النشاط</span>
                        </h4>
                        <div class="card-toolbar">
                            <ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-primary">
                                <li data-bs-target="#kt_student_status_slider" data-bs-slide-to="0" class="active ms-1"></li>
                                <li data-bs-target="#kt_student_status_slider" data-bs-slide-to="1" class="ms-1"></li>
                                <li data-bs-target="#kt_student_status_slider" data-bs-slide-to="2" class="ms-1"></li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body py-6">
                        <div class="carousel-inner mt-n5">
                            <!--begin::Item: Active -->
                            <div class="carousel-item active show">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-70px me-5">
                                        <span class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-user-tick fs-3x text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </div>
                                    <div class="m-0">
                                        <h4 class="fw-bold text-gray-800 mb-3">طلاب فعالون</h4>
                                        <div class="d-flex d-grid gap-5">
                                            <div class="d-flex flex-column">
                                                <span class="fs-2hx fw-bolder text-gray-900" data-kt-countup="true" data-kt-countup-value="{{ $active_students_count }}">{{ $active_students_count }}</span>
                                                <span class="text-gray-400 fw-bold fs-7">منتظمون بالدراسة</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <a href="#" class="btn btn-sm btn-light-success me-2">عرض القائمة الكاملة</a>
                                </div>
                            </div>
                            <!--begin::Item: Delayed -->
                            <div class="carousel-item">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-70px me-5">
                                        <span class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-timer fs-3x text-info"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </div>
                                    <div class="m-0">
                                        <h4 class="fw-bold text-gray-800 mb-3">طلاب مؤجلين</h4>
                                        <div class="d-flex d-grid gap-5">
                                            <div class="d-flex flex-column">
                                                <span class="fs-2hx fw-bolder text-gray-900" data-kt-countup="true" data-kt-countup-value="{{ $delayed_students_count }}">{{ $delayed_students_count }}</span>
                                                <span class="text-gray-400 fw-bold fs-7">في قائمة الانتظار</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <a href="#" class="btn btn-sm btn-light-info me-2">إعادة تفعيل</a>
                                </div>
                            </div>
                            <!--begin::Item: Inactive -->
                            <div class="carousel-item">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-70px me-5">
                                        <span class="symbol-label bg-light-danger">
                                            <i class="ki-duotone ki-user-minus fs-3x text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        </span>
                                    </div>
                                    <div class="m-0">
                                        <h4 class="fw-bold text-gray-800 mb-3">غير فعالين</h4>
                                        <div class="d-flex d-grid gap-5">
                                            <div class="d-flex flex-column">
                                                <span class="fs-2hx fw-bolder text-gray-900" data-kt-countup="true" data-kt-countup-value="{{ $inactive_students_count }}">{{ $inactive_students_count }}</span>
                                                <span class="text-gray-400 fw-bold fs-7">حسابات متوقفة</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <a href="#" class="btn btn-sm btn-light-danger me-2">مراجعة الأسباب</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Col-->
            <div class="col-xl-6 mb-5 mb-xl-10">
                <!--begin::Slider Widget: Staff Insights -->
                <div id="kt_staff_slider" class="card card-flush carousel carousel-custom carousel-stretch slide h-xl-100 shadow-sm" data-bs-ride="carousel" data-bs-interval="5500">
                    <!--begin::Header-->
                    <div class="card-header pt-5">
                        <h4 class="card-title d-flex align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-800">رؤى الكادر التعليمي</span>
                            <span class="text-gray-400 mt-1 fw-bold fs-7">نشاط المدرسين الحالي</span>
                        </h4>
                        <div class="card-toolbar">
                            <ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-success">
                                <li data-bs-target="#kt_staff_slider" data-bs-slide-to="0" class="active ms-1"></li>
                                <li data-bs-target="#kt_staff_slider" data-bs-slide-to="1" class="ms-1"></li>
                            </ol>
                        </div>
                    </div>
                    <!--end::Header-->
                    <!--begin::Body-->
                    <div class="card-body py-6">
                        <div class="carousel-inner mt-n5">
                            <!--begin::Item: Top Teacher -->
                            <div class="carousel-item active show">
                                <div class="d-flex align-items-center mb-9">
                                    <div class="symbol symbol-70px symbol-circle me-5">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-profile-user fs-3x text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                        </span>
                                    </div>
                                    <div class="m-0">
                                        <h4 class="fw-bold text-gray-800 mb-3">{{ $most_active_teacher }}</h4>
                                        <div class="d-flex d-grid gap-5">
                                            <div class="d-flex flex-column">
                                                <span class="fs-3 fw-bold text-primary">{{ $most_active_teacher_count }} مجموعة نشطة</span>
                                                <span class="text-gray-400 fw-bold fs-7 text-uppercase">المعلم الأكثر نشاطاً</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <a href="#" class="btn btn-sm btn-primary">عرض الملف الشخصي</a>
                                </div>
                            </div>
                            <!--begin::Item: Staff Stats -->
                            <div class="carousel-item">
                                <div class="d-flex align-items-center mb-9">
                                    <div class="symbol symbol-70px symbol-circle me-5">
                                        <span class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-teacher fs-3x text-warning"><span class="path1"></span><span class="path2"></span></i>
                                        </span>
                                    </div>
                                    <div class="m-0">
                                        <h4 class="fw-bold text-gray-800 mb-3">إجمالي الكادر</h4>
                                        <div class="d-flex d-grid gap-5">
                                            <div class="d-flex flex-column">
                                                <span class="fs-2hx fw-bolder text-gray-900" data-kt-countup="true" data-kt-countup-value="{{ $total_teachers_count }}">{{ $total_teachers_count }}</span>
                                                <span class="text-gray-400 fw-bold fs-7">مدرّس معتمد</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-0">
                                    <a href="#" class="btn btn-sm btn-warning">إدارة المدرسين</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
        
        <!--begin::Engage widget 4-->
        <div class="card border-transparent shadow-sm mb-5 mb-xl-10" data-bs-theme="light" style="background-color: #1C325E;">
            <div class="card-body d-flex ps-xl-15">
                <div class="m-0">
                    <div class="position-relative fs-2x z-index-2 fw-bold text-white mb-7">
                        أهلاً بك في أكاديمية أوكسفورد
                        <br />
                        <span class="fs-4 fw-semibold opacity-75">إدارة تعليمية متكاملة بأحدث المعايير</span>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('calendar.view') }}" class="btn btn-danger fw-semibold me-2">عرض الجدول اليومي</a>
                        <a href="{{ route('dashboard.view.membership') }}" class="btn btn-color-white bg-white bg-opacity-15 bg-hover-opacity-25 fw-semibold">طلبات العضوية</a>
                    </div>
                </div>
                <img src="{{ asset('assets/media/illustrations/sigma-1/17-dark.png') }}" class="position-absolute me-3 bottom-0 end-0 h-200px" alt="illustration" />
            </div>
        </div>
        <!--end::Engage widget 4-->

    </div>
    <!--end::Col-->
</div>

<!--begin::Groups Overview Row-->
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush shadow-sm border-0">
            <div class="card-header pt-7">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-800">المجموعات النشطة</span>
                    <span class="text-gray-400 mt-1 fw-bold fs-7">نظرة سريعة على المجموعات المسجلة حالياً</span>
                </h3>
            </div>
            <div class="card-body">
                <ul class="nav nav-pills d-flex flex-nowrap overflow-auto nav-pills-custom gap-3 mb-6" role="tablist">
                    @foreach($all_active_groups as $index => $group)
                    <li class="nav-item mb-3 me-0" role="presentation">
                        <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg" data-bs-toggle="pill" href="#group_tab_{{ $group->id }}" style="width: 140px;height: 180px" role="tab">
                            <div class="nav-icon mb-3">
                                @if($group->image)
                                    <img src="{{ url($group->image) }}" class="w-60px h-60px rounded shadow-sm border" alt="{{ $group->name }}">
                                @else
                                    <img src="{{ asset('assets/media/svg/avatars/001-boy.svg') }}" class="w-60px h-60px" alt="">
                                @endif
                            </div>
                            <div class="text-center">
                                <span class="text-gray-800 fw-bold fs-6 d-block">{{ Str::limit($group->name, 10) }}</span>
                                <span class="text-gray-400 fw-semibold fs-8">{{ $group->ctime ? $group->ctime->times : 'N/A' }}</span>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
<!--end::Groups Overview Row-->


<div class="row g-5 g-xl-10">
    <div class="col-xl-12">
        <!--begin::Weekly Calendar Widget-->
        <div class="card h-xl-100 shadow-sm border-0">
            <div class="card-header position-relative py-0 border-bottom-2">
                <ul class="nav nav-stretch nav-pills nav-pills-custom d-flex mt-3" role="tablist">
                    @php 
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $today = now()->format('l');
                    @endphp
                    @foreach($days as $day)
                    <li class="nav-item p-0 ms-0 me-8" role="presentation">
                        <a class="nav-link btn btn-color-muted px-0 {{ $day == $today ? 'active' : '' }}" data-bs-toggle="tab" href="#schedule_tab_{{ $day }}" role="tab">
                            <span class="nav-text fw-semibold fs-4 mb-3">{{ $day }}</span>
                            <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <div class="card-toolbar">
                    <div class="btn btn-sm btn-light d-flex align-items-center px-4">
                        <div class="text-gray-600 fw-bold">{{ now()->format('d M Y') }}</div>
                        <i class="ki-duotone ki-calendar-8 fs-1 ms-2 me-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content mb-2">
                    @foreach($days as $day)
                    <div class="tab-pane fade {{ $day == $today ? 'show active' : '' }}" id="schedule_tab_{{ $day }}" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th class="min-w-150px p-0"></th>
                                        <th class="min-w-200px p-0"></th>
                                        <th class="min-w-100px p-0"></th>
                                        <th class="min-w-80px p-0"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($weekly_schedule[$day]) && $weekly_schedule[$day]->count() > 0)
                                        @foreach($weekly_schedule[$day] as $schedule)
                                        <tr>
                                            <td class="fs-6 fw-bold text-gray-800">{{ $schedule->ctime ? $schedule->ctime->times : 'N/A' }}</td>
                                            <td class="fs-6 fw-bold text-gray-400">
                                                المجموعة: <span class="text-gray-800">{{ $schedule->name }}</span>
                                                <br>
                                                <small class="text-muted">{{ $schedule->program ? $schedule->program->name : '' }}</small>
                                            </td>
                                            <td class="fs-6 fw-bold text-gray-400">
                                                المدرس: <span class="text-gray-800">{{ $schedule->teacher ? $schedule->teacher->name : 'N/A' }}</span>
                                            </td>
                                            <td class="pe-0 text-end">
                                                <a href="{{ route('groups.edit', ['id' => Crypt::encrypt($schedule->id)]) }}" class="btn btn-sm btn-light">عرض</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center py-10">
                                                <div class="text-gray-400 fs-4 fw-bold">لا توجد مجموعات مجدولة لهذا اليوم</div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!--end::Weekly Calendar Widget-->
    </div>
</div>
@stop

@section('css')

<style>
    .transition-3d { transition: all 0.3s ease; }
    .transition-3d:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>
@stop
