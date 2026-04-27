@extends('frontend.layouts.master')
@section('title', 'Oxford Test of English')
@section('content')
<div class="text-center">
    <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt=""/>
</div>
<div class="about-page1-area">
    <div class="container">
        <div class="text-center">
            <img src="{{ url('assets/oxford/img/test/oxford_test_of_english_header-0x0.jpg') }}" alt=""/>
        </div>       
        <div class="text-center mt-30">
            <h2>
                Upcoming Exam Dates
            </h2>
            <div class="content-box ">
                <ul>
                    <li><strong>27/7/2021</strong></li>
                    <li><strong>24/8/2021</strong></li>
                    <li><strong>21/9/2021</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop