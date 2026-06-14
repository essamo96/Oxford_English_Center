<!DOCTYPE html>
<html>

<head>
    <title>Certificate</title>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            padding: 0;
            background-image: url('{{ $imagePath }}');
            background-image-resize: 6;
        }

        body {
            margin: 0;
            padding: 0;
            font-size: 0;
            line-height: 0;
        }

        /* لتغيير موضع أي عنصر: عدّل top / left في الـ class الخاص به */
        /* الوحدة بكسل (px) — الصفحة A4: عرض ~794px × ارتفاع ~1123px  */

        .title {
            position: fixed;
            top: 520px;
            left: -200px;
            width: 310mm;
            height: 40px;
            overflow: hidden;
            line-height: 40px;
            text-align: center;
            font-family: 'aguafinascript', 'dejavusans', sans-serif;
            font-size: 35px;
            font-style: normal;
            font-weight: normal;
            color: #010101;
        }

        .level-box {
            position: fixed;
            top: 639px;
            left: 380px;
            width: 250px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            font-weight: bold;
            color: #bd1e20;
        }

        .level-code {
            color: #bd1e20;
            font-style: italic;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
        }

        .week {
            position: fixed;
            top: 672px;
            left: 363px;
            width: 300px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

        .sessions {
            position: fixed;
            top: 692px;
            left: 363px;
            width: 100px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

        .numper_of_weeks {
            position: fixed;
            top: 710px;
            left: 363px;
            width: 100px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

        .hours {
            position: fixed;
            top: 730px;
            left: 363px;
            width: 100px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

        .scoure {
            position: fixed;
            top: 754px;
            left: 365px;
            width: 250px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

        .grade {
            position: fixed;
            top: 778px;
            left: 365px;
            width: 250px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

        .ccode {
            position: fixed;
            top: 793px;
            left: 205px;
            width: 200px;
            height: 20px;
            overflow: hidden;
            line-height: 20px;
            white-space: nowrap;
            font-size: 14px;
            font-family: 'dejavusans', sans-serif;
            color: #000000;
        }

    </style>
</head>

<body>

    {{-- اسم الطالب --}}
    <div class="title">{{ $formattedName }}</div>

    {{-- المستوى CEFR --}}
    <div class="level-box">
        | <span class="level-code">{{ $firstPart }}</span>
        @if($firstPart == 'A1') Beginner
        @elseif($firstPart == 'A2') Elementary
        @elseif($firstPart == 'B1') Preintermediate
        @elseif($firstPart == 'B1plus') Intermediate
        @elseif($firstPart == 'B2') Upper Intermediate
        @elseif($firstPart == 'C1') Advanced
        @elseif($firstPart == 'C2') Proficient
        @endif
    </div>

    {{-- التواريخ --}}
    <div class="week">{{ \Carbon\Carbon::parse($customData->group->start_date)->format('d / m / Y') }} To {{ \Carbon\Carbon::parse($customData->group->end_date)->format('d / m / Y') }}</div>

    {{-- الجلسات --}}
    <div class="sessions">3</div>

    {{-- الأسابيع --}}
    <div class="numper_of_weeks">12</div>

    {{-- الساعات --}}
    <div class="hours">72</div>

    {{-- النتيجة --}}
    <div class="scoure">{{ $customData->total_degree }} %</div>

    {{-- التقدير --}}
    @php
        $d = (int) $customData->total_degree;
        $grade = $d >= 96 ? 'Distinction' : ($d >= 90 ? 'High Merit' : ($d >= 80 ? 'Merit' : ($d >= 70 ? 'Pass' : '---')));
    @endphp
    <div class="grade">{{ $grade }}</div>

    {{-- رقم المرجع --}}
    <div class="ccode">{{ $customData->cer_code }}</div>

</body>

</html>
