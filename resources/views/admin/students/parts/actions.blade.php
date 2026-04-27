<div class="dropdown">
    <a href="#" class="btn btn-sm btn-light btn-active-light-primary dropdown-toggle border" data-bs-toggle="dropdown" 
        data-bs-boundary="viewport" data-kt-menu-placement="bottom-end" aria-expanded="false">
        إجراءات
    </a>
    <div class="dropdown-menu menu menu-sub menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
        @can('admin.students.edit')
            <div class="menu-item px-3">
                <a href="{{ route('students.edit', ['id' => Crypt::encrypt($id)]) }}" class="menu-link px-3">
                    <span class="menu-icon"><i class="ki-duotone ki-pencil fs-3 text-info"><span class="path1"></span><span class="path2"></span></i></span>
                    <span class="menu-title">تعديل البيانات</span>
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="{{ route('students.gropes',[ 'id' => $id]) }}" class="menu-link px-3">
                    <span class="menu-icon"><i class="ki-duotone ki-element-11 fs-3 text-primary"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i></span>
                    <span class="menu-title">سجل المجموعات</span>
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="{{ route('students.groups.add', Crypt::encrypt($id)) }}" class="menu-link px-3">
                    <span class="menu-icon"><i class="ki-duotone ki-plus-square fs-3 text-success"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i></span>
                    <span class="menu-title">تسجيل بمجموعة</span>
                </a>
            </div>
            <div class="menu-item px-3">
                <a class="menu-link px-3 Reply" style="cursor: pointer;" data-id="{{ Crypt::encrypt($id) }}">
                    <span class="menu-icon"><i class="ki-duotone ki-notification-on fs-3 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                    <span class="menu-title">إرسال إشعار</span>
                </a>
            </div>
        @endcan
        
        <div class="separator my-2 opacity-75"></div>
        
        <div class="menu-item px-3">
            <a href="{{ route('students.password', ['id' => Crypt::encrypt($id)]) }}" class="menu-link px-3">
                <span class="menu-icon"><i class="ki-duotone ki-key fs-3 text-dark"><span class="path1"></span><span class="path2"></span></i></span>
                <span class="menu-title">كلمة المرور</span>
            </a>
        </div>
        
        @can('admin.students.delete')
            <div class="separator my-2 opacity-75"></div>
            <div class="menu-item px-3">
                <a class="menu-link px-3 delete-btn" style="cursor: pointer;" href="#confirm" data-href="{{ Crypt::encrypt($id) }}" data-bs-toggle="modal">
                    <span class="menu-icon"><i class="ki-duotone ki-trash fs-3 text-danger"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>
                    <span class="menu-title text-danger">حذف الطالب</span>
                </a>
            </div>
        @endcan
    </div>
</div>
