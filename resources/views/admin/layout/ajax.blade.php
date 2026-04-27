<div id="confirm" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title text-danger"><strong>تحذير! </strong></h4>
            </div>
            <div class="modal-body">
                <p>
                    <Strong>هل أنت متأكد من حذف البيانات بشكل نهائي؟</Strong>
                </p>
                <p class="text-danger"><Strong>ملاحظة:</Strong> لا يمكن إسترجاع البيانات.</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn default" data-dismiss="modal" aria-hidden="true">لا</a>
                <a data-href="" data-dismiss="modal" aria-hidden="true" class="btn red delete">نعم</a>
                <input type="hidden" id="delete_id">
            </div>
        </div>
    </div>
</div>

{{-- start delete Section Modal --}}

<div class="modal" id="post_select">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">حذف القسم</h6><button aria-label="Close" class="close"
                    data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="sections/destroy" method="post">
                {{ method_field('delete') }}
                {{ csrf_field() }}
                <div class="modal-body">
                    <p>هل انت متاكد من عملية الضافة ؟</p><br>
                    <input type="numper" name="id" id="id" value="">
                    <input class="form-control" name="title_ar" id="title_ar" type="text"
                        readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                    <button type="submit" class="btn btn-danger">تاكيد</button>
                </div>
        </div>
        </form>
    </div>
</div>