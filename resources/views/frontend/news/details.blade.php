@extends('frontend.layouts.master')
@section('title', $post_news->title)
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url($post_news->category->color?$post_news->category->color:'assets/oxford/img/banner/1.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>{{$post_news->title}}</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>{{$post_news->title}}</li>
            </ul>
        </div>
    </div>
</div>
<div class="news-details-page-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12">
                <div class="row news-details-page-inner">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="news-img-holder">
                            <?php
                            $cimg = $post_news->image;
                            if (substr($cimg, 0, 1) != '/') {
                                $cimg = '/' . $cimg;
                            }
                            $nimg = 'assets/site/images/default.jpg';
                            $img = File::exists(public_path() . $cimg) ? $cimg : $nimg;
                            ?>
                            <img src="{{ URL::to(Helper::get_image($img)) }}" class="img-responsive" alt="News Image">
                            <ul class="news-date1">
                                <li><?= date('d M', strtotime($post_news->pub_date)) ?></li>
                                <li><?= date('Y', strtotime($post_news->pub_date)) ?></li>   
                            </ul>
                        </div>
                        <h2 class="title-default-left-bold-lowhight"><a>{{$post_news->title}}</a></h2>
                        {!! $post_news->descs !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                <div class="sidebar">
                    <div class="sidebar-box">
                        <div class="sidebar-box-inner">
                            <h3 class="sidebar-title">Related Posts</h3>
                            <div class="sidebar-latest-research-area">
                                <ul>
                                    @foreach($related as $li)
                                    <?php
                                    $cimg = $li->image;
                                    if (substr($cimg, 0, 1) != '/') {
                                        $cimg = '/' . $cimg;
                                    }
                                    $nimg = 'assets/site/images/default.jpg';
                                    $img = File::exists(public_path() . $cimg) ? $cimg : $nimg;
                                    ?>
                                    <li>
                                        <div class="latest-research-img">
                                            <a href="{{ URL::to('posts/'.$li->id) }}"><img src="{{ URL::to(Helper::get_image($img)) }}" class="img-responsive" alt="skilled"></a>
                                        </div>
                                        <div class="latest-research-content">
                                            <p>{{$li->title}}</p>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop