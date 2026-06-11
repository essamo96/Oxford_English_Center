@extends('admin.layout.master')

@section('title')
    تحديد صلاحيات {{ $info->name }}
@stop

@section('page-breadcrumb')
    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
        </li>
        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
        <li class="breadcrumb-item text-muted">
            <a href="{{ route('roles.view') }}" class="text-muted text-hover-info">إدارة الصلاحيات</a>
        </li>
        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
        <li class="breadcrumb-item text-dark">تحديد صلاحيات المجموعة</li>
    </ul>
@stop

@section('page-content')

{{-- ─── ملاحظة عدد الصلاحيات المحددة ─────────────────────────────────── --}}
<div class="d-flex align-items-center justify-content-between mb-5">
    <h2 class="fw-bold m-0">
        <i class="ki-duotone ki-element-plus fs-2 text-info me-2">
            <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>
        </i>
        تحديد صلاحيات: <span class="text-primary">{{ $info->name }}</span>
    </h2>
    <div class="d-flex align-items-center gap-3">
        <span class="badge badge-light-info fs-6 fw-bold" id="selected-count">0 محددة</span>
        <button type="button" class="btn btn-sm btn-light-success fw-bold" id="btn-select-all">تحديد الكل</button>
        <button type="button" class="btn btn-sm btn-light-danger fw-bold"  id="btn-deselect-all">إلغاء الكل</button>
    </div>
</div>

