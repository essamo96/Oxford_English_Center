@extends('admin.layout.master')

@section('title', 'الرد على رسالة')

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-muted">
    <a href="{{ route('contacts.view') }}" class="text-muted text-hover-info">إدارة اتصل بنا</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">الرد على {{ $info->name }}</li>
@stop

@section('page-content')
@php $active_menu = 'contacts'; @endphp

<div class="row g-7">

    {{-- ----------- Original message (read only) ----------- --}}
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="card-label fw-bold fs-4 text-info">
                        <i class="ki-duotone ki-message-text-2 fs-2 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        الرسالة الأصلية
                    </span>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="d-flex flex-column gap-5">
                    <div>
                        <div class="text-muted fw-semibold fs-7 mb-1">الاسم</div>
                        <div class="fw-bold fs-6 text-gray-800">{{ $info->name }}</div>
                    </div>

                    <div>
                        <div class="text-muted fw-semibold fs-7 mb-1">البريد الإلكتروني</div>
                        @if(!empty($info->email))
                            <a href="mailto:{{ $info->email }}" class="fw-bold fs-6 text-info text-hover-primary" dir="ltr">{{ $info->email }}</a>
                        @else
                            <span class="badge badge-light-danger">غير متوفر</span>
                        @endif
                    </div>

                    <div>
                        <div class="text-muted fw-semibold fs-7 mb-1">الجوال</div>
                        @if(!empty($info->mobile))
                            <span class="fw-bold fs-6 text-gray-800" dir="ltr">{{ $info->mobile }}</span>
                        @else
                            <span class="badge badge-light">غير متوفر</span>
                        @endif
                    </div>

                    <div>
                        <div class="text-muted fw-semibold fs-7 mb-1">الموضوع</div>
                        <div class="fw-bold fs-6 text-gray-800">{{ $info->subject ?: '—' }}</div>
                    </div>

                    <div>
                        <div class="text-muted fw-semibold fs-7 mb-1">نص الرسالة</div>
                        <div class="bg-light-info rounded p-4 fs-6 text-gray-700" style="white-space:pre-line;">{{ $info->message ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ----------- Reply form ----------- --}}
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <span class="card-label fw-bold fs-4 text-primary">
                        <i class="ki-duotone ki-send fs-2 text-primary me-2"><span class="path1"></span><span class="path2"></span></i>
                        الرد عبر البريد الإلكتروني
                    </span>
                </div>
            </div>
            <div class="card-body pt-3">
                @include('admin.layout.error')

                <form role="form" method="post" action="{{ route('contacts.reply.send') }}" class="form">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{ Crypt::encrypt($info->id) }}">

                    <div class="mb-6">
                        <label class="form-label required fw-semibold">المُرسَل إليه</label>
                        <input type="text" class="form-control form-control-solid bg-light" value="{{ $info->email ?: 'لا يوجد بريد إلكتروني' }}" dir="ltr" readonly>
                    </div>

                    <div class="mb-6">
                        <label class="form-label required fw-semibold">الموضوع</label>
                        <input type="text" name="subject" class="form-control form-control-solid"
                               placeholder="موضوع البريد الإلكتروني"
                               value="{{ old('subject', $info->subject ? 'Re: ' . $info->subject : '') }}">
                    </div>

                    <div class="mb-8">
                        <label class="form-label required fw-semibold">نص الرد</label>
                        <textarea name="body" class="form-control form-control-solid" rows="8"
                                  placeholder="اكتب ردك هنا... سيتم إرساله إلى البريد الإلكتروني للمرسل">{{ old('body') }}</textarea>
                        <div class="form-text">سيتم إرسال هذا النص إلى <strong>{{ $info->email ?: 'المرسل' }}</strong> ضمن قالب البريد الرسمي للمركز.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('contacts.view') }}" class="btn btn-light">إلغاء</a>
                        <button type="submit" class="btn btn-primary" @if(empty($info->email)) disabled @endif>
                            <i class="bi bi-send-fill me-1"></i> إرسال الرد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@stop

@section('js')
@stop
