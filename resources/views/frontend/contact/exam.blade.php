@extends('frontend.layouts.master')
@section('title', 'Book A Placement Test')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/contact.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Placement Test Booking</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Placement Test Booking</li>
            </ul>
        </div>
    </div>
</div>
<div class="registration-page-area bg-secondary">
    <div class="container">
        <h2 class="sidebar-title">Placement Test Booking</h2>
        <p>The test takes approximately 2 hours  and costs 50 NIS. After your placement test, we will discuss your level and the best course option(s) for your needs.</p>
        <div class="registration-details-area inner-page-padding">
            <center>                        
                @include('frontend.layouts.error')
            </center>
            <form id="contact-form" method="post">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="name">Name *</label>
                            <input type="text" placeholder="Name" class="form-control" name="name" required="">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="gender">Gender *</label>
                            <div class="form-group remember-style">
                                <span><input type="radio" name="gender" value="male" checked="">Male</span>
                                <span><input type="radio" name="gender" value="female">Female</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="phone">Mobile *</label>
                            <input type="text" placeholder="Phone" class="form-control" name="phone" required="">
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
                            <label class="control-label" for="days">Days</label>
                            <div class="form-group remember-style">
                                <span><input type="radio" name="days" value="Saturday" checked="">Saturday</span>
                                <span><input type="radio" name="days" value="Sunday">Sunday</span>
                                <span><input type="radio" name="days" value="Tuesday">Tuesday</span>
                                <span><input type="radio" name="days" value="Thursday">Thursday</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="time">Time</label>
                            <div class="form-group remember-style">
                                <span><input type="radio" name="time" value="10-12" checked="">10-12</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="pLace-order mt-30">
                            <button class="view-all-accent-btn disabled" type="submit" value="Login">Submit</button>
                        </div>
                    </div>
                </div>
                {{ csrf_field() }}
            </form>
        </div>
    </div>
</div>
@stop