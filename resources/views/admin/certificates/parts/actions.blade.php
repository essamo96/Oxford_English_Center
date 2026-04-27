<div class="d-flex justify-content-center">
    <a href="{{ route('students.groups.pdf', ['id' => Crypt::encrypt($id)]) }}" class="btn btn-sm btn-light-danger" title="تحميل PDF">
        <i class="bi bi-file-earmark-pdf fs-4 me-1"></i> PDF
    </a>
</div>
