@extends('frontend.layouts.master')
@section('title', 'Apply For The Job')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/contact.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Join the Oxford Family</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Apply For The Job</li>
            </ul>
        </div>
    </div>
</div>
<div class="registration-page-area bg-secondary">
    <div class="container">
        <h2 class="sidebar-title">Apply For The Job</h2>
        <div class="registration-details-area inner-page-padding">
            @include('frontend.layouts.error')
            </center>
            <form id="contact-form" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="name">Name *</label>
                            <input type="text" placeholder="Name" class="form-control" name="name" required="">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="email">E-mail Address *</label>
                            <input type="text" placeholder="Email" class="form-control" name="email" required="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="phone">Phone *</label>
                            <input type="text" placeholder="Phone" class="form-control" name="phone" required="">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="cv">Upload CV *</label>
                            <input type="file"  class="form-control" name="cv" required="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="letter">Upload Cover Letter *</label>
                            <input type="file"  class="form-control" name="letter" required="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="pLace-order mt-30">
                            <button class="view-all-accent-btn disabled" type="submit">Submit</button>
                        </div>
                    </div>
                </div>
                {{ csrf_field() }}
            </form>
        </div>
    </div>
</div>
<!--CONTACT US AREA END-->
@stop