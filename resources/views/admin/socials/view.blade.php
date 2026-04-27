@extends('admin.layout.master')

@section('title', 'إدارة الشبكات الإجتماعية')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">إدارة الشبكات الإجتماعية</li>
@stop

@section('page-content')
<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <div class="d-flex align-items-center position-relative my-1">
                <i class="ki-duotone ki-element-plus fs-1 text-primary me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                <h3 class="fw-bold m-0 text-gray-800">إدارة حسابات التواصل الإجتماعي</h3>
            </div>
        </div>
        <div class="card-toolbar">
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <a href="{{ url()->previous() }}" class="btn btn-light-primary btn-sm me-3">
                    <i class="ki-duotone ki-arrow-right fs-2 me-1"><span class="path1"></span><span class="path2"></span></i>
                    رجوع
                </a>
            </div>
        </div>
    </div>

    <div class="card-body py-8">
        @include('admin.layout.masterLayouts.error')
        
        <form role="form" method="post" action="{{ route('socials.view') }}" id="socials_form">
            @csrf
            <div class="row g-6 g-xl-9">
                @foreach ($socials as $row)
                @php
                    $socialLower = strtolower($row->name);
                    $logo = 'generic-logo.svg';
                    
                    if (str_contains($socialLower, 'facebook')) $logo = 'facebook-3.svg';
                    elseif (str_contains($socialLower, 'twitter') || str_contains($socialLower, 'x.')) $logo = 'twitter.svg';
                    elseif (str_contains($socialLower, 'instagram')) $logo = 'instagram-2-1.svg';
                    elseif (str_contains($socialLower, 'youtube')) $logo = 'youtube-3.svg';
                    elseif (str_contains($socialLower, 'linkedin')) $logo = 'linkedin-2.svg';
                    elseif (str_contains($socialLower, 'snap')) $logo = 'snapchat.svg';
                    elseif (str_contains($socialLower, 'tiktok')) $logo = 'tiktok.svg';
                    elseif (str_contains($socialLower, 'whatsapp')) $logo = 'whatsapp.svg';
                    
                    $logoPath = 'assets/media/svg/brand-logos/' . $logo;
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card border-hover-primary h-100 shadow-sm border border-gray-200">
                        <div class="card-header border-0 pt-7">
                            <div class="card-title m-0">
                                <div class="symbol symbol-50px w-50px bg-light">
                                    @if(file_exists(public_path($logoPath)))
                                        <img src="{{ asset($logoPath) }}" alt="image" class="p-3" />
                                    @else
                                        <img src="{{ asset('assets/media/svg/files/blank-image.svg') }}" alt="image" class="p-3 opacity-50" />
                                    @endif

                                </div>
                            </div>

                            <div class="card-toolbar">
                                <div class="form-check form-switch form-check-custom form-check-solid" title="تفعيل / تعطيل">
                                    <input type="hidden" value="0" name="status[{{ $row->id }}]">
                                    <input class="form-check-input h-25px w-45px" type="checkbox" value="1" name="status[{{ $row->id }}]" {{ $row->status == 1 ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-7 pt-5">
                            <div class="fs-3 fw-bold text-gray-900 mb-2">{{ $row->name }}</div>
                            <p class="text-gray-400 fw-semibold fs-6 mb-7">أضف رابط الحساب الخاص بمنصة {{ $row->name }} ليظهر في الموقع الرئيسي.</p>

                            <div class="d-flex flex-stack position-relative mt-auto">
                                <div class="input-group input-group-solid">
                                    <span class="input-group-text">
                                        <i class="ki-duotone ki-link fs-2"><span class="path1"></span><span class="path2"></span></i>
                                    </span>
                                    <input type="text" value="{{ $row->link }}" name="link[{{ $row->id }}]" class="form-control form-control-solid fs-7 ps-0" placeholder="https://..." />
                                </div>
                                <input type="hidden" value="{{ $row->id }}" name="id[]">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="separator separator-dashed my-10"></div>

            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-primary px-15 py-4 fs-4 shadow-sm" id="kt_socials_submit">
                    <span class="indicator-label">
                        <i class="ki-duotone ki-check-circle fs-1 me-2"><span class="path1"></span><span class="path2"></span></i>
                        حفظ جميع التغييرات
                    </span>
                    <span class="indicator-progress">يرجى الانتظار... 
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                </button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
    const form = document.querySelector('#socials_form');
    const submitButton = document.querySelector('#kt_socials_submit');

    if (form) {
        form.addEventListener('submit', function (e) {
            submitButton.setAttribute('data-kt-indicator', 'on');
            submitButton.disabled = true;
        });
    }
</script>
@stop

