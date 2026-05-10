@php
    $type = '';
    $icon = '';
    $message = '';
    if(Session::has('success')) {
        $type = 'success';
        $icon = 'fa-check-circle';
        $message = Session::get('success');
    } elseif(Session::has('danger')) {
        $type = 'danger';
        $icon = 'fa-times-circle';
        $message = Session::get('danger');
    } elseif(Session::has('warning')) {
        $type = 'warning';
        $icon = 'fa-exclamation-triangle';
        $message = Session::get('warning');
    } elseif(Session::has('info')) {
        $type = 'info';
        $icon = 'fa-info-circle';
        $message = Session::get('info');
    }
@endphp

@if($type)
    <div class="row">
        <div class="col-sm-12">
            <div class="modern-alert alert-{{ $type }} animate__animated animate__fadeInDown">
                <div class="alert-icon">
                    <i class="fa {{ $icon }}"></i>
                </div>
                <div class="alert-content">
                    @if(is_object($message))
                        @foreach ($message->all(':message') as $m)
                            <div class="alert-text">{{ $m }}</div>
                        @endforeach
                    @else
                        <div class="alert-text">{{ $message }}</div>
                    @endif
                </div>
                <button type="button" class="close-alert" onclick="this.parentElement.style.display='none'">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        .modern-alert {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            z-index: 10;
        }
        .modern-alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
        }
        .alert-success { background: #e6fffa; color: #234e52; }
        .alert-success::before { background: #38b2ac; }
        .alert-success .alert-icon { color: #38b2ac; }
        
        .alert-danger { background: #fff5f5; color: #742a2a; }
        .alert-danger::before { background: #f56565; }
        .alert-danger .alert-icon { color: #f56565; }
        
        .alert-warning { background: #fffaf0; color: #7b341e; }
        .alert-warning::before { background: #ed8936; }
        .alert-warning .alert-icon { color: #ed8936; }
        
        .alert-info { background: #ebf8ff; color: #2a4365; }
        .alert-info::before { background: #4299e1; }
        .alert-info .alert-icon { color: #4299e1; }

        .alert-icon {
            font-size: 24px;
            margin-right: 15px;
            display: flex;
            align-items: center;
        }
        .alert-content {
            flex-grow: 1;
        }
        .alert-text {
            font-size: 14px;
            font-weight: 600;
        }
        .close-alert {
            background: transparent;
            border: none;
            font-size: 18px;
            cursor: pointer;
            opacity: 0.5;
            transition: 0.3s;
            padding: 5px;
            margin-left: 10px;
        }
        .close-alert:hover {
            opacity: 1;
        }
    </style>
@endif