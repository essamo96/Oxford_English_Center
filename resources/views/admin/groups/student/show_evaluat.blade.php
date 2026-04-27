@extends('admin.layout.master')

@section('title')
    عرض تقييم الطالب
@stop

@section('page-title')
    عرض تقييم الطالب
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">إدارة المجموعات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">عرض التقييم</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">تفاصيل تقييم الطالب</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body">
            @include('admin.layout.masterLayouts.error')
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 text-center border">
                    <thead>
                        <tr class="fw-bold fs-6 text-gray-800 bg-light">
                            <th class="w-50px"> # </th>
                            <th class="text-start ps-4 min-w-200px"> السؤال </th>
                            <th> مقبول (1) </th>
                            <th> جيد (2) </th>
                            <th> جيد جداً (3) </th>
                            <th> ممتاز (4) </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-start ps-4 fw-semibold text-gray-700">
                                    {{ $question->questions->name_en }}
                                </td>
                                <td>
                                    <div class="form-check form-check-custom form-check-solid justify-content-center">
                                        <input class="form-check-input" type="radio" value="1" disabled
                                            {{ $question->answer == 1 ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-check-custom form-check-solid justify-content-center">
                                        <input class="form-check-input" type="radio" value="2" disabled
                                            {{ $question->answer == 2 ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-check-custom form-check-solid justify-content-center">
                                        <input class="form-check-input" type="radio" value="3" disabled
                                            {{ $question->answer == 3 ? 'checked' : '' }} />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-check-custom form-check-solid justify-content-center">
                                        <input class="form-check-input" type="radio" value="4" disabled
                                            {{ $question->answer == 4 ? 'checked' : '' }} />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-10 row g-9">
                <div class="col-md-4">
                    <label class="fs-6 fw-semibold mb-2">المجموع</label>
                    <input type="text" class="form-control form-control-solid" value="{{ $info->total }}" readonly />
                </div>
                <div class="col-md-12">
                    <label class="fs-6 fw-semibold mb-2">الملاحظات</label>
                    <textarea class="form-control form-control-solid" rows="4" readonly>{{ $info->notes }}</textarea>
                </div>
            </div>
        </div>
    </div>
@stop
