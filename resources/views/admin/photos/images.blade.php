@extends('admin.layout.master')

@section('title')
إدارة الصور
@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('photos.view') }}" class="text-muted text-hover-info">الصور</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">إدارة صور الألبوم</li>
@stop

@section('page-content')
<div class="card card-flush shadow-sm">
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">معرض الصور</span>
            <span class="text-muted fw-semibold fs-7">إدارة ورفع الصور للألبوم</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ route('photos.view') }}" class="btn btn-sm btn-light btn-active-light-primary">
                <i class="ki-duotone ki-arrow-left fs-2"><span class="path1"></span><span class="path2"></span></i> رجوع
            </a>
        </div>
    </div>
    
    <div class="card-body">
        @include('admin.layout.masterLayouts.error')
        <!-- Upload Area -->
        <div class="fv-row mb-10">
            <div class="dropzone d-flex flex-column flex-center border-dashed border-primary hover-elevate-up" id="drop-area" style="cursor: pointer; min-height: 150px; background-color: var(--bs-primary-light);">
                <i class="ki-duotone ki-file-up fs-3x text-primary mb-3"><span class="path1"></span><span class="path2"></span></i>
                <div class="ms-4 text-center">
                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">اسحب الصور هنا أو انقر للرفع</h3>
                    <span class="fs-7 fw-semibold text-gray-500">يمكنك رفع عدة صور في وقت واحد</span>
                </div>
            </div>
        </div>

        <div class="separator separator-dashed my-10"></div>

        <!-- Images Grid -->
        <div class="row g-6 g-xl-9 temp">
            @foreach($images as $img)
            <div class="col-md-3 col-lg-2 col-6 h-100">
                <div class="card card-flush h-100 border-dashed border-gray-300 hover-border-primary border-active shadow-sm" style="transition:0.3s">
                    <div class="card-body d-flex flex-center flex-column p-4 position-relative">
                        <!-- Actions -->
                        <div class="position-absolute top-0 end-0 m-2 z-index-1 d-flex gap-1">
                            <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm feature-image {{ $img->feature ? 'active text-warning' : 'text-gray-400' }}" title="تمييز كصورة غلاف">
                                <i class="ki-duotone ki-star fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-image text-gray-400" title="حذف">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </a>
                        </div>
                        
                        <div class="symbol symbol-100px mb-0 h-100 w-100 overflow-hidden rounded">
                            <img src="{{ asset('File/Images/photo/'.$img->image) }}" class="" style="width:100%; height:120px; object-fit:cover;" alt="image" />
                            <div class="loading-overlay d-none position-absolute top-0 start-0 w-100 h-100 d-flex flex-center bg-white bg-opacity-75">
                                <span class="spinner-border text-primary" role="status"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Clone Template -->
        <div style="display:none;">
            <div class="col-md-3 col-lg-2 col-6 image-holder h-100 mb-6">
                <div class="card card-flush h-100 border-dashed border-gray-300 hover-border-primary border-active shadow-sm">
                    <div class="card-body d-flex flex-center flex-column p-4 position-relative">
                        <div class="position-absolute top-0 end-0 m-2 z-index-1 d-flex gap-1">
                            <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-warning btn-sm feature-image text-gray-400">
                                <i class="ki-duotone ki-star fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            </a>
                            <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm delete-image text-gray-400">
                                <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
                            </a>
                        </div>
                        <div class="symbol symbol-100px mb-0 h-100 w-100 overflow-hidden rounded">
                            <img src="" class="" style="width:100%; height:120px; object-fit:cover;" alt="image" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
@section('modal')
@include('admin.layout.ajax')
@stop
@section('css')
<style>
    .feature-image.active { color: #ffc700 !important; }
    .temp .card { transition: all 0.3s ease; }
    .temp .card:hover { transform: translateY(-5px); }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Handle Drag and Drop
        $("#drop-area").on('dragenter', function (e) {
            e.preventDefault();
            $(this).addClass('border-primary bg-light-primary');
        });
        $("#drop-area").on('dragleave', function (e) {
            e.preventDefault();
            $(this).removeClass('bg-light-primary');
        });
        $("#drop-area").on('dragover', function (e) {
            e.preventDefault();
        });
        $("#drop-area").on('drop', function (e) {
            e.preventDefault();
            $(this).removeClass('bg-light-primary border-primary');
            var images = e.originalEvent.dataTransfer.files;
            $.each(images, function (index, value) {
                if(value.type.startsWith('image/')) {
                    upload(value);
                }
            });
        });
        
        // Click to upload
        $("#drop-area").on('click', function() {
            $('<input type="file" multiple accept="image/*">').on('change', function() {
                $.each(this.files, function(index, value) {
                    upload(value);
                });
            }).click();
        });

        // Use event delegation for delete and feature buttons
        $(".temp").on("click", ".delete-image", function (e) {
            e.preventDefault();
            let $btn = $(this);
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذه الصورة!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var $card = $btn.closest('.col-md-3');
                    var filenme = $card.find('img').attr('src');
                    
                    var form_data = new FormData();
                    form_data.append('_method', 'DELETE');
                    form_data.append('file', filenme);
                    form_data.append('_token', '{{csrf_token()}}');
                    
                    $.ajax({
                        url: "{{ url('admin/ajax-remove-image') }}",
                        data: form_data,
                        type: 'POST',
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            $card.fadeOut(300, function() { $(this).remove(); });
                        },
                        error: function (xhr) {
                            Swal.fire("خطأ", "فشل الحذف", "error");
                        }
                    });
                }
            });
        });

        $(".temp").on("click", ".feature-image", function (e) {
            e.preventDefault();
            var $link = $(this);
            var $card = $link.closest('.col-md-3');
            var filenme = $card.find('img').attr('src');
            
            var form_data = new FormData();
            form_data.append('file', filenme);
            form_data.append('_token', '{{csrf_token()}}');
            
            $.ajax({
                url: "{{ url('admin/ajax-feature-image') }}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (data) {
                    $('.feature-image').removeClass('active text-warning').addClass('text-gray-400');
                    $link.addClass('active text-warning').removeClass('text-gray-400');
                    toastr.success('تم تعيين صورة الغلاف بنجاح');
                }
            });
        });
    });

    function upload(img) {
        var form_data = new FormData();
        form_data.append('file', img);
        form_data.append('id', '{{ $id }}');
        form_data.append('_token', '{{csrf_token()}}');
        
        $.ajax({
            url: "{{ url('admin/ajax-image-upload') }}",
            data: form_data,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (data) {
                if (data.fail) {
                    toastr.error('خطأ في الرفع');
                } else {
                    var $newImage = $('.image-holder').clone().removeClass('image-holder');
                    $newImage.find('img').attr('src', '{{asset("File/Images/photo")}}/' + data);
                    $('.temp').prepend($newImage.fadeIn(500));
                    toastr.success('تم الرفع بنجاح');
                }
            },
            error: function (xhr) {
                toastr.error('فشل في رفع الصورة');
            }
        });
    }
</script>
@stop