<form role="form" method="post" action="" id="permissions-form">
    @csrf

    @include('admin.layout.error')

    @php
        $checkedIds = array_column($role_permissions, 'permission_id');
    @endphp

    {{-- ─── action labels in the fixed order ─────────────────────────── --}}
    @php
        $actionLabels = [
            'view'   => 'عرض',
            'edit'   => 'تعديل',
            'add'    => 'إضافة',
            'status' => 'تغيير الحالة',
            'delete' => 'حذف',
            'verify' => 'تحقق',
            'refund' => 'استرداد',
            'reply'  => 'رد',
        ];
    @endphp

    @foreach($permission_groups as $parent)

        @php
            // Collect ALL permissions under this parent (parent's own + all children's)
            $allPermsUnder = $parent->permissions->pluck('id')->toArray();
            foreach ($parent->mychild as $child) {
                $allPermsUnder = array_merge($allPermsUnder, $child->permissions->pluck('id')->toArray());
            }
            $hasAny = count($allPermsUnder) > 0;
        @endphp

        @if(!$hasAny)
            @continue
        @endif

        <div class="card shadow-sm border-0 mb-6 permission-section">
            {{-- ── Section header ───────────────────────────────────────── --}}
            <div class="card-header min-h-50px bg-light-primary border-bottom-0 rounded-top d-flex align-items-center justify-content-between pe-5">
                <h4 class="card-title fw-bold text-gray-800 m-0 d-flex align-items-center gap-2">
                    <i class="{{ $parent->icon ?? 'ki-duotone ki-element-plus' }} fs-2 text-primary">
                        <span class="path1"></span><span class="path2"></span>
                        <span class="path3"></span><span class="path4"></span>
                    </i>
                    {{ $parent->name_ar ?? $parent->name }}
                </h4>
                <label class="form-check form-check-custom form-check-solid">
                    <input type="checkbox" class="form-check-input section-toggle"
                           data-section="{{ $parent->id }}" />
                    <span class="form-check-label fw-semibold text-gray-600 fs-7">تحديد القسم كاملاً</span>
                </label>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-gray-200 align-middle mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-light fs-7 text-uppercase">
                                <th class="ps-5 w-200px">القسم</th>
                                <th class="text-center min-w-80px">عرض</th>
                                <th class="text-center min-w-80px">تعديل</th>
                                <th class="text-center min-w-80px">إضافة</th>
                                <th class="text-center min-w-120px">تغيير الحالة</th>
                                <th class="text-center min-w-80px">حذف</th>
                                <th class="text-center min-w-80px">أخرى</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- ── Parent's own permissions (e.g. academy_management.view) ── --}}
                            @if($parent->permissions->count() > 0)
                                <tr class="bg-light-warning border-bottom border-warning">
                                    <td class="ps-5 fw-bold text-warning fs-7">
                                        <i class="ki-duotone ki-key fs-4 text-warning me-1"><span class="path1"></span><span class="path2"></span></i>
                                        {{ $parent->name_ar ?? $parent->name }}
                                        <span class="badge badge-light-warning ms-1 fs-8">وصول القسم</span>
                                    </td>
                                    @php
                                        $parentPerms  = $parent->permissions->keyBy(fn($p) => last(explode('.', $p->name)));
                                        $standardKeys = ['view','edit','add','status','delete'];
                                        $extraKeys    = $parent->permissions->filter(fn($p) => !in_array(last(explode('.', $p->name)), $standardKeys));
                                    @endphp
                                    @foreach(['view','edit','add','status','delete'] as $action)
                                        <td class="text-center">
                                            @if(isset($parentPerms[$action]))
                                                @php $perm = $parentPerms[$action]; @endphp
                                                <label class="form-check form-check-custom form-check-solid justify-content-center section-perm-{{ $parent->id }}">
                                                    <input class="form-check-input permission-cb" type="checkbox"
                                                           name="permissions[]" value="{{ $perm->id }}"
                                                           title="{{ $perm->name_ar ?? $perm->name }}"
                                                           {{ in_array($perm->id, $checkedIds) ? 'checked' : '' }} />
                                                </label>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        @foreach($extraKeys as $perm)
                                            <label class="form-check form-check-custom form-check-solid d-inline-flex me-2 section-perm-{{ $parent->id }}">
                                                <input class="form-check-input permission-cb" type="checkbox"
                                                       name="permissions[]" value="{{ $perm->id }}"
                                                       {{ in_array($perm->id, $checkedIds) ? 'checked' : '' }} />
                                                <span class="form-check-label fw-semibold text-gray-600 fs-8 ms-1">
                                                    {{ $perm->name_ar ?? last(explode('.', $perm->name)) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif

                            {{-- ── Child modules ────────────────────────────────────────── --}}
                            @foreach($parent->mychild as $child)
                                @if($child->permissions->count() == 0) @continue @endif
                                @php
                                    $childPerms  = $child->permissions->keyBy(fn($p) => last(explode('.', $p->name)));
                                    $extraChildPerms = $child->permissions->filter(fn($p) => !in_array(last(explode('.', $p->name)), ['view','edit','add','status','delete']));
                                @endphp
                                <tr>
                                    <td class="ps-5 fw-semibold text-gray-700">
                                        {{ $child->name_ar ?? $child->name }}
                                    </td>
                                    @foreach(['view','edit','add','status','delete'] as $action)
                                        <td class="text-center">
                                            @if(isset($childPerms[$action]))
                                                @php $perm = $childPerms[$action]; @endphp
                                                <label class="form-check form-check-custom form-check-solid justify-content-center section-perm-{{ $parent->id }}">
                                                    <input class="form-check-input permission-cb" type="checkbox"
                                                           name="permissions[]" value="{{ $perm->id }}"
                                                           title="{{ $perm->name_ar ?? $perm->name }}"
                                                           {{ in_array($perm->id, $checkedIds) ? 'checked' : '' }} />
                                                </label>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-center">
                                        @foreach($extraChildPerms as $perm)
                                            <label class="form-check form-check-custom form-check-solid d-inline-flex me-2 section-perm-{{ $parent->id }}">
                                                <input class="form-check-input permission-cb" type="checkbox"
                                                       name="permissions[]" value="{{ $perm->id }}"
                                                       {{ in_array($perm->id, $checkedIds) ? 'checked' : '' }} />
                                                <span class="form-check-label fw-semibold text-gray-600 fs-8 ms-1">
                                                    {{ $perm->name_ar ?? last(explode('.', $perm->name)) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endforeach

    {{-- ── Save button ─────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-end mt-8 gap-3">
        <a href="{{ route('roles.view') }}" class="btn btn-light">إلغاء</a>
        <button type="submit" class="btn btn-primary px-10">
            <i class="ki-duotone ki-check fs-2 me-1"><span class="path1"></span><span class="path2"></span></i>
            حفظ الصلاحيات
        </button>
    </div>

</form>

@section('js')
<script>
(function () {
    // ── Count & display selected checkboxes ──────────────────────────────
    function updateCount() {
        const total = document.querySelectorAll('.permission-cb:checked').length;
        document.getElementById('selected-count').textContent = total + ' محددة';
    }

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('permission-cb')) updateCount();
    });

    // ── Select / Deselect All ────────────────────────────────────────────
    document.getElementById('btn-select-all').addEventListener('click', function () {
        document.querySelectorAll('.permission-cb').forEach(cb => cb.checked = true);
        document.querySelectorAll('.section-toggle').forEach(cb => cb.checked = true);
        updateCount();
    });

    document.getElementById('btn-deselect-all').addEventListener('click', function () {
        document.querySelectorAll('.permission-cb').forEach(cb => cb.checked = false);
        document.querySelectorAll('.section-toggle').forEach(cb => cb.checked = false);
        updateCount();
    });

    // ── Section toggle (select/deselect a whole parent section) ─────────
    document.querySelectorAll('.section-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const sectionId = this.dataset.section;
            document.querySelectorAll('.section-perm-' + sectionId + ' .permission-cb').forEach(function (cb) {
                cb.checked = toggle.checked;
            });
            updateCount();
        });
    });

    // ── Init count on page load ──────────────────────────────────────────
    updateCount();
})();
</script>
@endsection

@stop
