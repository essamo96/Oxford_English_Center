@if(Session::has('success'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">Close</span>
                </button>
                @if(is_object(Session::get('success')))
                    @foreach (Session::get('success')->all(':message') as $message)
                        {{ $message }}
                    @endforeach
                @else
                    {{ Session::get('success') }}
                @endif
            </div>
        </div>
    </div>
@elseif(Session::has('danger'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-danger">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">Close</span>
                </button>

                @if(is_object(Session::get('danger')))
                    @foreach (Session::get('danger')->all(':message') as $message)
                        {{ $message }}
                        <br>
                    @endforeach
                @else
                    {{ Session::get('danger') }}
                @endif
            </div>
        </div>
    </div>
@elseif(Session::has('warning'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">Close</span>
                </button>

                @if(is_object(Session::get('warning')))
                    @foreach (Session::get('warning')->all(':message') as $message)
                        {{ $message }}
                    @endforeach
                @else
                    {{ Session::get('warning') }}
                @endif
            </div>
        </div>
    </div>
@elseif(Session::has('info'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-info">
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">Close</span>
                </button>

                @if(is_object(Session::get('info')))
                    @foreach (Session::get('info')->all(':message') as $message)
                        {{ $message }}
                    @endforeach
                @else
                    {{ Session::get('info') }}
                @endif
            </div>
        </div>
    </div>
@endif