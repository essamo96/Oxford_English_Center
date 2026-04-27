<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ asset('assets/progress.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <script src="{{ asset('assets/progress.js') }}" defer></script>
    <title>Grope - Progress</title>
    <style>
        body {
            min-height: 60vh;
        }

        .form {
            display: flex;
            width: 80%;
        }

        .btns-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 30px;
        }

        .progressbar {
            position: relative;
            display: flex;
            justify-content: space-between;
            counter-reset: step;
            margin: 1rem 0 3rem;
            margin-left: 53px;
            width: 40%;
        }
        #back{
            margin-top: 19px;
    margin-left: 44px;
        }
         #alert-code,
        #go-back {
            border-radius: 10px;
            font-size: 15px;
            direction: rtl;
            background-color: #002147;
            color: #fdc800;
            margin-left: 512px;
        }
        .title-default-left {
    text-transform: capitalize;
    text-align: left;
    font-weight: 500;
    color: #002147;
    margin-left: 550px;
    margin-bottom: 18px;

}
h3{
            position: relative;
        font-size: 24px;
            font-family: 'Roboto', sans-serif;
    line-height: 1.5;
    font-weight: 400;
      margin: 12px -13px -26px -11px;
    color: #ffa600;
    margin-top: 54px;
    left: -616px;
}
    </style>
    


</head>
{{-- <div><h1 class="text-center" style="color:#ffa600">Grope - Progress<a id="go-back" href="/teacher"
                                class="btn btn-success btn-sm"> back
                                <i class="bi bi-skip-backward-fill"></i></a></h1></div> --}}
 
<body>
    <h3 class="title-section title-bar-high mb-40">Grope - Progress </h3><a id="go-back" href="/teacher"
                                class="btn btn-success btn-sm"> back
                                <i class="bi bi-skip-backward-fill"></i></a>

    @foreach ($info as $progress)

        <form action="#" class="form">

            <h2 class="text-center">{{ $progress->name }}</h2>
            <!-- Progress bar -->
            <div class="progressbar" >
                <div class="progress" id="progress" style="width:{{ $progress->progress}}%"></div>
                <div class="progress-step @if ($progress->progress == 30 || $progress->progress == null) progress-step-active  @else ' ' @endif"
                    data-title="Unit1"></div>
                <div class="progress-step @if ($progress->progress == 30) progress-step-active @else ' ' @endif"
                    data-title="Unit2"></div>
                <div class="progress-step @if ($progress->progress == 30) progress-step-active @else ' ' @endif"
                    data-title="Unit3"></div>
                <div class="progress-step @if ($progress->progress == 60) progress-step-active @else ' ' @endif"
                    data-title="Unit4"></div>
                <div class="progress-step @if ($progress->progress == 60) progress-step-active @else ' ' @endif"
                    data-title="Unit5"></div>
                <div class="progress-step @if ($progress->progress == 60) progress-step-active @else ' ' @endif"
                    data-title="Unit6"></div>
                <div class="progress-step @if ($progress->progress == 90) progress-step-active @else ' ' @endif"
                    data-title="Unit7"></div>
                <div class="progress-step @if ($progress->progress == 90) progress-step-active @else ' ' @endif"
                    data-title="Unit8"></div>
                <div class="progress-step @if ($progress->progress == 90) progress-step-active @else ' ' @endif"
                    data-title="Unit9"></div>
                <div class="progress-step @if ($progress->progress == 100) progress-step-active @else ' ' @endif"
                    data-title="Unit10"></div>
            </div>
            <div><a class="btn btn-primary btn-sm" href="{{route('teacher.showGroueStudents',['group_id' =>Crypt::encrypt($progress->id) , 'teacher_id'=>Crypt::encrypt($progress->teacher_id)]) }}" id="back" >Go</a></div>
            
        </form>

    @endforeach
       
</body>

</html>
