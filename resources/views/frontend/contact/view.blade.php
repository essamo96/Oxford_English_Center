@extends('frontend.layouts.master')
@section('title', 'Contact Us')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/contact.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Contact Us</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Contact Us</li>
            </ul>
        </div>
    </div>
</div>
<!--CONTACT US AREA-->
<div class="contact-us-page1-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="contact-us-info1">
                    <ul>
                        <li>
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            <h3>Phone</h3>
                            <p>+{{$mysettings->mobile}}</p>
                        </li>
                        <li>
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                            <h3>Address</h3>
                            <p>{{$mysettings->address}}</p>
                        </li>
                        <li>
                            <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            <h3>E-mail</h3>
                            <p>{{$mysettings->contact_email}}</p>
                        </li>
                        <li>
                            <h3>Follow Us</h3>
                            <ul class="contact-social">
                                @foreach($social as $row)
                                <li>
                                    <a href="{{ $row->link }}" target="_blank" rel="nofollow">
                                        <i class="fa {{ $row->icon }}" aria-hidden="true"></i>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h2 class="title-default-left title-bar-high">Contact With Us</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="contact-form1">
                        <center>
                            @include('frontend.layouts.error')
                        </center>
                        <form id="contact-form" method="post">
                            <fieldset>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" placeholder="Name" class="form-control" name="name" id="form-name" required="">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="email" placeholder="Email" class="form-control" name="email" id="form-email" data-error="Email field is required" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <textarea placeholder="Message" class="textarea form-control" name="message" id="form-message" rows="8" cols="20" data-error="Message field is required" required=""></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-sm-12">
                                    <div class="form-group margin-bottom-none">
                                        <button type="submit" class="default-big-btn disabled">Send Message</button>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-6 col-sm-12">
                                    <div class="form-response"></div>
                                </div>
                            </fieldset>
                            {{ csrf_field() }}
                        </form>
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
<!--CONTACT US AREA END-->
@stop
