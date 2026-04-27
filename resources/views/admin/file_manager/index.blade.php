<!DOCTYPE html>
<html lang="ar" direction="rtl" dir="rtl" style="direction: rtl;">
<head>
    <meta charset="utf-8" />
    <title>مدير الملفات | Oxford File Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet" />
    
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/plugins.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.rtl.css') }}" rel="stylesheet" type="text/css" />

    <script>
        var themeMode = "light";
        if (localStorage.getItem("kt_theme_mode_value") !== null) {
            themeMode = localStorage.getItem("kt_theme_mode_value");
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    </script>

    <style>
        body { background: #f5f8fa; font-family: 'Cairo', 'Inter', sans-serif; }
        .table.align-middle td { vertical-align: middle !important; }
        .file-item-name:hover { color: var(--bs-primary) !important; cursor: pointer; }
        .breadcrumb-item + .breadcrumb-item::before { content: "\f12d"; font-family: "Font Awesome 5 Free"; font-weight: 900; }
        .table-row-hover:hover { background-color: #f1faff !important; transition: all 0.2s ease; transform: translateX(-5px); }
        .icon-wrapper { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
        /* Custom Folder Icon Colors matching screenshot */
        .ki-folder.text-primary { color: #009ef7 !important; }
        .badge-light-primary-custom { background-color: #f1faff; color: #009ef7; border: 1px dashed #009ef7; }
        [data-bs-theme="dark"] .badge-light-primary-custom { background-color: #212e48; color: #009ef7; }
        .file-name-text { font-size: 1.05rem; font-weight: 600 !important; }
        .table-row-hover { border-bottom: 1px solid #eff2f5; transition: transform 0.2s ease; }
        .card { box-shadow: 0 0.1rem 1rem 0.25rem rgba(0, 0, 0, 0.05); }
        .illustration-card { position: relative; overflow: hidden; }
        .illustration-bg { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 400px; 
            height: 100%; 
            background-image: url('{{ asset('assets/media/illustrations/sketchy-1/4.png') }}'); 
            background-size: contain; 
            background-repeat: no-repeat; 
            background-position: left center; 
            transform: scaleX(-1); 
            opacity: 0.8;
            z-index: 0;
            pointer-events: none;
        }
        .illustration-card .card-header, .illustration-card .card-body { position: relative; z-index: 1; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .table tbody tr { animation: fadeIn 0.3s ease forwards; opacity: 0; }
        .table tbody tr:nth-child(1) { animation-delay: 0.05s; }
        .table tbody tr:nth-child(2) { animation-delay: 0.1s; }
        .table tbody tr:nth-child(3) { animation-delay: 0.15s; }
        .table tbody tr:nth-child(4) { animation-delay: 0.2s; }
        .table tbody tr:nth-child(5) { animation-delay: 0.25s; }
    </style>
</head>
<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed aside-enabled aside-fixed">

<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper" style="padding: 0;">
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="container-xxl" id="kt_content_container" style="max-width: none; padding: 20px;">
                    
                    <!-- Premium Header -->
                    <div class="card card-flush pb-0 mb-10 illustration-card">
                        <div class="illustration-bg"></div>
                        <div class="card-header pt-10">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-circle me-5">
                                    <div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                        <i class="ki-duotone ki-abstract-47 fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <h2 class="mb-1">مدير الملفات</h2>
                                    <div class="text-muted fw-bold">
                                        <a href="?folder={{ $baseFolder }}" class="text-hover-info">Oxford</a>
                                        <span class="mx-3">|</span> File Manager
                                        <span class="mx-3">|</span> {{ count($directories) + count($files) }} عناصر
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex overflow-auto h-55px">
                                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-semibold flex-nowrap">
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary me-6 active" href="#">الملفات (Files)</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-active-primary me-6" href="#">الإعدادات</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Main Container Card -->
                    <div class="card card-flush">
                        <!-- Card Header: Search & Toolbar -->
                        <div class="card-header pt-8">
                            <div class="card-title">
                                <div class="d-flex align-items-center position-relative my-1">
                                    <i class="ki-duotone ki-magnifier fs-1 position-absolute ms-6"><span class="path1"></span><span class="path2"></span></i>
                                    <input type="text" id="file_manager_search" class="form-control form-control-solid w-250px ps-15" placeholder="بحث في الملفات والمجلدات" />
                                </div>
                            </div>
                            <div class="card-toolbar">
                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-flex btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_new_folder">
                                        <i class="ki-duotone ki-add-folder fs-2"><span class="path1"></span><span class="path2"></span></i> مجلد جديد
                                    </button>
                                    <button type="button" class="btn btn-flex btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_upload">
                                        <i class="ki-duotone ki-folder-up fs-2"><span class="path1"></span><span class="path2"></span></i> رفع ملفات
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body: Table & Navigation -->
                        <div class="card-body">
                            <!-- Folder Path Header Badge -->
                            <div class="d-flex flex-stack mb-5">
                                <div class="badge badge-lg badge-light-primary badge-light-primary-custom py-3 px-5">
                                    <div class="d-flex align-items-center flex-wrap">
                                        <i class="ki-duotone ki-abstract-32 fs-2 text-primary me-3"><span class="path1"></span><span class="path2"></span></i>
                                        @foreach($breadcrumbs as $index => $crumb)
                                            <a href="?type={{ $type }}&folder={{ $crumb['path'] }}&target={{ request('target') }}" class="text-primary fw-bold text-hover-dark">{{ $crumb['name'] }}</a>
                                            @if(!$loop->last)
                                            <i class="ki-duotone ki-right fs-4 text-primary mx-2"></i>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <div class="badge badge-lg badge-primary py-3 px-5">
                                    <span>{{ count($directories) + count($files) }} عناصر</span>
                                </div>
                            </div>

                            <!-- List View Table -->
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_file_manager_list">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="w-10px pe-2">
                                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                    <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_file_manager_list .form-check-input" value="1" />
                                                </div>
                                            </th>
                                            <th class="min-w-250px">الاسم</th>
                                            <th class="min-w-100px">الحجم</th>
                                            <th class="min-w-125px">آخر تعديل</th>
                                            <th class="w-125px text-end pe-7">العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        <!-- Folders -->
                                        @foreach($directories as $dir)
                                        <tr class="table-row-hover">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                </div>
                                            </td>
                                            <td data-order="{{ $dir['name'] }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-40px me-4">
                                                        <div class="symbol-label bg-light-primary">
                                                            <i class="ki-duotone ki-folder fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                        </div>
                                                    </div>
                                                    <a href="?type={{ $type }}&folder={{ $dir['path'] }}&target={{ request('target') }}" class="text-gray-800 text-hover-info fw-bold file-name-text">{{ $dir['name'] }}</a>
                                                </div>
                                            </td>
                                            <td>-</td>
                                            <td>{{ $dir['last_modified'] }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2" title="نسخ الرابط">
                                                        <i class="ki-duotone ki-fasten fs-5"><span class="path1"></span><span class="path2"></span></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <i class="ki-duotone ki-dots-square fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                    </button>
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 btn-rename" data-path="{{ $dir['path'] }}" data-type="folder" data-name="{{ $dir['name'] }}">إعادة تسمية</a>
                                                        </div>
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 text-danger btn-delete" data-path="{{ $dir['path'] }}" data-type="folder">حذف</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach

                                        <!-- Files -->
                                        @foreach($files as $file)
                                        <tr class="table-row-hover">
                                            <td>
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="checkbox" value="1" />
                                                </div>
                                            </td>
                                            <td data-order="{{ $file['name'] }}">
                                                <div class="d-flex align-items-center choose-file" data-url="{{ $file['url'] }}" style="cursor:pointer;">
                                                    <div class="symbol symbol-40px me-4">
                                                        <div class="symbol-label bg-light">
                                                            @if(in_array($file['extension'], ['jpg','jpeg','png','webp','gif']))
                                                                <img src="{{ $file['url'] }}" class="h-30px" alt="{{ $file['name'] }}" style="border-radius:4px;" />
                                                            @elseif($file['extension'] == 'pdf')
                                                                <i class="ki-duotone ki-file-down fs-2x text-danger"><span class="path1"></span><span class="path2"></span></i>
                                                            @elseif(in_array($file['extension'], ['zip','rar','7z']))
                                                                <i class="ki-duotone ki-archive fs-2x text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                                            @else
                                                                <i class="ki-duotone ki-file fs-2x text-success"><span class="path1"></span><span class="path2"></span></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <span class="text-gray-800 text-hover-info fw-bold file-name-text">{{ $file['name'] }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $file['size'] }}</td>
                                            <td>{{ $file['last_modified'] }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2 btn-copy-url" data-url="{{ $file['url'] }}" title="نسخ الرابط">
                                                        <i class="ki-duotone ki-fasten fs-5"><span class="path1"></span><span class="path2"></span></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        <i class="ki-duotone ki-dots-square fs-5"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                                                    </button>
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4" data-kt-menu="true">
                                                        <div class="menu-item px-3">
                                                            <a href="{{ $file['url'] }}" target="_blank" class="menu-link px-3">عرض الملف</a>
                                                        </div>
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 btn-rename" data-path="{{ $file['path'] }}" data-type="file" data-name="{{ $file['name'] }}">إعادة تسمية</a>
                                                        </div>
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3 text-danger btn-delete" data-path="{{ $file['path'] }}" data-type="file">حذف</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="kt_modal_upload" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">رفع ملفات</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>
            <div class="modal-body pt-10 pb-15 px-lg-17">
                <div class="dropzone" id="kt_modal_upload_dropzone">
                    <div class="dz-message needsclick">
                        <i class="ki-duotone ki-file-up fs-3hx text-primary"><span class="path1"></span><span class="path2"></span></i>
                        <div class="ms-4 text-start">
                            <h3 class="fs-5 fw-bold text-gray-900 mb-1">أفلت الملفات هنا أو انقر للرفع</h3>
                            <span class="fs-7 fw-semibold text-gray-400">يمكنك رفع حتى 10 ملفات مرة واحدة</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal New Folder -->
<div class="modal fade" id="kt_modal_new_folder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <form class="form" id="kt_modal_new_folder_form">
                <div class="modal-header">
                    <h2 class="fw-bold">إنشاء مجلد جديد</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="fv-row mb-9">
                        <label class="fs-6 fw-semibold mb-2 required">اسم المجلد</label>
                        <input type="text" class="form-control form-control-solid" name="name" id="new_folder_name" placeholder="أدخل اسم المجلد" required />
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="submit" class="btn btn-primary" id="btn_save_folder">إنشاء مجلد</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $(document).ready(function() {
        // Initialize DataTables
        var table = $('#kt_file_manager_list').DataTable({
            "info": false,
            'order': [],
            'pageLength': 100,
            "language": {
                "sProcessing": "جاري التحميل...",
                "sLengthMenu": "عرض _MENU_ سجل",
                "sZeroRecords": "لم يعثر على أية سجلات",
                "sInfo": "إظهار _START_ إلى _END_ من أصل _TOTAL_ سجل",
                "sInfoEmpty": "يعرض 0 إلى 0 من أصل 0 سجل",
                "sInfoFiltered": "(منتقاة من مجموع _MAX_ سجل)",
                "sSearch": "بحث:",
                "oPaginate": {
                    "sFirst": "الأول",
                    "sPrevious": "السابق",
                    "sNext": "التالي",
                    "sLast": "الأخير"
                }
            },
            "columnDefs": [
                { orderable: false, targets: 0 },
                { orderable: false, targets: 4 },
            ]
        });

        // Search
        $('[id="file_manager_search"]').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Dropzone
        var myDropzone = new Dropzone("#kt_modal_upload_dropzone", {
            url: "{{ route('admin.file_manager.upload') }}",
            paramName: "file",
            maxFiles: 10,
            maxFilesize: 20, 
            addRemoveLinks: true,
            sending: function(file, xhr, formData) {
                formData.append("folder", "{{ $currentFolder }}");
                formData.append("_token", "{{ csrf_token() }}");
            },
            success: function(file, response) {
                toastr.success('تم رفع الملف بنجاح');
            },
            queuecomplete: function() {
                setTimeout(() => { window.location.reload(); }, 1000);
            }
        });

        // New Folder
        $('#kt_modal_new_folder_form').submit(function(e) {
            e.preventDefault();
            let name = $('#new_folder_name').val();
            $.post("{{ route('admin.file_manager.create_folder') }}", { name: name, folder: "{{ $currentFolder }}" }, function(res) {
                if(res.success) window.location.reload();
            });
        });

        // Delete
        $('.btn-delete').click(function(e) {
            e.preventDefault();
            let path = $(this).data('path');
            let type = $(this).data('type');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة هذا الملف!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('admin.file_manager.delete') }}", { path, type }, function(res) {
                        if(res.success) window.location.reload();
                    });
                }
            });
        });

        // Rename
        $('.btn-rename').click(function(e) {
            e.preventDefault();
            let path = $(this).data('path');
            let name = $(this).data('name');
            let type = $(this).data('type');
            Swal.fire({
                title: 'إعادة تسمية',
                input: 'text',
                inputValue: name,
                showCancelButton: true,
                confirmButtonText: 'تحديث',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    $.post("{{ route('admin.file_manager.rename') }}", { path, name: result.value, type }, function(res) {
                        if(res.success) window.location.reload();
                    });
                }
            });
        });

        // Copy URL
        $('.btn-copy-url').click(function() {
            let url = $(this).data('url');
            navigator.clipboard.writeText(url).then(() => {
                toastr.success('تم نسخ الرابط إلى الحافظة');
            });
        });

        // Selection Callback
        $('.choose-file').click(function() {
            let url = $(this).data('url');
            const target = new URLSearchParams(window.location.search).get('target');
            if(window.opener) {
                if (window.opener.metronicFilePickerCallback) {
                    window.opener.metronicFilePickerCallback(url, target);
                } else if(target) {
                    let input = window.opener.document.getElementById(target);
                    if(input) input.value = url;
                }
                window.close();
            }
        });
    });
</script>
</body>
</html>
