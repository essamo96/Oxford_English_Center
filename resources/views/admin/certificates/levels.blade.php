<!DOCTYPE html>
<html>

<head>
    <title>Your site's title should be here</title>
    <meta charset="UTF-8">
    <meta name="description" content="Your site's description should be here">
    <meta name="keywords" content="Your site's keywords should be here">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Aguafina+Script:regular&amp;subset=latin,latin-ext">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;900&family=Maven+Pro:wght@400;500;700&family=Shantell+Sans:ital,wght@0,300;0,400;0,500;1,300;1,600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* width: 100%; */
        }
        
        .certificate {
            font-family: 'adobemingstd-light';
            position: relative;
            width: 850px;
            height: 1200px;
            background-image: url('/Admin_Certifcate/levels_Admin.png');
            background-size: cover;
            text-align: center;
        }

        .content {
            font-family: 'adobemingstd-light';
            position: absolute;
            top: 520px;
            left: 290px;
             font-size: 14px;
            /* transform: translate(-50%, -50%); */
        }

        .content2 {
            position: absolute;
            top: 629px;
            left: 370px;
            font-size: 14px;
            font-family: 'adobemingstd-light';
            /* transform: translate(-50%, -50%); */
        }
        
        .content3 {
            font-family: 'adobemingstd-light';
            position: absolute;
            top: 660px;
            left: 363px;
             font-size: 14px;
            /* transform: translate(-50%, -50%); */
        }

        .content4 {
            font-family: 'adobemingstd-light';
            position: absolute;
            top: 678px;
            left: 363px;
             font-size: 14px;
             
             
             /* transform: translate(-50%, -50%); */
            }
            
            .content5 {
            font-family: 'adobemingstd-light';
            position: absolute;
            top: 677px;
            left: 363px;
            font-size: 14px;
                     

            /* transform: translate(-50%, -50%); */
        }
        /* span{
            font-size: 16px;
        } */
        .content6 {
            position: absolute;
            top: 692px;
            left: 363px;
            /* transform: translate(-50%, -50%); */
            font-family: 'adobemingstd-light';
        }
        .content7 {
            position: absolute;
            top: 734px;
            left: 358px;
            font-family: 'adobemingstd-light';
            /* transform: translate(-50%, -50%); */
        }
        .content8 {
            position: absolute;
            top: 753px;
            left: 358px;
            font-family: 'adobemingstd-light';
            /* transform: translate(-50%, -50%); */
        }
        .content9 {
            font-family: 'adobemingstd-light';
            position: absolute;
            top: 769px;
            left: 215px;
            /* transform: translate(-50%, -50%); */
        }

        .title {
            position: absolute;
            /* min-width: 2086px; */
            color: #010101;
            font-family: 'aguafinascript';
            /* font-family: 'Aguafina Script', handwriting; */
            font-size: 2rem;
            font-style: normal;
            font-weight: normal;
            letter-spacing: 0;
            text-decoration: none;
            top: -500px;
            left: 590px;
            /* margin-bottom: 48px;
            margin-top: 48px; */

        }
        * {

            /* font-family: 'adobemingstd-lightTPro'; */
            font-family: 'flingstdregular';
        }

        .name {
            font-family: 'flingstdregular';
            justify-content: center;
            display: flex;
            font-size: 16px;
            left: 71px;
            position: relative;
            top: 34px;
            margin-inline: 10px;
        }
        
        .name2 {
            font-family: 'flingstdregular';
            position: relative;
            top: 724px;
            left: 25rem;
            text-align: justify;
            margin-bottom: 4px;
        }
        
        .levels {
            font-family: 'adobemingstd-light';
            margin-right: 10px;
            color: #bd1e20;
            FONT-STYLE: italic;
            font-size: 14px;
            
        }
        
        .description {
            font-family: 'adobemingstd-light';
            font-size: 18px;
        }
        strong{
            font-family: 'adobemingstd-light';
            font-size: 18px;
        }
        </style>
</head>

<body>


    <div style="margin:80%">

    </div>
    <div class="content" style="float:left">
        <div class="title">{{ $formattedName }} </div>

        {{-- <p style="font-family: 'Maven Pro', sans-serif; font-size: 55px;">10-10-1999 to 20-10-1199<br> 3
                <br>10<br>60h
            </p> --}}
    </div>
    {{-- {{ $customData->group->name }} --}}

    <div class="content2" style="display: block">
        <strong>
            <div class="name"><span class="levels">{{ $firstPart }}</span> | @if($firstPart == 'A1')
    Beginner
        @elseif($firstPart == 'A2')
            Elementary
        @elseif($firstPart == 'B1')
            Preintermediate
        @elseif($firstPart == 'B1plus')
            Intermediate
        @elseif($firstPart == 'B2')
            Upper Intermediate
        @elseif($firstPart == 'C1')
            Advanced
        @elseif($firstPart == 'C2')
            Proficient
        @endif
 </div>
        </strong>
    </div>
    <div class="content3" style="display: block">
        <span>{{ \Carbon\Carbon::parse($customData->group->start_date)->format('d / m / Y') }} To
            {{ \Carbon\Carbon::parse($customData->group->end_date)->format('d / m / Y') }}</span>
    </div>
    <div class="content4" style="display: block">
        <span>3</span>
    </div>
    <div class="content5"  style="display: block">
       &nbsp; 12
    </div>
    <div class="content6"  style="display: block">
      &nbsp; 72
    </div>
    <div class="content7"  style="display: block">
      &nbsp; {{$customData->total_degree}} %
    </div>
    <div class="content9"  style="display: block">
      &nbsp; {{$customData->cer_code}} 
    </div>
    <div class="content8"  style="display: block">
      &nbsp;  @if ($customData->total_degree >= 70 && $customData->total_degree <= 79)
                    Pass
                @elseif ($customData->total_degree >= 80 && $customData->total_degree <= 89)
                    Merit
                @elseif ($customData->total_degree >= 90 && $customData->total_degree <= 95)
                    High Merit
                @elseif ($customData->total_degree >= 96 && $customData->total_degree <= 100)
                    Distinction
                    @else
                    ---
                @endif
    </div>
    {{-- <div class="content7"  style="display: block">
      &nbsp; 72
    </div> --}}






    <div class="certificate">
        {{-- <div class="name2">
            <span>{{\Carbon\Carbon::parse($customData->group->start_date)->format('d / m / Y')}} To {{\Carbon\Carbon::parse($customData->group->end_date)->format('d / m / Y')}}</span>
         </div> 
        <div class="name2">
            <span>3</span>
         </div> 
        <div class="name2">
            <span>12</span>
         </div> 
        <div class="name2">
            <span>72 Hours</span>
         </div> 
        <div class="name2">
            <span>{{$customData->total_degree}} % </span>
         </div> 
        <div class="name2">
              <span>
                @if ($customData->total_degree >= 70 && $customData->total_degree <= 79)
                    Pass
                @elseif ($customData->total_degree >= 80 && $customData->total_degree <= 89)
                    Merit
                @elseif ($customData->total_degree >= 90 && $customData->total_degree <= 95)
                    High Merit
                @elseif ($customData->total_degree >= 96 && $customData->total_degree <= 100)
                    Distinction
                    @else
                    ---
                @endif
            </span>
         </div>  --}}


    </div>
</body>

</html>
