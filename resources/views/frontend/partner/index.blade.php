@extends('frontend.layouts.master')

@section('title', 'Partners')

@section('sidebar')
@parent
@stop

@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/partner.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Partners </h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Partners </li>
            </ul>
        </div>
    </div>
</div>
<!-- Brand Area Start Here -->
<div class="brand-area">
    <div class="container">
        <div class="rc-carousel" data-loop="true" data-items="4" data-margin="30" data-autoplay="true" data-autoplay-timeout="5000" data-smart-speed="2000" data-dots="false" data-nav="false" data-nav-speed="false" data-r-x-small="2" data-r-x-small-nav="false" data-r-x-small-dots="false" data-r-x-medium="3" data-r-x-medium-nav="false" data-r-x-medium-dots="false" data-r-small="4" data-r-small-nav="false" data-r-small-dots="false" data-r-medium="4" data-r-medium-nav="false" data-r-medium-dots="false" data-r-large="4" data-r-large-nav="false" data-r-large-dots="false">
            @foreach($partners as $partner)
            <div class="brand-area-box">
                <a href="#"><img src="{{ url($partner->image)}}" alt="brand"></a>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Brand Area End Here -->
@stop