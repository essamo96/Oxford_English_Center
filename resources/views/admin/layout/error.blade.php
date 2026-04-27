@if (session('type') == 'success')
    <script>
        Swal.fire({
            position: 'top-end',
            icon: '{{ session('type') }}',
            title: '{{ session('type') }}',
            showConfirmButton: false,
            text: '{{ session('message') }}',
            timer: 2500
        })
    </script>
@elseif(session('type') == 'danger')
    <script>
        Swal.fire({
            icon: '{{ session('icon') }}',
            title: '{{ session('type') == 'danger' ? 'danger!' : 'warning!' }}',
            text: '{!! session('message') !!}',
            confirmButtonText: 'OK'
        })
    </script>
@endif

@if (Session::has('success'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-dismissible bg-light-success border border-success border-dashed d-flex flex-column flex-sm-row p-5 mb-10 align-items-center">
                <i class="ki-duotone ki-check-circle fs-2hx text-success me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <span class="fw-semibold text-gray-800">
                        @if (is_object(Session::get('success')))
                            @foreach (Session::get('success')->all(':message') as $message)
                                {{ $message }}<br>
                            @endforeach
                        @else
                            {{ Session::get('success') }}
                        @endif
                    </span>
                </div>
                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
    </div>
@elseif(Session::has('errors'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-5 mb-10 align-items-center">
                <i class="ki-duotone ki-information-5 fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <span class="fw-semibold text-gray-800">
                        @if (is_object(Session::get('errors')))
                            <ul class="mb-0">
                            @foreach (Session::get('errors')->all(':message') as $message)
                                <li>{!! $message !!}</li>
                            @endforeach
                            </ul>
                        @else
                            {{ Session::get('errors') }}
                        @endif
                    </span>
                </div>
                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
    </div>
@elseif(Session::has('danger'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-dismissible bg-light-danger border border-danger border-dashed d-flex flex-column flex-sm-row p-5 mb-10 align-items-center">
                <i class="ki-duotone ki-information-4 fs-2hx text-danger me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <span class="fw-semibold text-gray-800">
                        @if (is_object(Session::get('danger')))
                            <ul class="mb-0">
                            @foreach (Session::get('danger')->all(':message') as $message)
                                <li>{!! $message !!}</li>
                            @endforeach
                            </ul>
                        @else
                            {{ Session::get('danger') }}
                        @endif
                    </span>
                </div>
                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
    </div>
@elseif(Session::has('warning'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-5 mb-10 align-items-center">
                <i class="ki-duotone ki-warning-2 fs-2hx text-warning me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <span class="fw-semibold text-gray-800">
                        @if (is_object(Session::get('warning')))
                            @foreach (Session::get('warning')->all(':message') as $message)
                                {!! $message !!}<br>
                            @endforeach
                        @else
                            {{ Session::get('warning') }}
                        @endif
                    </span>
                </div>
                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-warning"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
    </div>
@elseif(Session::has('info'))
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-dismissible bg-light-info border border-info border-dashed d-flex flex-column flex-sm-row p-5 mb-10 align-items-center">
                <i class="ki-duotone ki-message-text-2 fs-2hx text-info me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                <div class="d-flex flex-column pe-0 pe-sm-10">
                    <span class="fw-semibold text-gray-800">
                        @if (is_object(Session::get('info')))
                            @foreach (Session::get('info')->all(':message') as $message)
                                {{ $message }}<br>
                            @endforeach
                        @else
                            {{ Session::get('info') }}
                        @endif
                    </span>
                </div>
                <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                    <i class="ki-duotone ki-cross fs-1 text-info"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
        </div>
    </div>
@endif