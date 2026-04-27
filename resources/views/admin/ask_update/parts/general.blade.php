@if ($x == 1)

    <div class="form-check form-check-custom form-check-info form-check-solid form-check-sm my-5">
        <input class="form-check-input checkboxes" name="mobile[{{ $id}}]" value="{{$mobile}}" data-mob="{{$mobile}}"  data-email="{{$email}}"  data-id="id"  />
    </div>
    {{-- @else
    <i class="bi bi-check-circle-fill text-success fs-2 my-3 "></i>
    @endif
@elseif ($x == 2)
    <a href=""
        class="btn btn-outline  btn-outline-dashed btn-outline-success btn-active-light-success btn-sm">{{ $employee }}</a>
@elseif ($x == 3)
    <a href=""
        class="btn btn-outline  btn-outline-dashed btn-outline-warning btn-active-light-warning btn-sm">{{ $loan_type_id }}</a>
@elseif ($x == 5)
    <span class="badge badge-light-success fw-bold px-4 py-3">{{ $installment_value }}</span>
@elseif ($x == 6)
    <span class="badge badge-light-success fw-bold px-4 py-3">{{ $month_no }} | @lang('app.month')</span>
@elseif ($x == 7)
    <span class="badge badge-light-info fw-bold px-4 py-3">{{ $payment_start_date }} </span>
@elseif ($x == 4)
    @if ($status == 0)
        <span class="badge badge-light-danger fs-base px-2">
            <i class="bi bi-clock fs-5 text-danger ms-n1"></i>     @lang('app.unpaid')     </span>

    @elseif($status == 2)
    
            <span class="badge badge-light-warning fs-base">
            <i class="bi bi-check fs-5 text-warning ms-n1"></i>  @lang('app.under_review')  </span>
    @elseif($status == 1 && $cash == 1)
    
            <span class="badge badge-light-primary fs-base">
            <i class="ki-duotone ki-check fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>  @lang('app.paycash')  </span>
            @else
            <span class="badge badge-light-success fs-base">
            <i class="bi bi-check fs-5 text-success ms-n1"></i>  @lang('app.paid')  </span>
            @endif --}}

@endif
