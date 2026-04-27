@extends('admin.layout.master')
@section('title')
    {{ $current_route->{'name_' . trans('app.lang')} }}
@stop
@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ url('/') }}" class="text-muted text-hover-primary">@lang('app.home')</a>
    </li>
    <li class="breadcrumb-item text-muted">- {{ $current_route->{'name_' . trans('app.lang')} }}</li>
@stop

@section('page-content')
    <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
        {{-- // if can admin.dashboard.finance or admin.dashboard.hr --}}
        @php
            $user = Auth::guard('admin')->user();
        @endphp
        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            @if ($user && ($user->can('admin.dashboard.finance') || $user->can('admin.dashboard.hr')))
            {{-- عدد موظفين داشبورد الموارد البشرية --}}
                <a href="{{ route('employee.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $employees->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_employees')</span>
                            </div>
                        </div>

                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-people  text-success fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.active_employees')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $employees->where('status', 1)->whereNull('deleted_at')->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.unactive_employees')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $employees->where('status', 0)->whereNull('deleted_at')->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 me-3" style="background-color: #fc0101"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.disabled_employees')</div>
                                    <div class=" fw-bolder text-gray-700 text-xxl-end">
                                        {{ $employees->where('deleted_at', '!=', null)->count() }}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </a>

                                <a href="{{ route('financial_loan.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Financial_Loan->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_loan')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-cash-coin  text-info fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.accepted_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('loan_status', 1)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.refused_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('loan_status', 3)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.wait_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('loan_status', 0)->count() }}</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
             
            @elseif($user->can('admin.dashboard.emp'))
                {{-- عدد الاجازات لموظف واحد --}}

                <a href="{{ route('holidays.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Holiday->where('employee_id', Auth::guard('admin')->user()->employee_id)->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_holdays')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-bag-heart-fill  text-success fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.accepted_total_holdays')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Holiday->where('employee_id', Auth::guard('admin')->user()->employee_id)->where('status', 1)->count() }}
                                    </div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.refused_total_holdays')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Holiday->where('employee_id', Auth::guard('admin')->user()->employee_id)->where('status', 3)->count() }}
                                    </div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.wait_total_holdays')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Holiday->where('employee_id', Auth::guard('admin')->user()->employee_id)->where('status', 0)->count() }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>

                {{-- طلبات السلف المفتوحة لموظف واحد --}}

                <a href="{{ route('financial_loan.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Financial_Loan->where('employee_id', Auth::guard('admin')->user()->employee_id)->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_loan')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-cash-coin  text-info fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.accepted_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('loan_status', 1)->where('employee_id', Auth::guard('admin')->user()->employee_id)->count() }}
                                    </div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.refused_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('loan_status', 3)->where('employee_id', Auth::guard('admin')->user()->employee_id)->count() }}
                                    </div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.wait_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('loan_status', 0)->where('employee_id', Auth::guard('admin')->user()->employee_id)->count() }}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            @endif
        </div>

        <div class="col-md-6 col-lg-6 col-xl-6 col-xxl-3 mb-md-5 mb-xl-10">
            <!--begin::Card widget 17-->
            @if ($user && $user->can('admin.dashboard.hr'))
                {{-- اجمالي البطاقات لكل الموظفين --}}
                <a href="{{ route('Notice_expiratio_card.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Attachments->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.Attachments_ended')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-paperclip  text-dark fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                            </div>
                        </div>
                    </div>
                </a>
                {{-- طلبات الاجازات بشكل عام --}}
                <a href="{{ route('holidays.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Holiday->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_holdays')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-bag-heart-fill fs-1  text-info fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.accepted_total_holdays')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Holiday->where('status', 1)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.refused_total_holdays')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Holiday->where('status', 3)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.wait_total_holdays')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Holiday->where('status', 0)->count() }}</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>

   {{-- الموارد البشرية عدد اقسام --}}
                <a href="{{ route('sections.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <!--begin::Title-->
                            <div class="card-title d-flex flex-column">
                                <!--begin::Info-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Currency-->

                                    <!--end::Currency-->

                                    <!--begin::Amount-->
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Section->count() }}</span>
                                    <!--end::Amount-->

                                    <!--begin::Badge-->
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                    <!--end::Badge-->
                                </div>
                                <!--end::Info-->

                                <!--begin::Subtitle-->
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_sections')</span>
                                <!--end::Subtitle-->
                            </div>
                            <!--end::Title-->
                        </div>

                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <!--begin::Chart-->
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70" data-kt-line="11">
                                    <i class="bi bi-columns-gap  text-warning fs-4x"></i>
                                </div>
                            </div>
                            <!--end::Chart-->

                            <!--begin::Labels-->
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">

                            </div>
                            <!--end::Labels-->
                        </div>
                    </div>
                </a>
            @elseif($user && $user->can('admin.dashboard.finance'))
                {{-- عدد استمارات الراتب تحت المراجعة --}}

                <a href="{{ route('salary.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Salary->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_forms')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-bank  text-danger fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.done_forms')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Salary->where('status', 1)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.under_revison')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Salary->where('status', 0)->count() }}</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>

                {{-- عدد السلف المقدمة --}}

                <a href="{{ route('financial_loan.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Financial_Loan->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.total_loan')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-cash-coin  text-info fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                                <div class="d-flex fw-semibold align-items-center">
                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.accepted_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('status', 1)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.refused_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('status', 3)->count() }}</div>
                                </div>
                                <div class="d-flex fw-semibold align-items-center my-3">
                                    <div class="bullet w-8px h-3px rounded-2 bg-primary me-3"></div>
                                    <div class="text-gray-500 flex-grow-1 me-4">@lang('app.wait_total_loan')</div>
                                    <div class="fw-bolder text-gray-700 text-xxl-end">
                                        {{ $Financial_Loan->where('status', 0)->count() }}</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </a>
            @elseif($user && $user->can('admin.dashboard.emp'))
                {{--  عدد البطاقات بحاجة لتجديد لموظف واحد --}}

                <a href="{{ route('Notice_expiratio_card.view') }}">
                    <div class="card card-flush h-md-50 mb-5 mb-xl-10">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <div class="d-flex align-items-center">
                                    <span
                                        class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $Attachments->where('employee_id', Auth::guard('admin')->user()->employee_id)->count() }}</span>
                                    <span class="badge badge-light-success fs-base">
                                        <i class="ki-duotone ki-arrow-up fs-5 text-success ms-n1"><span
                                                class="path1"></span><span class="path2"></span></i>
                                        total
                                    </span>
                                </div>
                                <span class="text-gray-400 pt-1 fw-semibold fs-6">@lang('app.Attachments_ended')</span>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">
                            <div class="d-flex flex-center me-5 pt-2">
                                <div id="kt_card_widget_17_chart" style="min-width: 70px; min-height: 70px"
                                    data-kt-size="70">
                                    <i class="bi bi-paperclip  text-dark fs-4x"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column content-justify-center flex-row-fluid">
                            </div>
                        </div>
                    </div>
                </a>

                {{-- روابط مهمة --}}

                <div class="card card-flush h-lg-50">
                    <div class="card-header pt-5">
                        <h3 class="card-title text-gray-800 fw-bold">@lang('app.important_links')</h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="d-flex flex-stack">
                            <a href="{{ route('view_vacations.view') }}"
                                class="text-primary fw-semibold fs-6 me-2">@lang('app.holdays_this_week')</a>
                            <span type="button"
                                class="btn btn-icon btn-sm h-auto btn-color-gray-400 btn-active-color-primary justify-content-end">
                                <i class="bi bi-arrow-up-right-square fs-2"></i> </span>
                        </div>
                        <div class="separator separator-dashed my-3"></div>
                        <div class="d-flex flex-stack">
                            <a href="{{ route('weekends.view') }}" class="text-primary fw-semibold fs-6 me-2">
                                @lang('app.weekends')</a>
                            <span type="button"
                                class="btn btn-icon btn-sm h-auto btn-color-gray-400 btn-active-color-primary justify-content-end">
                                <i class="bi bi-arrow-up-right-square fs-2"></i> </span>
                        </div>
                        <div class="separator separator-dashed my-3"></div>
                        <div class="d-flex flex-stack">
                            <a href="{{ route('holidays.view') }}"
                                class="text-primary fw-semibold fs-6 me-2">@lang('app.ask_holdays')
                            </a>
                            <button type="button"
                                class="btn btn-icon btn-sm h-auto btn-color-gray-400 btn-active-color-primary justify-content-end">
                                <i class="bi bi-arrow-up-right-square fs-2"></i> </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-xxl-6">
            @if ($Circulars == null)
                <!--begin::Engage widget 10-->
                <div class="card card-flush h-md-100">
                    <!--begin::Body-->
                    <div class="card-body d-flex flex-column justify-content-between mt-9 bgi-no-repeat bgi-size-cover bgi-position-x-center pb-0"
                        style="background-position: 100% 50%; background-image:url('/metronic8/demo1/assets/media/stock/900x600/42.png')">
                        <!--begin::Wrapper-->
                        <div class="mb-10">
                            <!--begin::Title-->
                            <div class="fs-2hx fw-bold text-gray-800 text-center mb-13">
                                <span class="me-2">
                                    @lang('app.no_role')
                                    <span class="position-relative d-inline-block text-danger">
                                        {{-- <a href="/metronic8/demo1/../demo1/pages/user-profile/overview.html"
                                        class="text-danger opacity-75-hover"> ? what is new </a> --}}

                                        <!--begin::Separator-->
                                        <span
                                            class="position-absolute opacity-15 bottom-0 start-0 border-4 border-danger border-bottom w-100"></span>
                                        <!--end::Separator-->
                                    </span>
                                </span>
                            </div>
                        </div>
                        <!--begin::Wrapper-->

                        <!--begin::Illustration-->
                        <img class="mx-auto h-150px h-lg-200px  theme-light-show" src="{{ url('assets/images/5.png') }}"
                            alt="">
                        <img class="mx-auto h-150px h-lg-200px  theme-dark-show" src="{{ url('assets/images/5.png') }}"
                            alt="">
                        <!--end::Illustration-->
                    </div>
                    <!--end::Body-->
                </div>
                <!--end::Engage widget 10-->
            @else
                <div class="card card-flush h-md-100">
                    <!--begin::Body-->
                    <div class="card-body d-flex flex-column justify-content-between mt-2 bgi-no-repeat bgi-size-cover bgi-position-x-center pb-0"
                        style="background-position: 100% 50%; background-image:url('assets/images/42.png')">
                        <!--begin::Wrapper-->
                        <div class="">
                            <!--begin::Title-->
                            <div class="fs-2hx fw-bold text-gray-800 text-center mb-5">
                                <span class="me-2">
                                    <div class="btn btn-sm btn-icon mw-20px btn-active-color-danger me-5">
                                        <i class="ki-duotone ki-minus-square toggle-on text-danger fs-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <i class="bi bi-stars toggle-off fs-1"> </i>
                                    </div>
                                    <span class="position-relative d-inline-block text-danger">
                                        <span class="text-danger opacity-75-hover"> @lang('app.list_role')</span>

                                        <!--begin::Separator-->
                                        <span
                                            class="position-absolute opacity-15 bottom-0 start-0 border-4 border-danger border-bottom w-100"></span>
                                        <!--end::Separator-->
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="mb-2">

                            <div class="fs-4 ms-1  show" style="">
                                @foreach ($Circulars as $Circular)
                                    <div class="mb-4">
                                        <!--begin::Item-->
                                        <div class="d-flex align-items-center ps-10 mb-n1">
                                            <!--begin::Bullet-->
                                            <span class="bi bi-check2-square btn-active-color-danger me-3"></span>
                                            <!--end::Bullet-->
                                            <!--begin::Label-->
                                            <div class="text-gray-700 fw-bold  fs-6">
                                                <?= $Circular->{'title_' . trans('app.lang')} ?></div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <!--begin::Wrapper-->

                        <!--begin::Illustration-->
                        <img class="mx-auto h-150px h-lg-200px  theme-light-show" src="{{ url('assets/images/10.png') }}"
                            alt="">
                        <img class="mx-auto h-150px h-lg-200px  theme-dark-show"
                            src="{{ url('assets/images/10-dark.png') }}" alt="">
                        <!--end::Illustration-->
                    </div>
                    <!--end::Body-->
                </div>
            @endif
        </div>

    </div>
    <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">

        {{-- <div class="col-xxl-6">
            <!--begin::Engage widget 10-->
            <div class="card card-flush h-md-100">
                <!--begin::Body-->
                <div class="card-body d-flex flex-column justify-content-between mt-9 bgi-no-repeat bgi-size-cover bgi-position-x-center pb-0"
                    style="background-position: 100% 50%; background-image:url('/metronic8/demo1/assets/media/stock/900x600/42.png')">
                    <!--begin::Engage widget 10-->
                    <h3 class="text-white fw-bolder fs-2qx pb-5">الموظفين الجدد</h3>
                    <div class="card-body card-flush h-md-100">
                        <ul class="nav nav-pills d-flex justify-content-between nav-pills-custom gap-3 mb-6"
                            role="tablist">

                            <!--begin::Item-->
                            <li class="nav-item mb-3 me-0" role="presentation">
                                <!--begin::Nav link-->
                                <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg show active"
                                    data-bs-toggle="pill" href="#kt_pos_food_content_1"
                                    style="width: 138px;height: 180px" aria-selected="true" role="tab">
                                    <!--begin::Icon-->
                                    <div class="nav-icon mb-3">
                                        <!--begin::Food icon-->
                                        <img src="/metronic8/demo1/assets/media/svg/food-icons/spaghetti.svg"
                                            class="w-50px" alt="">
                                        <!--end::Food icon-->
                                    </div>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="">
                                        <span class="text-gray-800 fw-bold fs-2 d-block">Lunch</span>
                                        <span class="text-gray-400 fw-semibold fs-7">8 Options</span>
                                    </div>
                                    <!--end::Info-->
                                </a>
                                <!--end::Nav link-->
                            </li>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <li class="nav-item mb-3 me-0" role="presentation">
                                <!--begin::Nav link-->
                                <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg "
                                    data-bs-toggle="pill" href="#kt_pos_food_content_2"
                                    style="width: 138px;height: 180px" aria-selected="false" tabindex="-1"
                                    role="tab">
                                    <!--begin::Icon-->
                                    <div class="nav-icon mb-3">
                                        <!--begin::Food icon-->
                                        <img src="/metronic8/demo1/assets/media/svg/food-icons/salad.svg" class="w-50px"
                                            alt="">
                                        <!--end::Food icon-->
                                    </div>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="">
                                        <span class="text-gray-800 fw-bold fs-2 d-block">Salad</span>
                                        <span class="text-gray-400 fw-semibold fs-7">14 Salads</span>
                                    </div>
                                    <!--end::Info-->
                                </a>
                                <!--end::Nav link-->
                            </li>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <li class="nav-item mb-3 me-0" role="presentation">
                                <!--begin::Nav link-->
                                <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg "
                                    data-bs-toggle="pill" href="#kt_pos_food_content_3"
                                    style="width: 138px;height: 180px" aria-selected="false" tabindex="-1"
                                    role="tab">
                                    <!--begin::Icon-->
                                    <div class="nav-icon mb-3">
                                        <!--begin::Food icon-->
                                        <img src="/metronic8/demo1/assets/media/svg/food-icons/cheeseburger.svg"
                                            class="w-50px" alt="">
                                        <!--end::Food icon-->
                                    </div>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="">
                                        <span class="text-gray-800 fw-bold fs-2 d-block">Burger</span>
                                        <span class="text-gray-400 fw-semibold fs-7">5 Burgers</span>
                                    </div>
                                    <!--end::Info-->
                                </a>
                                <!--end::Nav link-->
                            </li>
                            <!--begin::Item-->
                            <li class="nav-item mb-3 me-0" role="presentation">
                                <!--begin::Nav link-->
                                <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg "
                                    data-bs-toggle="pill" href="#kt_pos_food_content_3"
                                    style="width: 138px;height: 180px" aria-selected="false" tabindex="-1"
                                    role="tab">
                                    <!--begin::Icon-->
                                    <div class="nav-icon mb-3">
                                        <!--begin::Food icon-->
                                        <img src="/metronic8/demo1/assets/media/svg/food-icons/cheeseburger.svg"
                                            class="w-50px" alt="">
                                        <!--end::Food icon-->
                                    </div>
                                    <!--end::Icon-->

                                    <!--begin::Info-->
                                    <div class="">
                                        <span class="text-gray-800 fw-bold fs-2 d-block">Burger</span>
                                        <span class="text-gray-400 fw-semibold fs-7">5 Burgers</span>
                                    </div>
                                    <!--end::Info-->
                                </a>
                                <!--end::Nav link-->
                            </li>


                        </ul>
                    </div>
                    <!--end::Engage widget 10-->
                </div>
            </div>
        </div> --}}

    </div>
@stop
