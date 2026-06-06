@extends('admin.layout.master')

@section('title', 'تعديل ألبوم')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted"><a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted"><a href="{{ route('photos.view') }}" class="text-muted text-hover-info">الألبومات</a></li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-muted">تعديل: {{ $info->title }}</li>
@stop

@section('page-content')
@php $active_menu = 'photos'; @endphp

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bold"><i class="bi bi-images text-primary me-2"></i>تعديل الألبوم</h3></div>
            </div>
            <div class="card-body py-4">
                @include('admin.layout.masterLayouts.error')

                <form method="post" action="{{ route('photos.edit', ['id' => Crypt::encrypt($info->id)]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-5">
                        <div class="col-md-12">
                            <label class="form-label fw-bold required">عنوان الألبوم</label>
                            <input type="text" name="title" value="{{ old('title', $info->title) }}" class="form-control form-control-solid" placeholder="عنوان الألبوم">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">الوصف</label>
                            <textarea name="descs" rows="5" class="form-control form-control-solid" style="resize:none;" placeholder="وصف مختصر للألبوم">{{ old('descs', $info->descs) }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">الكلمات الدلالية</label>
                            <input type="text" name="tags" value="{{ old('tags', $info->tags) }}" class="form-control form-control-solid" placeholder="افصل بين الكلمات بفاصلة (,)">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold d-block mb-2">الحالة</label>
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" name="status" {{ old('status', $info->status) == 1 ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold ms-3">تفعيل الألبوم وعرضه في الموقع</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end pt-6 mt-6 border-top">
                        <a href="{{ route('photos.view') }}" class="btn btn-light me-3">إلغاء</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Images Section -->
<div class="row justify-content-center mt-8">
    <div class="col-lg-9">
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-6">
                <div class="card-title"><h3 class="fw-bold"><i class="bi bi-image text-success me-2"></i>صور الألبوم</h3></div>
                <div class="card-toolbar">
                    <span class="badge badge-light-success fs-7"><i class="bi bi-check-circle me-1"></i>يتم الحفظ تلقائياً</span>
                </div>
            </div>
            <div class="card-body py-4">
                <!-- Upload Area -->
                <div class="fv-row mb-8">
                    <div class="border-dashed border-success rounded d-flex flex-column flex-center p-8" id="drop-area-edit" style="cursor: pointer; min-height: 130px; background-color: #f1fff8; border-width: 2px; border-style: dashed;">
                        <i class="bi bi-cloud-arrow-up fs-3x text-success mb-3"></i>
                        <h3 class="fs-5 fw-bold text-gray-900 mb-1">اسحب الصور هنا أو انقر للرفع</h3>
                        <span class="fs-7 fw-semibold text-gray-500">يمكنك رفع عدة صور في وقت واحد — يتم الحفظ تلقائياً</span>
                    </div>
                </div>

                <!-- Images Grid -->
                <div class="row g-4 temp-edit">
                    @php
                        $albumImages = \App\Models\Images::where('album_id', Crypt::decrypt(request()->route('id')))->orderBy('feature','desc')->get();
                    @endphp
                    @foreach($albumImages as $img)
                    <div class="col-md-3 col-6">
                        <div class="card card-flush border-dashed border-gray-300 shadow-sm position-relative overflow-hidden" style="transition:0.3s">
                            <img src="{{ asset('File/Images/photo/'.$img->image) }}" style="width:100%; height:130px; object-fit:cover;" alt="image">
                            <div class="position-absolute top-0 end-0 m-1 d-flex gap-1">
                                <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-bg-white btn-active-color-warning feature-image {{ $img->feature ? 'text-warning' : 'text-gray-400' }}" title="صورة غلاف">
                                    <i class="bi bi-star-fill fs-5"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-bg-white btn-active-color-danger delete-image text-gray-400" title="حذف">
                                    <i class="bi bi-trash fs-5"></i>
                                </a>
                            </div>
                            @if($img->feature)
                            <div class="position-absolute bottom-0 start-0 w-100 text-center py-1" style="background:rgba(255,199,0,0.85);">
                                <span class="fs-8 fw-bold text-dark"><i class="bi bi-star-fill me-1"></i>صورة الغلاف</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Clone Template -->
                <div style="display:none;">
                    <div class="col-md-3 col-6 image-holder-edit">
                        <div class="card card-flush border-dashed border-gray-300 shadow-sm position-relative overflow-hidden">
                            <img src="" style="width:100%; height:130px; object-fit:cover;" alt="image">
                            <div class="position-absolute top-0 end-0 m-1 d-flex gap-1">
                                <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-bg-white btn-active-color-warning feature-image text-gray-400">
                                    <i class="bi bi-star-fill fs-5"></i>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-bg-white btn-active-color-danger delete-image text-gray-400">
                                    <i class="bi bi-trash fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function () {
    var albumId = '{{ Crypt::encrypt($info->id) }}';

    // Drag & Drop
    $("#drop-area-edit").on('dragover', function(e) { e.preventDefault(); $(this).css('background-color','#d4f5e4'); });
    $("#drop-area-edit").on('dragleave', function(e) { e.preventDefault(); $(this).css('background-color','#f1fff8'); });
    $("#drop-area-edit").on('drop', function(e) {
        e.preventDefault(); $(this).css('background-color','#f1fff8');
        $.each(e.originalEvent.dataTransfer.files, function(i, file) { if(file.type.startsWith('image/')) uploadEdit(file); });
    });
    $("#drop-area-edit").on('click', function() {
        $('<input type="file" multiple accept="image/*">').on('change', function() {
            $.each(this.files, function(i, file) { uploadEdit(file); });
        }).click();
    });

    // Delete
    $(".temp-edit").on("click", ".delete-image", function(e) {
        e.preventDefault();
        var $btn = $(this), $card = $btn.closest('[class*="col-"]'), filenme = $card.find('img').attr('src');
        Swal.fire({ title:'هل أنت متأكد؟', text:'لن تتمكن من استعادة هذه الصورة!', icon:'warning', showCancelButton:true, confirmButtonText:'نعم، احذف', cancelButtonText:'إلغاء', customClass:{confirmButton:'btn btn-danger',cancelButton:'btn btn-light'} })
        .then((result) => {
            if (result.isConfirmed) {
                var fd = new FormData(); fd.append('_method','DELETE'); fd.append('file',filenme); fd.append('_token','{{csrf_token()}}');
                $.ajax({ url:"{{ url('admin/ajax-remove-image') }}", data:fd, type:'POST', contentType:false, processData:false,
                    success: function() { $card.fadeOut(300, function(){ $(this).remove(); }); },
                    error: function() { Swal.fire("خطأ","فشل الحذف","error"); }
                });
            }
        });
    });

    // Feature
    $(".temp-edit").on("click", ".feature-image", function(e) {
        e.preventDefault();
        var $link = $(this), $card = $link.closest('[class*="col-"]'), filenme = $card.find('img').attr('src');
        var fd = new FormData(); fd.append('file',filenme); fd.append('_token','{{csrf_token()}}');
        $.ajax({ url:"{{ url('admin/ajax-feature-image') }}", data:fd, type:'POST', contentType:false, processData:false,
            success: function() {
                $('.temp-edit .feature-image').removeClass('text-warning').addClass('text-gray-400');
                $('.temp-edit .bottom-0').remove();
                $link.addClass('text-warning').removeClass('text-gray-400');
                $card.find('.card').append('<div class="position-absolute bottom-0 start-0 w-100 text-center py-1" style="background:rgba(255,199,0,0.85);"><span class="fs-8 fw-bold text-dark"><i class="bi bi-star-fill me-1"></i>صورة الغلاف</span></div>');
                toastr.success('تم تعيين صورة الغلاف بنجاح');
            }
        });
    });

    function uploadEdit(img) {
        var fd = new FormData(); fd.append('file',img); fd.append('id',albumId); fd.append('_token','{{csrf_token()}}');
        $.ajax({ url:"{{ url('admin/ajax-image-upload') }}", data:fd, type:'POST', contentType:false, processData:false,
            success: function(data) {
                if (data && !data.fail) {
                    var $newImg = $('.image-holder-edit').clone().removeClass('image-holder-edit');
                    $newImg.find('img').attr('src','{{ asset("File/Images/photo") }}/'+data);
                    $('.temp-edit').prepend($newImg.fadeIn(400));
                    toastr.success('تم رفع الصورة بنجاح');
                } else { toastr.error('خطأ في الرفع'); }
            },
            error: function() { toastr.error('فشل في رفع الصورة'); }
        });
    }
});
</script>
@endsection
