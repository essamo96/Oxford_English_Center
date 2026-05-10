<!DOCTYPE html>
<html>

<head>
    <title>Certificate</title>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .certificate-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            background-image: url('{{ $imagePath }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* اسم الطالب - التحكم الكامل بالتموضع */
        .student-name {
            position: absolute; /* يجب أن يكون absolute للتحكم الحر */
            top: 540px; 
            left: 0;
            width: 100%;
            text-align: center;
            font-family: 'aguafinascript';
            font-size: 65px;
            color: #1a1a1a;
            /* background-color: rgba(189, 30, 33, 0.1);  تم تخفيف اللون للمساعدة في المعاينة فقط */
        }

        /* قيمة المستوى - بجانب CEFR Level */
        .cefr-value {
            position: absolute;
            top: 566px;
            left: 485px;
            color: #bd1e20;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
        }

        /* محاذاة قيم الجدول - جميعها absolute */
        .value-item {
            position: absolute;
            font-size: 15px;
            font-weight: bold;
            color: #2c3e50;
        }

        .dates-value {
            top: 591px;
            left: 455px;
        }

        .sessions-value {
            top: 610px;
            left: 455px;
        }

        .weeks-value {
            top: 628px;
            left: 455px;
        }

        .duration-value {
            top: 645px;
            left: 455px;
        }

        .score-value {
            top: 664px;
            left: 455px;
        }

        .grade-value {
            top: 683px;
            left: 455px;
        }

        /* رقم المرجع */
        .ref-value {
            position: absolute;
            top: 698px;
            left: 285px;
            font-size: 13px;
            font-weight: bold;
            color: #2c3e50;
        }

    </style>
</head>

<body>
    <div class="certificate-container">
        <!-- اسم الطالب -->
        <div class="student-name">{{ $formattedName }}</div>

        <!-- قيمة المستوى -->
        <div class="cefr-value">{{ $firstPart }}</div>

        <!-- قيم الجدول -->
        <div class="value-item dates-value">
            {{ \Carbon\Carbon::parse($customData->group->start_date)->format('d / m / Y') }} To {{ \Carbon\Carbon::parse($customData->group->end_date)->format('d / m / Y') }}
        </div>

        <div class="value-item sessions-value">3</div>
        <div class="value-item weeks-value">12</div>
        <div class="value-item duration-value">72</div>
        <div class="value-item score-value">{{$customData->total_degree}} %</div>
        
        <div class="value-item grade-value">
            @if ($customData->total_degree >= 70 && $customData->total_degree <= 79) Pass
            @elseif ($customData->total_degree >= 80 && $customData->total_degree <= 89) Merit
            @elseif ($customData->total_degree >= 90 && $customData->total_degree <= 95) High Merit
            @elseif ($customData->total_degree >= 96 && $customData->total_degree <= 100) Distinction
            @else ---
            @endif
        </div>

        <!-- رقم المرجع -->
        <div class="ref-value">{{ $customData->cer_code }}</div>
    </div>
</body>

</html>
