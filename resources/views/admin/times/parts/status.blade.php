@if($status == 0)
    <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-danger @can('admin.times.status') status @endcan" style="min-width:90px;">
        <i class="bi bi-x-circle fs-5"></i> غير فعال
    </button>
@elseif($status == 1)
    <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-success @can('admin.times.status') status @endcan" style="min-width:90px;">
        <i class="bi bi-check-circle fs-5"></i> فعال
    </button>
@endif
