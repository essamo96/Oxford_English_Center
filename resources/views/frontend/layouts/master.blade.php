<!doctype html>
<html class="no-js" lang="">
    <head>
        @include('frontend.general.head')
        @yield('css')
    </head>
    <body>
        <!--[if lt IE 8]>
          <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
      <![endif]-->
        <!-- Add your site or application content here -->
        <!-- Preloader Start Here -->
        <div id="preloader">
            <div class="preloader-content">
                <div class="preloader-spinner"></div>
                <img src="{{ url('assets/oxford/img/logo.png') }}" alt="Logo" class="preloader-logo">
            </div>
        </div>
        <!-- Preloader End Here -->
        <!-- Main Body Area Start Here -->
        <div id="wrapper">
            <!-- Header Area Start Here -->
            @include('frontend.general.menu')
            @yield('content')
            @include('frontend.general.footer')
            @yield('js')
        </div>
        <div id="chat-overlay" class="row"></div>

        <audio id="chat-alert-sound" style="display: none">
            <source src="{{ url('assets/oxford/sound/facebook_chat.mp3') }}" />
        </audio>
    </body>
</html>