@extends('frontend.layouts.master')

@section('title', 'Oxford Family')

@section('sidebar')
@parent
@stop

@section('content')
<div class="inner-page-banner-area" style="background-image: url('{{ url('assets/oxford/img/banner/family.jpg')}}');">
    <div class="container">
        <div class="pagination-area">
            <h1>Oxford Family</h1>
            <ul>
                <li><a href="#">Home</a> - </li>
                <li>Oxford Family</li>
            </ul>
        </div>
    </div>
</div>
<div class="lecturers-page1-area">
    <div class="container">
        <div class="row">
            @foreach($partners as $partner)
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="single-item">
                    <div class="lecturers1-item-wrapper">
                        <div class="lecturers-img-wrapper">
                            <a href="#"><img class="img-responsive" src="{{ url($partner->image)}}" alt="team"></a>
                        </div>
                        <div class="lecturers-content-wrapper">
                            <h3 class="item-title"><a href="#">{{ $partner->title}}</a></h3>
                            <span class="item-designation">{{ $partner->descs}}</span>
                            <span class="item-designation">{{ $partner->link}}</span>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!--END CONTENT ABOUT-->

@stop