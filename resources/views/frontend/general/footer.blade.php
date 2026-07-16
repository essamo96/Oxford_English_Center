{{-- ============================================================
     Oxford English Centre — Footer (redesigned 2026)
     Markup modernised; all dynamic data ($mysettings, $social) and
     existing routes preserved. Legacy plugin scripts kept intact.
     ============================================================ --}}

<!-- Approvals strip -->
<div class="ox-approve">
    <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="OTE Approved Test Centre" style="max-width: 150px; height: 70px;">
</div>

<footer class="ox-footer">
    <div class="ox-container ox-footer__inner">
        <div class="ox-footer__grid">

            <div data-reveal="up">
                <a href="{{ url('/') }}">
                    <img class="ox-footer__logo" src="{{ url('assets/oxford/img/footer-logo.png') }}" alt="Oxford English Centre">
                </a>
                <p>{{ optional($mysettings)->more_desc }}</p>
                <div class="ox-social">
                    @foreach($social as $row)
                        <a href="{{ $row->link }}" target="_blank" rel="nofollow noopener" aria-label="social link">
                            <i class="fa {{ $row->icon }}" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <div data-reveal="up" data-reveal-delay=".05s">
                <h3>Explore</h3>
                <ul class="ox-footer__links">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ url('page/about') }}">About us</a></li>
                    <li><a href="{{ url('page/promise') }}">Oxford Promise</a></li>
                    <li><a href="{{ url('page/family') }}">Oxford Family</a></li>
                    <li><a href="{{ url('page/prize') }}">IELTS Prize</a></li>
                    <li><a href="{{ url('community') }}">Community</a></li>
                </ul>
            </div>

            <div data-reveal="up" data-reveal-delay=".1s">
                <h3>Resources</h3>
                <ul class="ox-footer__links">
                    <li><a href="{{ url('page/methods') }}">Teaching Methods</a></li>
                    <li><a href="{{ url('photos') }}">Photos</a></li>
                    <li><a href="{{ url('videos') }}">Videos</a></li>
                    <li><a href="{{ url('contact') }}">Contact Us</a></li>
                    <li><a href="{{ url('page/privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ url('page/tos') }}">Terms of Service</a></li>
                </ul>
            </div>

            <div data-reveal="up" data-reveal-delay=".15s">
                <h3>Get in touch</h3>
                <ul class="ox-footer__info">
                    <li><i class="bi bi-telephone-fill"></i> <a href="tel:+{{ optional($mysettings)->mobile }}">+{{ optional($mysettings)->mobile }}</a></li>
                    <li><i class="bi bi-envelope-fill"></i> <span>{{ optional($mysettings)->contact_email }}</span></li>
                    <li><i class="bi bi-geo-alt-fill"></i> <span>{{ optional($mysettings)->address }}</span></li>
                </ul>
            </div>

        </div>
    </div>

    <div class="ox-footer__bottom">
        <div class="ox-container">
            &copy; <span data-year>{{ date('Y') }}</span> oxford.ps — All Rights Reserved. &nbsp; Designed by
            <a target="_blank" href="http://oxford.ps">oxford.ps</a>
        </div>
    </div>
</footer>

<!-- Back to top -->
<button class="ox-totop" data-totop aria-label="Back to top"><i class="bi bi-arrow-up"></i></button>

<!-- ============================================================
     Legacy plugin scripts — preserved for existing pages
     ============================================================ -->
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

<!-- ============================================================
     Oxford 2026 Design System modules (new)
     ============================================================ -->
<script src="{{ url('assets/js/app.js?v=3') }}"></script>
<script src="{{ url('assets/js/animations.js?v=3') }}"></script>
<script src="{{ url('assets/js/particles.js?v=3') }}"></script>
<script src="{{ url('assets/js/navbar.js?v=3') }}"></script>
<script src="{{ url('assets/js/sliders.js?v=3') }}"></script>
<script src="{{ url('assets/js/forms.js?v=3') }}"></script>

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
