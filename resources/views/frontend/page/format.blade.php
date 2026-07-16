@extends('frontend.layouts.master')
@section('title', 'Oxford Test of English')
@section('content')
<div class="text-center">
    <img src="{{ url('assets/oxford/img/OTE-Approved-Test-Centre-Logo.png') }}" alt="" style="max-width: 150px; height: 70px;"/>
</div>
<div class="about-page1-area">
    <div class="container">
        <div class="text-center">
            <img src="{{ url('assets/oxford/img/test/oxford_test_of_english_header-0x0.jpg') }}" alt=""/>
        </div>       
        <div class="text-center mt-30">
            <h2>Test modules specifications</h2>
        </div>

        <div class="row mt-30">
            <div class="col-md-6 mt-30">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ url('assets/oxford/img/test/speaking.svg') }}">
                    </div>
                    <div class="col-md-6">
                        – Responding appropriately to eight questions on everyday topics <br>
                        – Leaving two voicemails in response <br>
                        – Giving a short talk on an issue <br>
                        – Answering six follow-up questions on this issue
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-30">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ url('assets/oxford/img/test/listening.svg') }}">
                    </div>
                    <div class="col-md-6">
                        – Conversations and monologues in different contexts, with totally twenty 3-option multiple-choice questions
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-30">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ url('assets/oxford/img/test/reading.svg') }}">
                    </div>
                    <div class="col-md-6">
                        – Understanding cohesion and organizing or identifying author purpose, by following tasks:<br>
                        – Six multiple-choice questions on short texts<br>
                        – Multiple matching of six people profiles with four text descriptions<br>
                        – A longer text with six gapped sentences<br>
                        – Four 3-option multiple-choice questions on a longer text
                    </div>
                </div>
            </div>
            <div class="col-md-6 mt-30">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{ url('assets/oxford/img/test/writing.svg') }}">
                    </div>
                    <div class="col-md-6">
                        – Writing a response to an input email, 80-130 words, in 20 minutes<br>
                        – Writing an essay or article/review on a topic typical of classroom discussion, 100-160 words, in 25 minutes
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-30">
            <h2>Take the online demo test</h2>
            <div class="apply-btn-area">
                <a href="https://fdslive.oup.com/www.oup.com/elt/general_content/global/ote/demo-v3/#/" target="_blank" class="default-big-btn disabled" style="padding: 15px 10px;width: 190px;">Go To Demo</a>
            </div>
        </div>
    </div>
</div>
@stop