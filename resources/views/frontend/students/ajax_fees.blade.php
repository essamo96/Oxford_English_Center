<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Student Fees</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="table-responsive">
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @if(sizeof($fees)>0)
                    @foreach($fees as $item)
                    <tr>
                        <th>{{ $item->student_fee_paid}}{{ $item->student_paid_type?' NIS':' $'}}</th>
                        <td>{{ $item->student_paid_type?'Book':'Course'}}</td>
                        <td>{{ $item->created_at}}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <th colspan="3"> No Fees Paid</th>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>