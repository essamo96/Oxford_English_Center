<div class="d-flex flex-wrap flex-sm-nowrap mb-3">
    <!--begin: Pic-->
    <div class="me-7 mb-4">
        <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
            <img src="{{ $info->image ? asset($info->image) : url('assets/oxford/img/logo.png') }}" alt="image" style="object-fit: cover;">
            <div class="position-absolute translate-middle bottom-0 start-100 mb-6 {{ $info->status == 1 ? 'bg-success' : 'bg-danger' }} rounded-circle border border-4 border-body h-20px w-20px"></div>
        </div>
    </div>
    <!--end::Pic-->

    <!--begin::Info-->
    <div class="flex-grow-1">
        <!--begin::Title-->
        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
            <!--begin::User-->
            <div class="d-flex flex-column">
                <!--begin::Name-->
                <div class="d-flex align-items-center mb-2">
                    <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $info->name }}</a>
                    @if($info->status == 1)
                        <a href="#">
                            <i class="ki-duotone ki-verify fs-1 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    @endif
                </div>
                <!--end::Name-->

                <!--begin::Info-->
                <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                    <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                        <i class="ki-duotone ki-profile-circle fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>{{ $info->job ?: 'Student' }}
                    </a>
                    <a href="tel:{{ $info->mobile }}" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                        <i class="ki-duotone ki-phone fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>{{ $info->mobile }}
                    </a>
                    <a href="mailto:{{ $info->email }}" class="d-flex align-items-center text-gray-400 text-hover-primary mb-2">
                        <i class="ki-duotone ki-sms fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>{{ $info->email ?: 'No Email' }}
                    </a>
                </div>
                <!--end::Info-->
            </div>
            <!--end::User-->

            <!--begin::Actions-->
            <div class="d-flex my-4">
                <a href="{{ route('students.edit', Crypt::encrypt($info->id)) }}" class="btn btn-sm btn-light-primary me-2">
                    <i class="ki-duotone ki-pencil fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>Edit Profile
                </a>
                <div class="me-0">
                    <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                        <i class="ki-solid ki-dots-horizontal fs-2x"></i>
                    </button>
                    <!-- Menu placeholders can be added here if needed -->
                </div>
            </div>
            <!--end::Actions-->
        </div>
        <!--end::Title-->

        <!--begin::Stats-->
        <div class="d-flex flex-wrap flex-stack">
            <!--begin::Wrapper-->
            <div class="d-flex flex-column flex-grow-1 pe-8">
                <!--begin::Stats-->
                <div class="d-flex flex-wrap">
                    <!--begin::Stat-->
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-calendar-8 fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i>
                            <div class="fs-4 fw-bold">{{ $info->join_date ? \Carbon\Carbon::parse($info->join_date)->format('Y-m-d') : 'N/A' }}</div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-400">Join Date</div>
                    </div>
                    <!--end::Stat-->

                    <!--begin::Stat-->
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-medal-star fs-3 text-warning me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            <div class="fs-4 fw-bold">{{ $info->exam_degree ?: '0' }}%</div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-400">Placement Score</div>
                    </div>
                    <!--end::Stat-->

                    <!--begin::Stat-->
                    <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-element-11 fs-3 text-primary me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                            <div class="fs-4 fw-bold">{{ $info->gropes()->count() }}</div>
                        </div>
                        <div class="fw-semibold fs-6 text-gray-400">Groups</div>
                    </div>
                    <!--end::Stat-->
                </div>
                <!--end::Stats-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Stats-->
    </div>
    <!--end::Info-->
</div>

@if($info->note)
<div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
    <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>
    <div class="d-flex flex-stack flex-grow-1 ">
        <div class=" fw-semibold">
            <h4 class="text-gray-900 fw-bold">Internal Notes</h4>
            <div class="fs-6 text-gray-700">{{ $info->note }}</div>
        </div>
    </div>
</div>
@endif