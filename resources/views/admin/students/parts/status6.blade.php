<div class="d-flex justify-content-center">
    @if($status == 0)
        <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-danger status min-w-100px">
            <i class="ki-duotone ki-cross-circle fs-5 me-1"><span class="path1"></span><span class="path2"></span></i> غير مفعل
        </button>
    @elseif($status == 1)
        <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-success status min-w-100px">
            <i class="ki-duotone ki-check-circle fs-5 me-1"><span class="path1"></span><span class="path2"></span></i> مفعل
        </button>
    @endif
</div>