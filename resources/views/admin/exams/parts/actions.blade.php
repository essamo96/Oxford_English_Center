@php
    $permPrefix = $category === 'placement' ? 'exam_placement_tests' : 'group_exams';
    $editRoute = $category === 'placement' ? 'exam_placement_tests.edit' : 'group_exams.edit';
@endphp
<div class="d-flex justify-content-center gap-2">
    @can('admin.' . $permPrefix . '.edit')
    <a href="{{ route($editRoute, ['id' => Crypt::encrypt($id)]) }}" class="btn btn-icon btn-light-primary btn-sm" title="تعديل">
        <i class="bi bi-pencil-square fs-4"></i>
    </a>
    @endcan
    @can('admin.' . $permPrefix . '.delete')
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirm" title="حذف">
        <i class="bi bi-trash fs-4"></i>
    </a>
    @endcan
</div>
