@extends('admin.layout.master')

@section('title', 'إضافة أسئلة التقييم')

@section('page-title')
    إضافة أسئلة التقييم
@stop

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('questions.view') }}" class="text-muted text-hover-info">إدارة التقييمات</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">إضافة أسئلة تقييم المعلمين</li>
@stop

@section('page-content')
    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">إضافة أسئلة تقييم المعلمين</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{ route('questions.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
                </a>
            </div>
        </div>
        <div class="card-body py-4">
            @include('admin.layout.error')
            <form role="form" method="post" action="" class="form d-flex flex-column gap-7" enctype="multipart/form-data">
                {{ csrf_field() }}
                
                <div class="students">
                    <div class="student mb-5 border border-dashed border-gray-300 p-5 rounded bg-light">
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-grow-1">
                                <label class="fs-6 fw-semibold mb-2 required">ادخل نص السؤال</label>
                                <input type="text" class="form-control form-control-solid name" name="name[]" autocomplete="off" placeholder="نص السؤال...">
                            </div>
                            <div>
                                <label class="fs-6 fw-semibold mb-2 d-block">&nbsp;</label>
                                <a href="javascript:;" class="btn btn-icon btn-light-danger removestudent" data-bs-toggle="tooltip" title="حذف الحقل">
                                    <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                                </a>
                            </div>
                            <div>
                                <label class="fs-6 fw-semibold mb-2 d-block">&nbsp;</label>
                                <a href="javascript:;" class="btn btn-icon btn-light-primary addstudent" data-bs-toggle="tooltip" title="إضافة حقل جديد">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('questions.view') }}" class="btn btn-light btn-active-light-primary me-2">إلغاء</a>
                    <button type="submit" class="btn btn-primary">حفظ الأسئلة</button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Add new input field
    $(document).on('click', '.addstudent', function (event) {
        event.preventDefault();
        var container = $('.students');
        var template = $(this).closest('.student').clone();
        
        // Clear input value
        template.find('input').val('');
        
        // Destroy tooltips in clone to prevent issues, and re-init
        template.find('[data-bs-toggle="tooltip"]').tooltip('dispose');

        // Append to container
        container.append(template);
        
        // Re-init tooltips
        KTApp.initBootstrapTooltips();
    });

    // Remove input field
    $(document).on('click', '.removestudent', function (event) {
        event.preventDefault();
        var itemsCount = $('.student').length;
        
        if (itemsCount > 1) {
            var item = $(this).closest('.student');
            item.fadeOut(300, function () {
                $(this).remove();
            });
        } else {
            toastr.warning('يجب أن يكون هناك حقل سؤال واحد على الأقل');
        }
    });
});
</script>
@stop
