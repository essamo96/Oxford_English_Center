<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
@if ($mysettings)
<meta name="description" content="{{$mysettings->description}}" />
<meta name="keywords"    content="{{$mysettings->more_desc}}" />
@else
<meta name="description" content="" />
<meta name="keywords"    content="" />

@endif
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="{{url('assets/oxford/img/favicon.ico') }}" />
<!-- Normalize CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/normalize.css') }}">
<!-- Main CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/main.css') }}">
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/bootstrap.min.css') }}">
<!-- Animate CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/animate.min.css') }}">
<!-- Font-awesome CSS-->
<link rel="stylesheet" href="{{url('assets/oxford/css/font-awesome.min.css') }}">
<!-- Owl Caousel CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/vendor/OwlCarousel/owl.carousel.min.css') }}">
<link rel="stylesheet" href="{{url('assets/oxford/vendor/OwlCarousel/owl.theme.default.min.css') }}">
<!-- Main Menu CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/meanmenu.min.css') }}">
<!-- nivo slider CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/vendor/slider/css/nivo-slider.css') }}" type="text/css" />
<link rel="stylesheet" href="{{url('assets/oxford/vendor/slider/css/preview.css') }}" type="text/css" media="screen" />
<!-- Datetime Picker Style CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/jquery.datetimepicker.css') }}">
<!-- Magic popup CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/magnific-popup.css') }}">
<!-- Switch Style CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/hover-min.css') }}">
<!-- ReImageGrid CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/css/reImageGrid.css') }}">
<!-- Custom CSS -->
<link rel="stylesheet" href="{{url('assets/oxford/style.css?v=8') }}">
<link rel="stylesheet" href="{{url('assets/oxford/css/custom.css') }}">
<link rel="stylesheet" href="{{url('assets/oxford/css/chat.css') }}">
<!-- Modernizr Js -->
<script src="{{url('assets/oxford/js/modernizr-2.8.3.min.js') }}"></script>
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

<style>
    .dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-menu {
  display: none;
  position: absolute;
  z-index: 1;
  min-width: 160px;
  padding: 5px 0;
  margin: 2px 0 0;
  list-style: none;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-shadow: 0 6px 12px rgba(0,0,0,.175);
}

.dropdown:hover .dropdown-menu {
  display: block;
}

.dropdown-item {
  display: block;
  width: 100%;
  padding: 3px 20px;
  clear: both;
  font-weight: 400;
  color: #333;
  text-align: inherit;
  white-space: nowrap;
  background-color: transparent;
  border: 0;
}

.dropdown-item:hover, .dropdown-item:focus {
  color: #262626;
  text-decoration: none;
  background-color: #f5f5f5;
}

.btn {
  display: inline-block;
  padding: 6px 12px;
  margin-bottom: 0;
  font-size: 14px;
  font-weight: 400;
  line-height: 1.42857143;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle;
  touch-action: manipulation;
  cursor: pointer;
  user-select: none;
  background-image: none;
  border: 1px solid transparent;
  border-radius: 4px;
}

.btn-primary {
  color: #fff;
  background-color: #337ab7;
  border-color: #2e6da4;
}

</style>
