@extends('frontend.layouts.master')
@section('title', 'Videos')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/gallary.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Videos</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Videos</li>
            </ul>
        </div>
    </div>
</div>
<div class="gallery-area1">
    <div class="container">
        <div class="row gallery-wrapper">
            @foreach($videos as $list)
            <?php $img = explode("v=", $list->url); ?>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="gallery-box">
                    <div class="video-area2 overlay-video bg-common-style" style="background-image: url('{{ url('https://img.youtube.com/vi/'.$img[1].'/0.jpg')}}');">
                        <div class="video-content">
                            <a class="play-btn popup-youtube" href="{{ $list->url }}"><i class="fa fa-play" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <h4>{{ $list->title}}</h4>
            </div>
            @endforeach
        </div>
    </div>
    {{ $videos->links() }}
</div>
@stop