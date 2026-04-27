@extends('admin.layout.master')

@section('title', 'تعديل شريك')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('partners.view') }}" class="text-muted text-hover-info">إدارة الشركاء</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">{{ $info->title }}</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1 text-info">
                    <i class="ki-duotone ki-notepad-edit fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> تعديل شريك: {{ $info->title }}
                </span>
            </div>
        </div>
        <div class="card-body py-10">
            @include('admin.layout.error')
            <form role="form" method="post" action="" class="form" enctype="multipart/form-data">
                @csrf
                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">العنوان</label>
                        <input type="text" value="{{ $info->title }}" name="title" id="title" class="form-control form-control-solid" placeholder="العنوان">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الوصف</label>
                        <input type="text" value="{{ $info->descs }}" name="descs" id="descs" class="form-control form-control-solid" placeholder="وصف المختصر">
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الرابط</label>
                        <input type="text" value="{{ $info->url }}" name="url" id="url" class="form-control form-control-solid" placeholder="https://...">
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">القسم</label>
                        <select name="user_id" id="user_id" class="form-select form-select-solid" data-control="select2">
                            <option value="1" {{ $info->user_id == 1 ? 'selected' : '' }}>Family</option>
                            <option value="2" {{ $info->user_id == 2 ? 'selected' : '' }}>Partners</option>
                            <option value="3" {{ $info->user_id == 3 ? 'selected' : '' }}>Testimonial</option>
                        </select>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-12 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الشعار</label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <input id="thumbnail" value="{{ $info->image }}" class="form-control form-control-solid w-400px" type="text" name="image" readonly>
                            </div>
                            <button type="button" onclick="openMetronicFileManager('image', 'thumbnail')" class="btn btn-primary">
                                <i class="ki-duotone ki-picture fs-2 me-1"><span class="path1"></span><span class="path2"></span></i> حدد صورة
                            </button>
                        </div>
                        <div id="holder" class="mt-5">
                            @if($info->image)
                                <img src="{{ url($info->image) }}" style="max-height: 100px;" class="rounded border shadow-sm">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-8">
                    <div class="col-md-6 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">الحالة</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input h-30px w-50px" name="status" type="checkbox" value="1" {{ $info->status == 1 ? 'checked' : '' }}>
                            <label class="form-check-label ms-3 text-gray-700 fw-bold">تفعيل / تعطيل</label>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-15">
                    <button type="submit" class="btn btn-primary px-15">حفظ</button>
                    <a href="{{ route('partners.view') }}" class="btn btn-light ms-3">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
@stop

