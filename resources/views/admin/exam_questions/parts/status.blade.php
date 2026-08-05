@if($status != 'active')
    <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-danger @can('admin.exam_questions.status') status @endcan" style="min-width:90px;">
        <i class="bi bi-x-circle fs-5"></i> غير فعال
    </button>
@else
    <button data-href="{{ Crypt::encrypt($id) }}" class="btn btn-sm btn-light-success @can('admin.exam_questions.status') status @endcan" style="min-width:90px;">
        <i class="bi bi-check-circle fs-5"></i> فعال
    </button>
@endif
