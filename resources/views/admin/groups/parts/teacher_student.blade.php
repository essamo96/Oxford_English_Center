@extends('admin.layout.master')

@section('title')
    عرض طلاب المجموعة
@stop

@section('css')
    <style>
        textarea.swal2-textarea {
            height: 500px;
            max-width: 500px;
            font-size: 19px;
            height: 250px;
            padding: 10px;

        }

        div.swal2-popup {
            width: 500px;
            height: 377px;
        }

        /* .swal2-textarea {
                        width: 250px;
                        height: 70px;
                    } */
        .swal2-show {
            background-color: #00142ba9;
            border-radius: 20px;
            color: white;
        }

        .swal2-title {
            color: #f5a700;
        }

        .swal2-success-circular-line-left,
        .swal2-success-circular-line-right,
        .swal2-success-fix {
            visibility: hidden;
        }

        .swal2-container.swal2-center>.swal2-popup {
            width: 556px;
            height: 381px;
            color: white;
            background-color: #1e1c1abf;
            border-radius: 25px;
        }

        .swal2-html-container {
            font-size: 17px;
        }

        .swal2-input {
            height: 2.625em;
            padding: 0 0.75em;
            width: 256px;
        }

        .swal2-styled.swal2-confirm {
            border: 0;
            border-radius: 0.25em;
            background: initial;
            background-color: #3ebce5;
            color: #fff;
            font-size: 1.3rem;
        }

    </style>
@endsection

@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('dashboard.view') }}" class="text-muted text-hover-info">الرئيسية</a>
    </li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-muted">
        <a href="{{ route('groups.view') }}" class="text-muted text-hover-info">المجموعات</a>
    </li>
    <li class="breadcrumb-item">
        <span class="bullet bg-gray-400 w-5px h-2px"></span>
    </li>
    <li class="breadcrumb-item text-dark">عرض طلاب المجموعة</li>
@stop

@section('page-content')

    <div class="card shadow-sm">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <span class="card-label fw-bold fs-3 mb-1">
                    <i class="ki-duotone ki-people fs-3 text-info me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    عرض طلاب المجموعة: <span class="text-primary">{{ $grope_teacher_name }}</span> للمدرس: <span
                        class="text-primary">{{ $teacher_name }}</span>
                </span>
            </div>
            <div class="card-toolbar">
                <a href="{{ URL::previous() }}" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="card-body py-4">
            @include('admin.layout.error')
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5 table-striped table-bordered text-center"
                    id="mytable">
                    <thead class="bg-light">
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-center">#</th>
                            <th class="text-center">اسم الطالب/ة </th>
                            <th class="text-center"> رقم الموبايل </th>
                            <th class="text-center"> الايميل </th>
                            <th class="text-center">تاريخ الميلاد</th>
                            <th class="text-center">التخصص</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">نشط/مؤجل</th>
                            <th class="text-center min-w-150px">العمليات</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold text-center">
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($teacher_st as $info)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $info->name ? $info->name : 'لايوجد' }}</td>
                                <td>{{ $info->mobile ? $info->mobile : 'لايوجد' }}</td>
                                <td>
                                    @if (isset($info->email))
                                        {{ $info->email }}
                                    @else
                                        <span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا
                                            يوجد</span>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($info->dob))
                                        @if (substr($info->dob, 5) == substr(Carbon\Carbon::now()->format('m-d-Y'), 0, 5))
                                            <span class="badge badge-light-danger"><i
                                                    class="bi bi-gift text-danger me-1"></i>ميلاد سعيد</span>
                                        @else
                                            {{ $info->dob }}
                                        @endif
                                    @else
                                        <span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا
                                            يوجد</span>
                                    @endif
                                </td>
                                <td>
                                    @if (isset($info->job))
                                        {{ $info->job }}
                                    @else
                                        <span class="text-danger"><i class="bi bi-exclamation-circle text-danger me-1"></i>لا
                                            يوجد</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($info->status == 0)
                                        <button data-href="{{ Crypt::encrypt($info->id) }}"
                                            class="btn btn-sm btn-light-danger @can('admin.students.status') status @endcan"
                                            style="min-width:90px;">
                                            <i class="bi bi-x-circle fs-5"></i> غير فعال
                                        </button>
                                    @elseif($info->status == 1)
                                        <button data-href="{{ Crypt::encrypt($info->id) }}"
                                            class="btn btn-sm btn-light-success @can('admin.students.status') status @endcan"
                                            style="min-width:90px;">
                                            <i class="bi bi-check-circle fs-5"></i> فعال
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    @if ($info->delaying == 1)
                                        <button data-href="{{ Crypt::encrypt($info->id) }}" data-id="{{ $group_id }}"
                                            class="btn btn-sm btn-light-danger @can('admin.students.status') delay @endcan"
                                            style="min-width:90px;">
                                            <i class="bi bi-x-circle fs-5"></i> مؤجل
                                        </button>
                                    @elseif($info->delaying == 0)
                                        <button data-href="{{ Crypt::encrypt($info->id) }}" data-id="{{ $group_id }}"
                                            class="btn btn-sm btn-light-success @can('admin.students.status') delay @endcan"
                                            style="min-width:90px;">
                                            <i class="bi bi-check-circle fs-5"></i> نشط
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @can('admin.groups.edit')
                                            <a href="{{ route('students.edit', ['id' => Crypt::encrypt($info->id)]) }}"
                                                class="btn btn-icon btn-light-primary btn-sm" title="تعديل">
                                                <i class="bi bi-pencil-square fs-4"></i>
                                            </a>
                                        @endcan
                                        @can('admin.groups.student.view')
                                            <a href="{{ route('students.groups.add', ['student_id' => Crypt::encrypt($info->id), 'group_id' => Crypt::encrypt($info->group_id)]) }}"
                                                class="btn btn-icon btn-light-success btn-sm" title="اضافة لمجموعة">
                                                <i class="bi bi-plus-square fs-4"></i>
                                            </a>
                                            <a href="{{ route('students.groups.edit', ['student_id' => Crypt::encrypt($info->id), 'group_id' => Crypt::encrypt($info->group_id)]) }}"
                                                class="btn btn-icon btn-light-info btn-sm" title="تغيير المجموعة">
                                                <i class="bi bi-arrow-left-right fs-4"></i>
                                            </a>
                                        @endcan
                                        <a class="btn btn-icon btn-light-warning btn-sm Reply" style="cursor:pointer;"
                                            data-id="{{ Crypt::encrypt($info->id) }}" title="ارسال اشعار">
                                            <i class="bi bi-bell fs-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
