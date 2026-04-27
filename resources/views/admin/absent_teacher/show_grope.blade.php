@extends('admin.layout.master')

@section('title', 'تفاصيل حضور وغياب مجموعات المعلم')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('absent_teacher.view') }}" class="text-muted text-hover-info">سجل حضور المدرسين</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">تفاصيل المجموعات</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-calendar-tick fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i> تفاصيل حضور وغياب مجموعات المعلم
            </span>
        </div>
        <div class="card-toolbar gap-2">
            <a href="{{ route('absent_teacher.view') }}" class="btn btn-light-info btn-sm fw-bold">
                <i class="ki-duotone ki-black-right me-1 fs-5"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body py-4">
        @include('admin.layout.error')
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-10px">#</th>
                        <th class="min-w-150px">اسم المجموعة</th>
                        <th class="min-w-100px">عدد الأيام</th>
                        <th class="min-w-200px">تفاصيل الأيام</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach ($info as $index => $group)
                    <tr>
                        <td>{{ is_numeric($index) ? $index + 1 : '' }}</td>
                        <td class="fw-bold text-gray-800">{{ $group->group_name }}</td>
                        <td>
                            <span class="badge badge-light-primary fs-7 fw-bold">{{ $group->days_count }}</span>
                        </td>
                        <td class="text-start">
                            @php
                                $dayes = App\Models\Absent_Teacher::where('group_id', $group->group_id)
                                        ->where('teacher_id', $group->teacher_id)
                                        ->get();
                            @endphp

                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($dayes as $day)
                                    <span class="badge badge-light-info fw-bold py-2 px-3 shadow-sm">
                                        <i class="ki-duotone ki-calendar fs-8 me-1"><span class="path1"></span><span class="path2"></span></i> {{ $day->days }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

