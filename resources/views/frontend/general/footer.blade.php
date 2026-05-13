<!-- Footer Area Start Here -->
<div class="text-center mb-10">
    <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="" style="max-width: 50%"/>
</div>
<footer>
    <div class="footer-area-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                    <div class="footer-box">
                        <a href="{{ url('/')}}"><img class="img-responsive" style="height: 170px;display: block;margin: auto;" src="{{ url('assets/oxford/img/footer-logo.png')}}" alt="logo"></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="footer-box">
                        <div class="footer-about">
                            <p>{{ optional($mysettings)->more_desc }}</p>
                        </div>
                        <ul class="footer-social">
                            @foreach($social as $row)
                            <li>
                                <a href="{{ $row->link }}" target="_blank" rel="nofollow">
                                    <i class="fa {{ $row->icon }}" aria-hidden="true"></i>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="footer-box">
                        <ul class="featured-links">
                            <li>
                                <ul>
                                    <li><a href="{{ url('/')}}">Home</a></li>
                                    <li><a href="{{ url('page/about')}}">About us</a></li>
                                    <li><a href="{{ url('page/promise')}}">Oxford Promise </a></li>
                                    <li><a href="{{ url('page/family')}}">Oxford Family</a></li>
                                    <li><a href="{{ url('page/prize')}}">IELTS Prize</a></li>
                                    <li><a href="{{ url('community')}}">Community</a></li>
                                </ul>
                            </li>
                            <li>
                                <ul>
                                    <li><a href="{{ url('page/methods')}}">Teaching Methods</a></li>
                                    <li><a href="{{ url('photos')}}">Photos</a></li>
                                    <li><a href="{{ url('videos')}}">Videos</a></li>
                                    <li><a href="{{ url('contact')}}">Contact Us</a></li>
                                    <li><a href="{{ url('page/privacy')}}">Privacy Policy</a></li>
                                    <li><a href="{{ url('page/tos')}}">Terms of Service</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="footer-box">
                        <h3>Information</h3>
                        <ul class="corporate-address">
                            <li><i class="fa fa-phone" aria-hidden="true"></i><a href="Phone:+{{ optional($mysettings)->mobile }}"> +{{ optional($mysettings)->mobile }} </a></li>
                            <li><i class="fa fa-envelope-o" aria-hidden="true"></i>{{ optional($mysettings)->contact_email }}</li>
                            <li><i class="fa fa-map-marker" aria-hidden="true"></i>{{ optional($mysettings)->address }}</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="footer-area-bottom">
        <div class="container">
            <p style="text-align: center">&copy; <?= date('Y') ?> oxford.ps All Rights Reserved. &nbsp; Designed by<a target="_blank" href="http://oxford.ps"> oxford.ps</a></p>
        </div>
    </div>
</footer>
<!-- Footer Area End Here -->
<!-- Main Body Area End Here -->
<!-- jquery-->
<script src="{{url('assets/oxford/js/jquery-2.2.4.min.js') }}" type="text/javascript"></script>
<!-- Plugins js -->
<script src="{{url('assets/oxford/js/plugins.js') }}" type="text/javascript"></script>
<!-- Bootstrap js -->
<script src="{{url('assets/oxford/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- WOW JS -->
<script src="{{url('assets/oxford/js/wow.min.js') }}"></script>
<!-- Nivo slider js -->
<script src="{{url('assets/oxford/vendor/slider/js/jquery.nivo.slider.js') }}" type="text/javascript"></script>
<script src="{{url('assets/oxford/vendor/slider/home.js') }}" type="text/javascript"></script>
<!-- Owl Cauosel JS -->
<script src="{{url('assets/oxford/vendor/OwlCarousel/owl.carousel.min.js') }}" type="text/javascript"></script>
<!-- Meanmenu Js -->
<script src="{{url('assets/oxford/js/jquery.meanmenu.min.js') }}" type="text/javascript"></script>
<!-- Srollup js -->
<script src="{{url('assets/oxford/js/jquery.scrollUp.min.js') }}" type="text/javascript"></script>
<!-- jquery.counterup js -->
<script src="{{url('assets/oxford/js/jquery.counterup.min.js') }}"></script>
<script src="{{url('assets/oxford/js/waypoints.min.js') }}"></script>
<!-- Countdown js -->
<script src="{{url('assets/oxford/js/jquery.countdown.min.js') }}" type="text/javascript"></script>
<!-- Isotope js -->
<script src="{{url('assets/oxford/js/isotope.pkgd.min.js') }}" type="text/javascript"></script>
<!-- Magic Popup js -->
<script src="{{url('assets/oxford/js/jquery.magnific-popup.min.js') }}" type="text/javascript"></script>
<!-- Gridrotator js -->
<script src="{{url('assets/oxford/js/jquery.gridrotator.js') }}" type="text/javascript"></script>
<!-- Custom Js -->
<script src="{{url('assets/oxford/js/main.js?v=3') }}" type="text/javascript"></script>


<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function () {
    FB.init({
        xfbml: true,
        version: 'v8.0'
    });
};
(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
        return;
    js = d.createElement(s);
    js.id = id;
    js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<!-- Your Chat Plugin code -->
<div class="fb-customerchat"
     attribution=setup_tool
     page_id="216368378403099">
</div>