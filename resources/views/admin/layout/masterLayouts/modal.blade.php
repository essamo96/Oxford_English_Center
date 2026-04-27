<div class="modal fade" tabindex="-1" id="confirm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title text-danger">تحذير!</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body">
                <p><strong>هل أنت متأكد من حذف البيانات بشكل نهائي؟</strong></p>
                <p class="text-danger"><strong>ملاحظة:</strong> لا يمكن إسترجاع البيانات.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">لا</button>
                <a href="javascript:void(0)" class="btn btn-danger delete" data-bs-dismiss="modal">نعم</a>
                <input type="hidden" id="delete_id">
            </div>
        </div>
    </div>
</div>
