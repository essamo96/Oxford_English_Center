<div class="d-flex justify-content-center gap-2">
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-primary btn-sm view-answers" title="عرض جميع الإجابات">
        <i class="bi bi-eye fs-4"></i>
    </a>
    <a href="javascript:;" data-href="{{ Crypt::encrypt($id) }}" class="btn btn-icon btn-light-danger btn-sm view-wrong-answers" title="عرض الأسئلة الخاطئة فقط">
        <i class="bi bi-x-circle fs-4"></i>
    </a>
</div>
