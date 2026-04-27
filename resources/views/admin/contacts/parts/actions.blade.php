<div class="d-flex justify-content-center gap-2">
    @can('admin.contact.reply')
    <a href="{{ route('contacts.reply',[ 'id' => Crypt::encrypt($id)]) }}" class="btn btn-icon btn-light-success btn-sm" title="رد">
        <i class="bi bi-reply fs-4"></i>
    </a>
    @endcan
    @can('admin.contact.delete')
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
        <i class="bi bi-trash fs-4"></i>
    </a>
    @endcan
</div>