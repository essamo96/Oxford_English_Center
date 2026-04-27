@can('admin.'.$active_menu.'.delete')
<div class="d-flex justify-content-center">
    <a class="btn btn-icon btn-light-danger btn-sm" href="javascript:void(0)" data-href="{{ Crypt::encrypt($id) }}" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
        <i class="bi bi-trash fs-4"></i>
    </a>
</div>
@endcan