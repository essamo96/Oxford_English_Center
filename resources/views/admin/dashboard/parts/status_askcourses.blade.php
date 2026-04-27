@if($status == 0)
    <button data-href="{{ $id }}" class="btn btn-sm btn-light-danger status" style="min-width:90px;">
        <i class="bi bi-x-circle fs-5"></i> تعطيل
    </button>
@elseif($status == 1)
    <button data-href="{{ $id }}" class="btn btn-sm btn-light-success status" style="min-width:90px;">
        <i class="bi bi-check-circle fs-5"></i> تفعيل
    </button>
@endif