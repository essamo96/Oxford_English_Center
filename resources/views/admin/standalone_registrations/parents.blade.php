@extends('admin.layout.master')

@section('title', 'أولياء الأمور')

@section('css')
<style>
    .filter-label { font-weight: 600; color: var(--bs-gray-700); }
</style>
@stop

@section('page-breadcrumb')
<li class="breadcrumb-item text-muted">
    <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
</li>
<li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
<li class="breadcrumb-item text-dark">Parents</li>
@stop

@section('page-content')
<div class="card shadow-sm mb-5">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-filter fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span></i> فلاتر البحث
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="filter-label">Program</label>
                <select name="program_id" class="form-select form-select-solid" data-control="select2" data-placeholder="اختر البرنامج">
                    <option value=""></option>
                    @foreach($programs ?? [] as $program)
                        <option value="{{ $program->id }}">{{ $program->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Gender</label>
                <select name="gender" class="form-select form-select-solid">
                    <option value="">All</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Branch</label>
                <select name="branch" class="form-select form-select-solid">
                    <option value="">All</option>
                    @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->name_en }}">{{ $branch->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Status</label>
                <select name="is_invoiced" class="form-select form-select-solid">
                    <option value="">All</option>
                    <option value="1">Invoiced</option>
                    <option value="0">Not Invoiced</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-solid">
            </div>
            <div class="col-md-3">
                <label class="filter-label">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-solid">
            </div>
            <div class="col-md-12 text-end mt-4">
                <button type="button" id="btn-filter" class="btn btn-primary"><i class="ki-duotone ki-magnifier fs-2"><span class="path1"></span><span class="path2"></span></i> بحث</button>
                <button type="reset" id="btn-reset" class="btn btn-light"><i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i> مسح</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header border-0 pt-6">
        <div class="card-title">
            <span class="card-label fw-bold fs-3 mb-1 text-info">
                <i class="ki-duotone ki-profile-user fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i> Parents Information
            </span>
        </div>
    </div>
    <div class="card-body py-4">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center w-100" id="parents_table">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0 text-center">
                        <th class="w-30px text-center"> # </th>
                        <th class="min-w-100px text-center">Student Name (EN)</th>
                        <th class="min-w-100px text-center">الطالب (عربي)</th>
                        <th class="min-w-100px text-center">Program</th>
                        <th class="min-w-125px text-center">Parents Info</th>
                        <th class="min-w-80px text-center">DOB</th>
                        <th class="min-w-80px text-center">Status</th>
                        <th class="min-w-100px text-center">Registered At</th>
                        <th class="text-center min-w-100px pe-4"> Actions </th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold text-center">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-800px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Details <span class="text-muted fs-6 ms-2">(تفاصيل التسجيل)</span></h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-2x"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body" id="detailsContent">
                <div class="text-center py-10">
                    <span class="spinner-border text-primary" role="status"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('assets/admin/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        var table = $('#parents_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('combo_parents.view') }}",
                data: function (d) {
                    d.program_id = $('select[name="program_id"]').val();
                    d.gender = $('select[name="gender"]').val();
                    d.branch = $('select[name="branch"]').val();
                    d.is_invoiced = $('select[name="is_invoiced"]').val();
                    d.date_from = $('input[name="date_from"]').val();
                    d.date_to = $('input[name="date_to"]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id', orderable: false, searchable: false, render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }},
                {data: 'full_name_en', name: 'full_name_en'},
                {data: 'full_name_ar', name: 'full_name_ar'},
                {data: 'program_title', name: 'program.title', orderable: false, searchable: false},
                {data: 'parents_list', name: 'parents_list', orderable: false, searchable: false},
                {data: 'dob', name: 'dob'},
                {data: 'is_read', name: 'is_read'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
            },
            order: [[7, 'desc']]
        });

        $('#btn-filter').click(function(){
            table.draw();
        });

        $('#btn-reset').click(function(){
            $('#filterForm')[0].reset();
            $('select[name="program_id"]').val(null).trigger('change');
            table.draw();
        });

        // Delete Logic
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استرجاع هذا التسجيل!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("admin/new-registrations/delete") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('تم الحذف!', response.message, 'success');
                                table.draw();
                            }
                        }
                    });
                }
            })
        });

        $(document).on('click', '.view-details', function() {
            var id = $(this).data('id');
            viewDetails(id);
        });
    });

    function viewDetails(id) {
        $('#detailsContent').html('<div class="text-center py-10"><span class="spinner-border text-primary" role="status"></span></div>');
        $('#detailsModal').modal('show');

        $.ajax({
            url: '{{ url("admin/new-registrations/show") }}/' + id,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    var data = response.data;
                    var parents = response.parents;
                    
                    var healthDetailsHtml = data.health_issues && data.health_issues_details 
                        ? `<p class="mt-2 p-3 bg-light-danger rounded text-danger" style="border: 1px solid #f1bcbc;"><strong>تفاصيل الإعاقة/المشكلة الصحية:</strong><br>${data.health_issues_details}</p>` 
                        : '';

                    var html = `
                        <div class="row g-5">
                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-user text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Full Name (En):</strong> ${data.full_name_en}</p>
                                <p class="mb-2"><i class="ki-duotone ki-text-align-right text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i><strong>الاسم (عربي):</strong> ${data.full_name_ar}</p>
                                <p class="mb-2"><i class="ki-duotone ki-phone text-success fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Phone:</strong> <span dir="ltr">${data.phone}</span></p>
                                <p class="mb-2"><i class="ki-duotone ki-sms text-warning fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Email:</strong> ${data.email}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-calendar-8 text-info fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i><strong>DOB:</strong> <span dir="ltr">${data.dob}</span></p>
                                <p class="mb-2"><i class="ki-duotone ki-abstract-26 text-dark fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Gender:</strong> ${data.gender}</p>
                                <p class="mb-2"><i class="ki-duotone ki-shop text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i><strong>Branch:</strong> ${data.branch}</p>
                                <p class="mb-2"><i class="ki-duotone ki-geolocation text-danger fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Address:</strong> ${data.address}</p>
                            </div>
                            
                            <div class="col-12"><hr class="my-3"></div>

                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-book text-success fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i><strong>Program:</strong> ${data.program ? data.program.title : '-'}</p>
                                <p class="mb-2"><i class="ki-duotone ki-abstract-14 text-warning fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Program Type:</strong> ${data.program_type === 'kids' ? 'Kids (صغار)' : (data.program_type === 'adults' ? 'Adults (كبار)' : '-')}</p>
                                <p class="mb-2"><i class="ki-duotone ki-briefcase text-info fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Major/Profession:</strong> ${data.major_profession}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><i class="ki-duotone ki-pulse text-danger fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Health Issues:</strong> ${data.health_issues ? '<span class="text-danger fw-bold">Yes</span>' : 'No'}</p>
                                ${healthDetailsHtml}
                                <p class="mb-2 mt-2"><i class="ki-duotone ki-pencil text-primary fs-3 me-2"><span class="path1"></span><span class="path2"></span></i><strong>Placement Test:</strong> ${data.placement_test ? '<span class="text-success fw-bold">Requested</span>' : 'No'}</p>
                                ${data.placement_test ? `<p class="mb-2"><i class="ki-duotone ki-calendar-add text-dark fs-3 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span></i><strong>Test Date:</strong> <span dir="ltr">${data.placement_test_date || '-'}</span></p>` : ''}
                            </div>
                        </div>
                    `;

                    if(parents && parents.length > 0) {
                        html += `
                            <div class="col-12 mt-4">
                                <h5 class="text-primary mb-3"><i class="ki-duotone ki-profile-user text-primary fs-2 me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>Parent Information (بيانات ولي الأمر)</h5>
                                <div class="bg-light p-4 rounded border border-light-primary">
                                    <p class="mb-2"><strong>Name:</strong> ${parents[0].parent_name}</p>
                                    <p class="mb-2"><strong>Phone:</strong> <span dir="ltr">${parents[0].parent_phone}</span></p>
                                    <p class="mb-0"><strong>Email:</strong> ${parents[0].parent_email || '-'}</p>
                                </div>
                            </div>
                        `;
                    }

                    $('#detailsContent').html(html);
                    $('#parents_table').DataTable().ajax.reload(null, false);
                }
            },
            error: function() {
                $('#detailsContent').html('<div class="alert alert-danger">Error loading details.</div>');
            }
        });
    }
</script>
@stop
