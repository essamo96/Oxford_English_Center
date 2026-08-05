@php
    $permPrefix = $category === 'placement' ? 'exam_placement_tests' : 'group_exams';
    $editRoute = $category === 'placement' ? 'exam_placement_tests.edit' : 'group_exams.edit';
@endphp
<div class="d-flex justify-content-center gap-2">
    @can('admin.' . $permPrefix . '.view')
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-info btn-sm preview-exam" title="معاينة الامتحان كما سيراه الطالب">
        <i class="bi bi-eye fs-4"></i>
    </a>
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-warning btn-sm exam-questions" title="عرض الأسئلة">
        <i class="bi bi-list-check fs-4"></i>
    </a>
    @endcan
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
