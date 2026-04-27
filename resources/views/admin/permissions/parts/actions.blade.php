<div class="d-flex justify-content-center gap-2">
    @can('admin.' . $active_menu . '.edit')
    <a href="{{ route($active_menu . '.edit', Crypt::encrypt($id)) }}" class="btn btn-icon btn-light-primary btn-sm" title="تعديل">
        <i class="bi bi-pencil-square fs-4"></i>
    </a>
    @endcan
    @can('admin.' . $active_menu . '.delete')
    <a class="btn btn-icon btn-light-danger btn-sm" href="javascript:void(0)" data-href="{{ Crypt::encrypt($id) }}" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
        <i class="bi bi-trash fs-4"></i>
    </a>
    @endcan
</div>
