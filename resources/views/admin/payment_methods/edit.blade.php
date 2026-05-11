@extends('admin.layout.master')

@section('title', 'تعديل طريقة دفع')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted"><a href="{{ route('payment_methods.view') }}" class="text-muted text-hover-info">طرق الدفع</a></li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">تعديل طريقة دفع</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">تعديل: {{ $method->name }}</span>
        </div>
    </div>
    <div class="card-body py-4">
        <form action="{{ route('payment_methods.edit', $method->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-10">
                    <label class="form-label required">اسم الطريقة</label>
                    <input type="text" name="name" class="form-control" value="{{ $method->name }}" required>
                </div>
                <div class="col-md-6 mb-10">
                    <label class="form-label">أيقونة / شعار (اتركه فارغاً للإبقاء على الحالي)</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    @if($method->image)
                        <div class="mt-2">
                            <img src="{{ asset('uploads/' . $method->image) }}" width="50" class="rounded shadow-sm">
                        </div>
                    @endif
                </div>
                
                <div class="col-12 mb-10">
                    <h5 class="mb-5">بيانات التحويل (Credentials)</h5>
                    <div id="credentials-wrapper">
                        @if($method->credentials)
                            @foreach($method->credentials as $key => $value)
                                <div class="row mb-2 credential-row">
                                    <div class="col-md-5">
                                        <input type="text" name="credentials_keys[]" class="form-control" value="{{ $key }}">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" name="credentials_values[]" class="form-control" value="{{ $value }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-light-danger btn-icon w-100 remove-row"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-credential" class="btn btn-light-primary mt-2">
                        <i class="bi bi-plus"></i> إضافة حقل جديد
                    </button>
                </div>

                <div class="col-md-6 mb-10">
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $method->is_active ? 'checked' : '' }} />
                        <label class="form-check-label" for="is_active">تفعيل الطريقة</label>
                    </div>
                </div>
            </div>

            <div class="text-end mt-10">
                <button type="submit" class="btn btn-primary">تحديث البيانات</button>
                <a href="{{ route('payment_methods.view') }}" class="btn btn-light">إلغاء</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    $('#add-credential').on('click', function() {
        const row = `
            <div class="row mb-2 credential-row">
                <div class="col-md-5">
                    <input type="text" name="credentials_keys[]" class="form-control" placeholder="اسم الحقل">
                </div>
                <div class="col-md-5">
                    <input type="text" name="credentials_values[]" class="form-control" placeholder="القيمة">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-light-danger btn-icon w-100 remove-row"><i class="bi bi-trash"></i></button>
                </div>
            </div>
        `;
        $('#credentials-wrapper').append(row);
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('.credential-row').remove();
    });
</script>
@stop
