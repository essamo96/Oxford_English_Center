@extends('admin.layout.master')
@section('title')
    {{ $current_route->name_ar }}
@stop
@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ url('/') }}" class="text-muted text-hover-primary">الرئيسية</a>
    </li>
    <li class="breadcrumb-item text-muted">- {{ $current_route->name_ar }}</li>
@stop
@section('page-content')
    <div class="card">
        <div class="card-body py-4">
            @include('admin.layout.error')
            <form action="" method="POST">
                <div class="row justify-content-center">
                    <div class="col-9">
                        <div class="form-floating mb-9 row ">
                            <div class="col">
                                <label class="p-2"> الاسم <span>*</span></label>
                                <input type="text" value="{{ $info ? $info->name : old('name') }}" name="name"
                                    class="form-control" />
                            </div>
                            <div class="col">
                                <label class="p-2"> العنوان <span>*</span></label>
                                <input type="text" value="{{ $info ? $info->address : old('address') }}" name="address"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="mobile">الموبايل</label>
                                <textarea name="mobile" id="mobile" class="form-control form-control-solid"></textarea>
                            </div>
                            <div class="fv-row mb-10 col">
                                <label for="contact_email" class="required fw-semibold fs-6 mb-2">البريد</label>
                                <textarea name="contact_email" id="contact_email" class="form-control form-control-solid"></textarea>
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="description">الوصف</label>
                                <textarea name="description" id="description" class="form-control form-control-solid"></textarea>
                            </div>
                            <div class="fv-row mb-10 col">
                                <label for="more_desc" class="required fw-semibold fs-6 mb-2">وصف اضافي</label>
                                <textarea name="more_desc" class="form-control form-control-solid"></textarea>
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="bootstrap-tagsinput"><span class="tag label label-info">IELTS<span
                                data-role="remove"></span></span> <span class="tag label label-info">8.5<span
                                data-role="remove"></span></span><input type="text" placeholder="">
                            </div>
                        </div>
                        <div class="form-floating mb row">
                        </div>
                    </div>
                </div>
                <div class="text-center pt-2">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">حقظ </button>
                    <a type="reset" href="{{ route($active_menu . '.view') }}" class="btn btn-light me-3">الغاء
                        الامر</a>
                </div>
            </form>
        </div>
    </div>
@stop
