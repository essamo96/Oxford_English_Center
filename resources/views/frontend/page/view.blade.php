@extends('frontend.layouts.master')
@section('title', $page->title)
@section('content')
<div class="ox-scope">

    {{-- ---------- Page banner + breadcrumb ---------- --}}
    <section class="ox-pagehero" style="background-image:url('{{ url($page->banner ? $page->banner : 'assets/oxford/img/banner/1.jpg') }}')">
        <div class="ox-pagehero__shapes">
            <span class="ox-shape ox-shape--2"></span>
            <span class="ox-shape ox-shape--3"></span>
        </div>
        <div class="ox-container ox-pagehero__inner" data-reveal="fade">
            <h1>{{ $page->title }}</h1>
            <ul class="ox-breadcrumb">
                <li><a href="{{ url('/') }}"><i class="bi bi-house-door-fill"></i> Home</a></li>
                <li>{{ $page->title }}</li>
            </ul>
        </div>
    </section>

    {{-- ---------- Content ---------- --}}
    <section class="ox-section">
        <div class="ox-container">
            <div class="ox-grid ox-grid--split" style="align-items:start">

                <div data-reveal="right">
                    <span class="ox-eyebrow">{{ $page->title }}</span>
                    <h2 class="ox-title">{{ $page->title }}</h2>
                    <div class="ox-prose">
                        {!! $page->details !!}
                    </div>
                </div>

                <div data-reveal="left">
                    @if($page->image)
                        <div class="ox-media" style="aspect-ratio:auto;margin-bottom:24px">
                            <img src="{{ url($page->image) }}" alt="{{ $page->title }}">
                        </div>
                    @endif

                    @php $prog = $page->program; @endphp
                    @if($prog || $page->age)
                        <div class="ox-sidecard">
                            <div class="ox-sidecard__head"><i class="bi bi-stars"></i> Course Features</div>
                            <ul class="ox-featurelist">
                                @php
                                    $allFees = [];
                                    $totalFeeAmt = 0;
                                    $minPaymentAmt = 0;
                                    
                                    if ($prog) {
                                        $fees = \App\Models\FeeSettings::where('program_id', $prog->id)->get();
                                        foreach ($fees as $fee) {
                                            $totalFeeAmt += $fee->amount;
                                            $allFees[$fee->type] = rtrim(rtrim(number_format($fee->amount, 2), '0'), '.') . ' ₪';
                                        }
                                        $minPaymentAmt = $prog->computeMinimumDue($totalFeeAmt);
                                    }
                                @endphp
                                @if(!empty($allFees))
                                    @if(isset($allFees['registration']) || isset($allFees['course']))
                                        <li><b>Price</b><span>{{ $allFees['registration'] ?? ($allFees['course'] ?? '') }}</span></li>
                                    @endif
                                    @if(isset($allFees['books']))
                                        <li><b>Book Fees</b><span>{{ $allFees['books'] }}</span></li>
                                    @endif
                                    <li><b>Total Fees</b><span style="font-weight:bold; color:var(--ox-primary)">{{ rtrim(rtrim(number_format($totalFeeAmt, 2), '0'), '.') }} ₪</span></li>
                                    @if($minPaymentAmt > 0)
                                        <li><b>Minimum Payment</b><span style="font-weight:bold; color:var(--ox-success)">{{ rtrim(rtrim(number_format($minPaymentAmt, 2), '0'), '.') }} ₪</span></li>
                                    @endif
                                @else
                                    @if($page->price)<li><b>Price</b><span>{{ $page->price }} ₪</span></li>@endif
                                    @if($page->fees)<li><b>Book Fees</b><span>{{ $page->fees }} ₪</span></li>@endif
                                @endif
                                @if(optional($prog)->age ?? $page->age)<li><b>Age Range</b><span>{{ optional($prog)->age ?? $page->age }}</span></li>@endif
                                @if(optional($prog)->level ?? $page->level)<li><b>Level</b><span>{{ optional($prog)->level ?? $page->level }}</span></li>@endif
                                @if(optional($prog)->weeks ?? $page->weeks)<li><b>Weeks</b><span>{{ optional($prog)->weeks ?? $page->weeks }}</span></li>@endif
                                @if(optional($prog)->hours ?? $page->hours)<li><b>Hours</b><span>{{ optional($prog)->hours ?? $page->hours }}</span></li>@endif
                                @if(optional($prog)->mock ?? $page->mock)<li><b>Mocks Exam</b><span>{{ optional($prog)->mock ?? $page->mock }}</span></li>@endif
                                @if(optional($prog)->duration ?? $page->duration)<li><b>Duration</b><span>{{ optional($prog)->duration ?? $page->duration }}</span></li>@endif
                                @if(optional($prog)->class_size ?? $page->class_size)<li><b>Class Size</b><span>{{ optional($prog)->class_size ?? $page->class_size }}</span></li>@endif
                                @if(optional($prog)->start ?? $page->start)<li><b>Start</b><span>{{ optional($prog)->start ?? $page->start }}</span></li>@endif
                                @if(optional($prog)->days ?? $page->days)<li><b>Days</b><span>{{ optional($prog)->days ?? $page->days }}</span></li>@endif
                                @if(optional($prog)->time ?? $page->time)<li><b>Time</b><span>{{ optional($prog)->time ?? $page->time }}</span></li>@endif
                            </ul>
                            <div style="padding:0 24px 24px">
                                <a class="ox-btn ox-btn--primary ox-btn--block" href="{{ url('book') }}"><i class="bi bi-journal-bookmark-fill"></i> Book this Course</a>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

</div>
@stop
