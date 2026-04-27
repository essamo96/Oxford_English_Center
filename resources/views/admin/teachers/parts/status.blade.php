@if(isset($X) && $X == 1)
    @if($status == 0)
        <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-danger @can('admin.teachers.status') status @endcan" style="min-width:90px;">
            <i class="bi bi-x-circle fs-5"></i> غير فعال
        </button>
    @elseif($status == 1)
        <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-success @can('admin.teachers.status') status @endcan" style="min-width:90px;">
            <i class="bi bi-check-circle fs-5"></i> فعال
        </button>
    @endif
@elseif(isset($X) && $X == 2)
    @if($status == 0)
        <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-danger evaluations" style="min-width:90px;">
            <i class="bi bi-x-circle fs-5"></i> تعطيل
        </button>
    @elseif($status == 1)
        <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-success evaluations" style="min-width:90px;">
            <i class="bi bi-check-circle fs-5"></i> فعال
        </button>
    @endif
@endif
