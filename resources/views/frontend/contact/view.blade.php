@extends('frontend.layouts.master')
@section('title', 'Contact Us')
@section('content')
<div class="ox-scope">

    {{-- ---------- Banner ---------- --}}
    <section class="ox-pagehero" style="background-image:url('{{ url('assets/oxford/img/banner/contact.jpg') }}')">
        <div class="ox-pagehero__shapes">
            <span class="ox-shape ox-shape--2"></span>
            <span class="ox-shape ox-shape--3"></span>
        </div>
        <div class="ox-container ox-pagehero__inner" data-reveal="fade">
            <h1>Contact Us</h1>
            <ul class="ox-breadcrumb">
                <li><a href="{{ url('/') }}"><i class="bi bi-house-door-fill"></i> Home</a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </section>

    {{-- ---------- Contact ---------- --}}
    <section class="ox-section">
        <div class="ox-container">
            <div class="ox-grid" style="grid-template-columns:1fr 1.6fr;gap:48px;align-items:start">

                {{-- info column --}}
                <div data-reveal="right" style="display:grid;gap:16px">
                    <div class="ox-infocard">
                        <span class="ox-infocard__icon"><i class="bi bi-telephone-fill"></i></span>
                        <div><h3>Phone</h3><p>+{{ $mysettings->mobile }}</p></div>
                    </div>
                    <div class="ox-infocard">
                        <span class="ox-infocard__icon"><i class="bi bi-geo-alt-fill"></i></span>
                        <div><h3>Address</h3><p>{{ $mysettings->address }}</p></div>
                    </div>
                    <div class="ox-infocard">
                        <span class="ox-infocard__icon"><i class="bi bi-envelope-fill"></i></span>
                        <div><h3>E-mail</h3><p>{{ $mysettings->contact_email }}</p></div>
                    </div>
                    <div class="ox-infocard" style="align-items:center">
                        <span class="ox-infocard__icon"><i class="bi bi-share-fill"></i></span>
                        <div>
                            <h3>Follow Us</h3>
                            <div class="ox-social" style="margin-top:8px">
                                @foreach($social as $row)
                                    <a href="{{ $row->link }}" target="_blank" rel="nofollow noopener" aria-label="social">
                                        <i class="fa {{ $row->icon }}" aria-hidden="true"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- form column --}}
                <div data-reveal="left">
                    <span class="ox-eyebrow">Get in touch</span>
                    <h2 class="ox-title">Contact With Us</h2>

                    <div class="ox-form-card">
                        <center>
                            @include('frontend.layouts.error')
                        </center>
                        <form id="contact-form" method="post">
                            <fieldset style="border:0;padding:0;margin:0">
                                <!-- Honeypot Field for Spam Prevention -->
                                <div style="display:none;" aria-hidden="true">
                                    <label for="company_website">Company Website</label>
                                    <input type="text" name="company_website" id="company_website" autocomplete="off">
                                </div>
                                <div class="ox-form-row">
                                    <div class="ox-field form-group">
                                        <label class="ox-label" for="form-name">Name</label>
                                        <input type="text" placeholder="Your name" class="ox-input form-control" name="name" id="form-name" required="">
                                    </div>
                                    <div class="ox-field form-group">
                                        <label class="ox-label" for="form-email">Email</label>
                                        <input type="email" placeholder="you@example.com" class="ox-input form-control" name="email" id="form-email" data-error="Email field is required" required="">
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="ox-form-row">
                                    <div class="ox-field form-group">
                                        <label class="ox-label" for="form-phone">Phone / WhatsApp</label>
                                        <input type="text" placeholder="e.g. +97259..." class="ox-input form-control" name="mobile" id="form-phone">
                                    </div>
                                    <div class="ox-field form-group">
                                        <label class="ox-label" for="form-subject">Subject</label>
                                        <input type="text" placeholder="Subject" class="ox-input form-control" name="subject" id="form-subject">
                                    </div>
                                </div>
                                <div class="ox-field form-group">
                                    <label class="ox-label" for="form-message">Message</label>
                                    <textarea placeholder="How can we help you?" class="ox-textarea textarea form-control" name="message" id="form-message" rows="8" cols="20" data-error="Message field is required" required=""></textarea>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div style="display:flex;flex-wrap:wrap;gap:16px;align-items:center">
                                    <button type="submit" class="ox-btn ox-btn--primary default-big-btn disabled"><i class="bi bi-send-fill"></i> Send Message</button>
                                    <div class="form-response" style="flex:1"></div>
                                </div>
                            </fieldset>
                            {{ csrf_field() }}
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
@stop
