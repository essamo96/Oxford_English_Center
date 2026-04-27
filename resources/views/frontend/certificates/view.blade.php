@extends('frontend.layouts.master')
@section('title', 'Certificates')
@section('content')
    <div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/contact.jpg') }}');">
        <div class="container">
            <div class="pagination-area">
                <h1>Certificates</h1>
                <ul>
                    <li><a href="#">Home</a> - </li>
                    <li>Certificates</li>
                </ul>
            </div>
        </div>
    </div>
    <!--Certificates AREA-->
    <div class="contact-us-page1-area">
        <div class="container">
            <div class="row">
                <h2 class="sidebar-title">Searsh For Certificates</h2>
                <div class="col-xl-9 col-lg-9 col-md-8 col-sm-12">
                    <div class="row" id="certificates" style="display: none">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                            {{-- <img src="img/course/15.jpg" class="img-responsive" alt="course"/> --}}
                            <div class="course-details-inner" id="certificates_pdf">

                            </div>
                            <div class="section-divider"></div>
                            <div class="course-details-inner">
                                <div class="course-details-comments">
                                    <h3 class="sidebar-title"> <i class="bi bi-hearts text-info mx-2"></i> Student Info</h3>
                                    <div class="media">
                                        <a href="#" class="pull-left">
                                            <img alt="Comments" style="width: 50px;" id="studentImage" 
                                                src="{{ url('assets/oxford/img/favicon.ico') }}" class="media-object">
                                        </a>
                                        <div class="media-body">
                                            <h3><a href="#" id="student-name"></a></h3>
                                            <h4 id="student-group"></h4>
                                            <p>Thanx For Oxford Iam ... </p>
                                            <div class="replay-area">
                                                <ul>
                                                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                    <li><i class="fa fa-star" aria-hidden="true"></i></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                    <div class="sidebar">
                        <div class="sidebar-box">
                            <div class="sidebar-box-inner">
                                <h3 class="sidebar-title">Certificate Code</h3>
                                <div class="sidebar-course-price">
                                    <div class="form-group">
                                        <input type="text" placeholder="Certificate Code*" id="code"
                                            class="form-control" name="name" id="form-name"
                                            data-error="Certificate Code field is required" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <a id="Searsh" class="enroll-btn">Searsh</a>
                                    <a  id="download-btn" style="display: none;"  href="#" download="certificate.pdf" class="download-btn">Download PDF</a>
                                </div>
                            </div>
                        </div>
                        <div class="sidebar-box">
                            <div class="sidebar-box-inner">
                                <h3 class="sidebar-title">More Infos ?</h3>
                                <div class="sidebar-question-form">
                                    <form id="question-form" novalidate="true">
                                        <fieldset>
                                            <div class="form-group">
                                                <input type="text" placeholder="Name*" class="form-control"
                                                    name="name" id="form-name" data-error="Name field is required"
                                                    required="">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <div class="form-group">
                                                <input type="email" placeholder="Email*" class="form-control"
                                                    name="email" id="form-email" data-error="Email field is required"
                                                    required="">
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <div class="form-group">
                                                <textarea placeholder="Message*" class="textarea form-control" name="message" id="sidebar-form-message" rows="3"
                                                    cols="20" data-error="Message field is required" required=""></textarea>
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="default-full-width-btn disabled">Send</button>
                                            </div>
                                            <div class="form-response"></div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="google-map-area">
            </div>
        </div>
    </div>
    <!--Certificates AREA END-->
@stop
@section('js')
<script>
    $(document).ready(function() {
        $("#Searsh").on("click", function() {
            const code = $("#code").val();

            $.ajax({
                type: "POST",
                url: "{{ route('certificates.student') }}",
                data: {
                    code: code,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    // Check if the response is empty or an error occurred
                    if (!response || response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'An error occurred while processing your request.',
                        });
                        return;
                    }

                    // Display the PDF container
                    $("#certificates").css("display", "block");
                    
                    // Display the PDF in an iframe
                    $("#certificates_pdf").html('<iframe src="data:application/pdf;base64,' + response.pdf + '" style="width:100%; height:1100px;"></iframe>');
                    const studentData = response.student;
                    $("#student-name").text(studentData.student.name);
                    $("#student-img").text(studentData.student.image);
                    $("#student-group").text(studentData.group.title);
                    $("#download-btn").css("display", "block");
                     $("#download-btn").attr("href", "data:application/pdf;base64," + response.pdf);
                        // Change the src attribute of the img element
                    const studentImageElement = $("#studentImage");
                    if (studentData.student.image) {
                        studentImageElement.attr("src", studentData.student.image);
                    } else {

                        studentImageElement.attr("src", "{{ url('default-photo.png') }}");
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Search successful!',
                        text: 'Your search results have been loaded.',
                    });
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.',
                    });
                }
            });
        });
    });
</script>

    {{-- <script>
        $(document).ready(function() {
            $("#Searsh").on("click", function() {
                const code = $("#code").val();
                // alert(code);
                $.ajax({
                    type: "POST",
                    url: "{{ route('certificates.student') }}",
                    dataType: 'blob',
                    contentType: "application/xml; charset=utf-8",
                    processData: false,
                    data: {
                        code: code,
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        $("#certificates").css("display", "block");
                        $("#pdf-container").html('<iframe src="data:application/pdf;base64,' + response + '" style="width:100%; height:500px;"></iframe>');

                        // const url = URL.createObjectURL(data);

                        // $('#certificates_pdf').html(
                        //     `<iframe src="${url}" style="width:100%; height:500px;"></iframe>`
                        //     );
                        // $("#certificates_pdf").html(response);
                        Swal.fire({
                            icon: 'success',
                            title: 'Search successful!',
                            text: 'Your search results have been loaded.',
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.',
                        });
                    }
                });
            });
        });
    </script> --}}
@stop
