<div class="d-flex justify-content-center">
    <button type="button" class="btn btn-sm btn-light btn-active-light-primary btn-flex btn-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
        العمليات
        <i class="ki-duotone ki-down fs-5 ms-1"></i>
    </button>
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
        @can('admin.students.edit')
        <div class="menu-item px-3">
            <a href="{{ route('students.edit',['id' => Crypt::encrypt($student_id)]) }}" class="menu-link px-3">
                <i class="ki-duotone ki-pencil fs-4 me-2"><span class="path1"></span><span class="path2"></span></i> تعديل بيانات الطالب
            </a>
        </div>
        @endcan

        <div class="menu-item px-3">
            <a href="{{ route('students.groups.add', ['student_id' => Crypt::encrypt($student_id), 'group_id' => Crypt::encrypt($grope_id)]) }}" class="menu-link px-3 text-primary">
                <i class="ki-duotone ki-plus-circle fs-4 me-2 text-primary"><span class="path1"></span><span class="path2"></span></i> إضافة مجموعة للطالب
            </a>
        </div>

        <div class="menu-item px-3">
            <a href="{{ route('students.groups.edit', ['student_id' => Crypt::encrypt($student_id), 'group_id' => Crypt::encrypt($grope_id)]) }}" class="menu-link px-3">
                <i class="ki-duotone ki-setting-2 fs-4 me-2"><span class="path1"></span><span class="path2"></span></i> تغيير المجموعة الحالية
            </a>
        </div>

        @can('admin.students.delete')
        <div class="separator mt-3 opacity-75"></div>
        <div class="menu-item px-3">
            <a href="javascript:void(0)" class="menu-link px-3 text-danger delete" data-href="{{ Crypt::encrypt($row_id) }}">
                <i class="ki-duotone ki-trash fs-4 me-2 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i> حذف من المجموعة
            </a>
        </div>
        @endcan
    </div>
</div>