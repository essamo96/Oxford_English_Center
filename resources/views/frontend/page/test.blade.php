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
        <div class="content-box mt-30">
            <p>The Oxford Test of English is a computer-adaptive general English proficiency test. Developed by Oxford University Press and certified by the University of Oxford, it is more flexible and faster than traditional proficiency tests.The Oxford Test of English is available to test takers 365 days a year, in any module combination (Speaking, Listening, Reading, Writing) with tests results available in just 14 days!</p>
        </div>
        <div class="row mt-30">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <iframe src="https://www.youtube.com/embed/8X_SPgPSZRg?rel=0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" width="100%" height="396" frameborder="0"></iframe>
            </div>
            <div class="col-md-2"></div>            
        </div>
        <div class="row mt-30">
            <div class="col-md-3">
                <a href="{{ url('test-format') }}">
                    <img src="{{ url('assets/oxford/img/test/speaking.svg') }}">
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('test-format') }}">
                    <img src="{{ url('assets/oxford/img/test/listening.svg') }}">
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('test-format') }}">
                    <img src="{{ url('assets/oxford/img/test/reading.svg') }}">
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ url('test-format') }}">
                    <img src="{{ url('assets/oxford/img/test/writing.svg') }}">
                </a>
            </div>
        </div>
        <div class="text-center mt-30">
            <h2>
                The Oxford Test of English is available in 19 countries
            </h2>
            <div class="content-box ">
                <p>
                    The test is a multi-level, general English proficiency test which assesses
                    the ability to understand and communicate effectively in English,
                    reporting at three CEFR levels: B2, B1, and A2.
                </p>
                <div class="apply-btn-area">
                    <a href="{{ url('upcoming-exam-dates') }}" class="default-big-btn disabled" style="padding: 15px 10px;width: 190px;">Upcoming Exam Dates</a>
                </div>
            </div>
        </div>
        <div class="text-center about-page1-inner mt-30">
            <img src="{{ url('assets/oxford/img/test/oxford_test_of_english_banner.jpg') }}" border="0"> 
        </div>
        <div class="content-box mt-30">
            <h2>Benefits</h2>
            <div class="row mt-30">
                <div class="col-md-4">
                    <h2>Online</h2>
                    <ul>			
                        <li>Online programming</li>
                        <li>Online testing</li>
                        <li>Online results</li>
                    </ul>	

                </div>
                <div class="col-md-4">
                    <h2>Adaptable</h2>
                    <ul>	
                        <li>The Reading and Listening modules are tailored to the level of the person tested</li>
                        <li>The tests are never too heavy or too light</li>
                        <li>Shorter testing time</li>
                        <li>Motivational experience</li>
                    </ul>		
                </div>
                <div class="col-md-4">
                    <h2>Sure</h2>
                    <ul>
                        <li>Safe test conditions</li>
                        <li>Verification and surveillance procedures</li>
                        <li>Secure online environment</li>
                        <li>The adaptability of the test items reduces the risk of miscarriage</li>
                    </ul>
                </div>
            </div>  
            <div class="row mt-30">
                <div class="col-md-4">	
                    <h2>Rapid</h2>
                    <ul>			
                        <li>Receiving results for Reading and Listening tests on the day of testing</li>
                        <li>Results for Speaking and Writing tests within 14 days</li>
                    </ul>		
                </div>
                <div class="col-md-4">
                    <h2>Flexible</h2>
                    <ul>	
                        <li>Individual or combined modules for Listening, Speaking, Writing and Reading</li>
                        <li>Ability to repeat testing of one of the modules or a combination thereof</li>
                    </ul>	
                </div>
                <div class="col-md-4">
                    <h2>Available</h2>
                    <ul>
                        <li>Testing available 365 days a year </li>
                        <li>Possibilities for testing their in 14 days after the programming</li>
                    </ul>
                </div>
            </div>  
        </div>
    </div>
</div>
@stop