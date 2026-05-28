@extends('admin.layout.master')

@section('title', 'صلات القرابة')

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
    <li class="breadcrumb-item text-dark">صلات القرابة</li>
@stop

@section('page-content')
@php $active_menu = 'relationships'; @endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-5">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('danger'))
    <div class="alert alert-danger alert-dismissible fade show mb-5">
        <i class="bi bi-x-circle-fill me-2"></i> {{ session('danger') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    {{-- Left: Add form --}}
    <div class="col-xl-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">
                    <i class="bi bi-plus-circle-fill me-2 text-warning"></i>
                    إضافة صلة قرابة جديدة
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.relationships.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold required">الاسم (عربي)</label>
                        <input type="text" name="name_ar" class="form-control" placeholder="مثال: العم" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">الاسم (إنجليزي)</label>
                        <input type="text" name="name_en" class="form-control" placeholder="e.g. Uncle">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">ترتيب الظهور</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active_new" checked>
                        <label class="form-check-label fw-bold" for="active_new">مفعّل</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save2-fill me-2"></i> حفظ
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Right: Existing list --}}
    <div class="col-xl-8">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-people-fill me-2 text-primary"></i>
                    صلات القرابة المتاحة
                </h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fs-7 fw-bold">{{ $relationships->count() }} صلة</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-5 gy-4 mb-0">
                        <thead class="bg-light">
                            <tr class="fw-bold text-muted text-uppercase fs-7">
                                <th class="min-w-150px">الاسم (عربي)</th>
                                <th class="min-w-120px">الاسم (إنجليزي)</th>
                                <th class="min-w-80px text-center">الترتيب</th>
                                <th class="min-w-80px text-center">الحالة</th>
                                <th class="min-w-100px text-center">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($relationships as $rel)
                                <tr>
                                    <td>
                                        <span class="text-dark fw-bold fs-6">{{ $rel->name_ar }}</span>
                                        @if($rel->is_other)
                                            <span class="badge badge-light-info ms-2 fs-9">خيار أخرى</span>
                                        @endif
                                    </td>
                                    <td><span class="text-muted">{{ $rel->name_en ?? '—' }}</span></td>
                                    <td class="text-center">{{ $rel->sort_order }}</td>
                                    <td class="text-center">
                                        @if($rel->is_active)
                                            <span class="badge badge-light-success fw-bold">مفعّل</span>
                                        @else
                                            <span class="badge badge-light-danger fw-bold">غير مفعّل</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-icon btn-light-primary me-1"
                                            data-bs-toggle="modal" data-bs-target="#editRel{{ $rel->id }}"
                                            title="تعديل">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        @if(!$rel->is_other)
                                            <form action="{{ route('admin.relationships.destroy', $rel->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="حذف">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Edit modal --}}
                                <div class="modal fade" id="editRel{{ $rel->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.relationships.update', $rel->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تعديل: {{ $rel->name_ar }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold required">الاسم (عربي)</label>
                                                        <input type="text" name="name_ar" class="form-control" value="{{ $rel->name_ar }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">الاسم (إنجليزي)</label>
                                                        <input type="text" name="name_en" class="form-control" value="{{ $rel->name_en }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">ترتيب الظهور</label>
                                                        <input type="number" name="sort_order" class="form-control" value="{{ $rel->sort_order }}" min="0">
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $rel->id }}" {{ $rel->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-bold" for="active{{ $rel->id }}">مفعّل</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ التعديل</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-8">
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
                                        لا توجد صلات قرابة بعد
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
