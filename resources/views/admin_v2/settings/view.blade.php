@extends('admin.layout.master')
@section('title')
    {{ $current_route->{'name_' . trans('app.lang')} }}
@stop
@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ url('/') }}" class="text-muted text-hover-primary">@lang('app.home')</a>
    </li>
    <li class="breadcrumb-item text-muted">- {{ $current_route->{'name_' . trans('app.lang')} }}</li>
@stop
@section('page-content')
    <div class="card">
        <div class="card-body py-4">
            @include('admin.layout.error')
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row justify-content-center">
                    <div class="col-9">
                        <div class="form-floating mb-9 row ">
                            <div class="col">
                                <label class="p-2"> @lang('app.app_name')<span>*</span></label>
                                <input type="text" value="{{ $info ? $info->title : old('title') }}" name="title"
                                    class="form-control" />
                            </div>
                            <div class="col">
                                <label class="p-2">@lang('app.address')  <span>*</span></label>
                                <input type="text" value="{{ $info ? $info->address : old('address') }}" name="address"
                                    class="form-control" />
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="mobile">@lang('app.mobile')</label>
                                <input type="text" value="{{ $info ? $info->mobile : old('mobile') }}" name="mobile"
                                    class="form-control" />
                            </div>
                            <div class="fv-row mb-10 col">
                                <label for="contact_email" class="required fw-semibold fs-6 mb-2">@lang('app.contact_email')</label>
                                <input type="text" value="{{ $info ? $info->contact_email : old('contact_email') }}"
                                    name="contact_email" class="form-control" />
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="footer_text"> @lang('app.footer_text')</label>
                                <input type="text" value="{{ $info ? $info->footer_text : old('footer_text') }}"
                                    name="footer_text" class="form-control" />
                            </div>
                            <div class="fv-row mb-10 col">
                                <div class="mb-0">
                                    <label class="required fw-semibold fs-6 mb-2" for="footer_date">@lang('app.footer_date')</label>
                                    <input class="form-control form-control-solid" placeholder="Pick date rage" value="{{ $info ? $info->footer_date : old('footer_date') }}"
                                   type="date"     name="footer_date" id="footer_date" />
                                </div>
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="description">@lang('app.description')</label>
                                <textarea name="description" value="{{ $info ? $info->description : old('description') }}" id="description"
                                    class="form-control form-control-solid">
                                {{ $info ? $info->description : ' ' }}
                                </textarea>
                            </div>
                            <div class="fv-row mb-10 col">
                                <label for="more_desc" class="required fw-semibold fs-6 mb-2"> @lang('app.more_desc')</label>
                                <textarea name="more_desc" value="{{ $info ? $info->more_desc : old('more_desc') }}"
                                    class="form-control form-control-solid">{{ $info ? $info->more_desc : ' ' }}</textarea>
                            </div>
                        </div>
                        <div class="form-floating mb-9 row ">
                            <div class="fv-row mb-10 col">
                                <label for="kt_tagify_1" class="required fw-semibold fs-6 mb-2"> @lang('app.tags')</label>
                                <div class="mb-10">
                                    <input class="form-control" name="tags" value="dfssdf" id="kt_tagify_1" />
                                </div>
                            </div>
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="version"> @lang('app.version')</label>
                                <input type="text" value="{{ $info ? $info->version : old('version') }}" name="version"
                                    class="form-control" />
                            </div>
                            <div class="fv-row mb-10 col">
                                <label class="required fw-semibold fs-6 mb-2" for="version"> @lang('app.currency')</label>
                                <input type="text" value="{{ $info ? $info->currency : old('currency') }}" name="currency"
                                    class="form-control" />
                            </div>

                        </div>
                      <div class="form-floating mb-9 row ">
                        <div class="fv-row mb-10 col">
                            <label class="required fw-semibold fs-6 mb-2" for="version">@lang('app.logo')</label>
                            <div class="image-input image-input-outline" data-kt-image-input="true"
                                style="background-image: {{ asset('assets/images/blank.png') }}">
                                <div class="image-input-wrapper w-125px h-125px"
                                    style="background-image: {{ asset('assets/images/blank.png') }}"></div>
                                <label
                                    class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click"
                                    title="Change avatar">
                                    <i class="ki-duotone ki-pencil fs-6"><span class="path1"></span><span
                                            class="path2"></span></i>
                                    <input type="file" name="logo" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" value="{{ $info ? $info->logo : old('logo') }}" name="logo" />
                                </label>
                                <span 
                                    class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click"
                                    title="Cancel avatar">
                                    <i class="ki-outline ki-cross fs-3"></i>
                                </span>
                                <span
                                    class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click"
                                    title="Remove avatar">
                                    <i class="ki-outline ki-cross fs-3"></i>
                                </span>
                            </div>
                        </div>
                             <div class="fv-row mb-10 col">
                                <label class="p-2"> @lang('app.market_situation') <span>*</span></label>
                                <select class="form-select" aria-label="اختر.." name="market_situation">
                                    <option>@lang('app.choose')</option>
                                    <?php $data = $info ? $info->market_situation : old('market_situation'); ?>
                                        <option value="مغلق" {{$data == 'مغلق' ? 'selected' : ''}}>@lang('app.close')</option>
                                        <option value="مفتوح" {{$data == 'مغلق' ? 'selected' : ''}}>@lang('app.open')</option>
                                </select>
                            </div>
                      </div>
                        
                    </div>
                </div>
                <div class="text-center pt-2">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">@lang('app.save') </button>
                    <a type="reset" href="{{ route($active_menu . '.view') }}" class="btn btn-light me-3">@lang('app.cancel')</a>
                </div>
            </form>
        </div>
    </div>
@stop
@section('js')
    <script>
            $('#footer_date').daterangepicker({
            timePicker: false,
            autoUpdateInput: false,
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }).on("apply.daterangepicker", function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
    </script>
    {{-- <script type="text/javascript">
$(function () {
    $('#lfm').filemanager('image');

});
</script> --}}
@stop
