@extends('frontend.layouts.master')
@section('title', optional($category_info)->name ?? 'Category')
@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url(optional($category_info)->color ? $category_info->color : 'assets/oxford/img/banner/1.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>{{ optional($category_info)->name ?? 'Category' }}</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>{{ optional($category_info)->name ?? 'Category' }}</li>
            </ul>
        </div>
    </div>
</div>
@if($page)
<div class="about-page1-area">
    <div class="container">
        <div class="row about-page1-inner">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="about-page-content-holder">
                    <div class="content-box">
                        <h2>{{$page->title}}</h2>
                        {!!$page->details!!}
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="about-page-img-holder">
                    <img src="{{url($page->image)}}" class="img-responsive" alt="about">
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="news-page-area">
    <div class="container">
        <div class="row">           
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row">
                    @if(sizeof($category_news)>0)
                    @foreach($category_news as $item)
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <?php
                        $cimg = $item->thumb;
                        if (substr($cimg, 0, 1) != '/') {
                            $cimg = '/' . $cimg;
                        }
                        $nimg = 'assets/site/images/default.jpg';
                        $img = (File::exists(public_path() . $cimg)) ? $cimg : $nimg;
                        ?>
                        <div class="news-box">
                            <div class="news-img-holder">
                                <img src="{{ URL::to(Helper::get_image($img)) }}" class="img-responsive" alt="{{ str_replace('"','',$item->title)}}">
                                <ul class="news-date2">
                                    <li><?= date('d M', strtotime($item->pub_date)) ?></li>
                                    <li><?= date('Y', strtotime($item->pub_date)) ?></li>                                
                                </ul>
                            </div>
                            <h3 class="title-news-left-bold"><a href="{{ URL::to('posts/'.$item->id) }}">{{$item->title}}</a></h3>
                            <p>{{$item->sub}}</p>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="content-box text-center">
                        <h2>No entries for {{ optional($category_info)->name ?? 'this category' }} found yet.</h2>
                    </div>
                    @endif
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <ul class="pagination-center">
                            {{ $category_news->appends(request()->query())->links() }}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
