@extends('frontend.layouts.master')
@section('title', 'Photos')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Photos</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Photos</li>
            </ul>
        </div>
    </div>
</div>
<div class="gallery-area1">
    <div class="container">
        <div class="row gallery-wrapper">
            @foreach($pics as $list)
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="gallery-box">
                    <img src="{{ URL::to('File/Images/photo/'.$list->image) }}" class="img-responsive" alt="gallery">
                    <div class="gallery-content">
                        <a href="{{ URL::to('File/Images/photo/'.$list->image) }}" class="zoom"><i class="fa fa-link" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@stop