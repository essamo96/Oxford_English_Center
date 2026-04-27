@can('admin.closed_classes.delete')
<a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-danger btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
    <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i>
</a>
@endcan