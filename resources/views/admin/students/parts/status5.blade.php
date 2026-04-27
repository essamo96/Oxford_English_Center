<div class="d-flex justify-content-center">
    @if($status == 0)
        <span class="badge badge-light-danger fw-bold px-4 py-2">
            <i class="ki-duotone ki-cross-circle fs-5 text-danger me-1"><span class="path1"></span><span class="path2"></span></i> غير مفعل
        </span>
    @elseif($status == 1)
        <span class="badge badge-light-success fw-bold px-4 py-2">
            <i class="ki-duotone ki-check-circle fs-5 text-success me-1"><span class="path1"></span><span class="path2"></span></i> مفعل
        </span>
    @endif
</div>