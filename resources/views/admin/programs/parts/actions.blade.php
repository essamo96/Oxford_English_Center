<div class="d-flex justify-content-center gap-2">
    <a href="javascript:;" data-id="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-info btn-sm view-program-details" title="تفاصيل المجموعات">
        <i class="ki-duotone ki-category fs-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
    </a>
    <a href="javascript:;" onclick="showBrochureQr('{{ Crypt::encrypt($id) }}')" class="btn btn-icon btn-light-success btn-sm" title="QR Code البروشور">
        <i class="bi bi-qr-code fs-4"></i>
    </a>
    @can('admin.programs.edit')
    <a href="{{ route('programs.edit',[ 'id' => Crypt::encrypt($id)]) }}" class="btn btn-icon btn-light-primary btn-sm" title="تعديل">
        <i class="bi bi-pencil-square fs-4"></i>
    </a>
    @endcan
    @can('admin.programs.delete')
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
        <i class="bi bi-trash fs-4"></i>
    </a>
    @endcan
</div>