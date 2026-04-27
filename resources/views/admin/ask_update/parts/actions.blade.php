@can('admin.ask_update.delete')
<div class="d-flex justify-content-end gap-2">
    <button type="button" class="btn btn-sm btn-light-success accept font-weight-bolder" data-id="{{ Crypt::encrypt($id) }}">
        <i class="ki-duotone ki-check fs-2"></i> سماح
    </button>
    <button type="button" class="btn btn-sm btn-light-danger refuse font-weight-bolder" data-id="{{ Crypt::encrypt($id) }}">
        <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> رفض
    </button>
</div>
@endcan