@section('js')

    {{-- <script src="{{asset('assets/admin/ckeditor/ckeditor.js')}}" type="text/javascript"></script> --}}
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>


<script>
    $(document).on('click', ".status", function() {
        var id = $(this).data('href');
        var item = $(this);

        $.ajax({
            type: "GET",
            url: "{{ route('groups.students.status') }}",
            data: {
                'id': id
            },
            success: function(data) {
                if (data.type == 'yes') {
                    item.removeClass("btn-light-danger").addClass("btn-light-success");
                    item.html('<i class="bi bi-check-circle fs-5"></i> فعال');
                } else if (data.type == 'no') {
                    item.removeClass("btn-light-success").addClass("btn-light-danger");
                    item.html('<i class="bi bi-x-circle fs-5"></i> غير فعال');
                }
                if(typeof toastr !== 'undefined') toastr[data.status](data.message);
            }
        });
    });
</script>
 <script> 
$(document).on('click', ".delay", function() {
      let  id = $(this).data('id');
      let id_student = $(this).data('href');
    
    Swal.fire({
        title: 'أدخل سبب التاجيل  او ملاحظة',
        icon: 'info',
        html: `
        <div class="form-group">
          <label for="message-body" style="color:#fdc800"> ملاحظة:</label>
          <textarea name ="message-body" class="form-control" id="message-body"></textarea>
        </div>`,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'delay',
        onBeforeOpen: function() {
        
        CKEDITOR.replace('message-body');
      }
      
    }).then(function (result) {
        if (result.isConfirmed) {
           let body = $('#message-body').val();
            $.ajax({
                method: 'post',
                url: '{{ route('groups.students.delay') }}',
                data: {
                    id: id,
                    id_student: id_student,
                    message: body,
                    _token: '{{ csrf_token() }}'
                },
               
                success:function(response) {
                        Swal.fire({
                                    title: 'تم   تأجيل الطالب بنجاح !',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                          });
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                },
                error: function (response) {
                    Swal.fire({
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });


        });
    </script> 
    {{-- <script> 
            function sendMaseageToGroupe(id) {
            Swal.fire({
                icon: 'info',
                html: `
        <div class="form-group">
          <label for="swal_t" style="color:#fdc800">عنوان الرسالة:</label>
          <input type="text"  class="form-control" id="swal_t">
        </div>
        <div class="form-group">
          <label for="swal-textarea" style="color:#fdc800">نص الرسالة:</label>
          <textarea  class="form-control" id="swal-textarea"></textarea>
        </div>`,
                focusConfirm: false,
                preConfirm: () => {
                    return [
                        document.getElementById('swal-textarea').value,
                        document.getElementById('swal_t').value
                    ]
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('send.message') }}',
                        type: 'POST',
                        data: {
                            message: result.value[0],
                            title: result.value[1],
                            id: id,
                            somefield: "Some field value", _token: '{{csrf_token()}}'
                        },
                        success: function(response) {
                            // If the server successfully processes the data, show a success SweetAlert
                            Swal.fire('Message sent!', response.message, 'success')
                        },
                        error: function(error) {
                            // If there is an error sending the data to the server, show an error SweetAlert
                            Swal.fire('Oops...', 'هذا الطالب غير نشط او مؤجل !!', 'error')
                        }
                    })
                }
            })

        }
    </script>  --}}
        <script>
        $(document).on('click', '.Reply', function() {
            var id = $(this).data('id');

            Swal.fire({
                title: 'أدخل عنوان ونص الرسالة؟',
                icon: 'warning',
                html: `
        <div class="form-group">
          <label for="message-title" style="color:#fdc800">عنوان الرسالة:</label>
          <input type="text"  class="form-control" id="message-title">
        </div>
        <div class="form-group">
          <label for="message-body" style="color:#fdc800">نص الرسالة:</label>
          <textarea  class="form-control" id="message-body"></textarea>
        </div>`,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Send'
            }).then(function(result) {
                if (result.isConfirmed) {
                    let title = $('#message-title').val();
                    let body = $('#message-body').val();
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('send.message') }}',
                        data: {
                            id: id,
                            title: title,
                            message: body,
                            _token: '{{ csrf_token() }}'
                        },

                        success: function(response) {
                            Swal.fire({
                                title: 'تم  الارسال بنجاح !',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Reload the page after a short delay
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        },
                        error: function(response) {
                            Swal.fire({
                                title: 'Oops...',
                                text: 'Something went wrong!',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        });
    </script>

@stop
