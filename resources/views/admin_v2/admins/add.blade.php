@extends('admin.layout.master')
@section('title')
    {{ $current_route->{'name_' . trans('app.lang')} }}
@stop
@section('page-breadcrumb')
    <li class="breadcrumb-item text-muted">
        <a href="{{ url('/') }}" class="text-muted text-hover-primary">@lang('app.home')</a>
    </li>
    <li class="breadcrumb-item text-muted">- {{ $current_route->{'name_' . trans('app.lang')} }}</li>
@stop
@section('page-content')
    <div class="card">
        <div class="card-body py-4">
            @include('admin.layout.error')
            <form action="" method="POST">
                <div class="row justify-content-center">
                    <div class="col-9">
                        <div class="form-floating mb-9 row ">
                            <div class="col">
                                <label class="p-2">@lang('app.full_name')<span>*</span></label>
                                <input type="text" value="{{ $info ? $info->name : old('name') }}" name="name"
                                    class="form-control" />
                            </div>
                            <div class="col">
                                <label class="p-2">@lang('app.username')<span>*</span></label>
                                <input type="text" value="{{ $info ? $info->username : old('username') }}"
                                    name="username" class="form-control" />
                            </div>
                        </div>
                        @if (isset($info) == null)
                        <div class="form-floating mb-9 row ">
                            <div class="col">
                                <label class="p-2"> @lang('app.password') <span>*</span></label>
                                <input type="password" value="{{ $info ? $info->password : old('password') }}"
                                    name="password" class="form-control" />
                            </div>
                            <div class="col">
                                <label class="p-2"> @lang('app.change-password') <span>*</span></label>
                                <input type="password"
                                    value="{{ $info ? $info->password_confirmation : old('password_confirmation') }}"
                                    name="password_confirmation" class="form-control" />
                            </div>
                        </div>
                        @endif
                        <div class="form-floating mb-9 row ">
                            <div class="col">
                                <label class="p-2"> @lang('app.email') <span>*</span></label>
                                <input type="text" placeholder="example@domain.com"
                                    value="{{ $info ? $info->email : old('email') }}" name="email" class="form-control" />
                            </div>
                            <div class="col">
                                <label class="p-2"> @lang('app.group') <span>*</span></label>
                                <select class="form-select" aria-label="Select example" name="role">
                                    <option>@lang('app.choose')</option>
                                     <?php $data = $info ? $info->role : old('role'); ?>
                                    @foreach ($roles as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $data == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-floating mb row">
                            <div class="col">
                                <label class="p-2">@lang('app.status')</label>
                                <label class="form-check form-switch">
                                    <?php $data = $info ? $info->status : old('status'); ?>
                                    <input class="form-check-input" name="status" type="checkbox" value="1"
                                        {{ $data == 1 ? 'checked="checked"' : '' }}>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center pt-2">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">@lang('app.save') </button>
                    <a type="reset" href="{{ route($active_menu . '.view') }}" class="btn btn-light me-3">@lang('app.cancel')</a>
                </div>
            </form>
        </div>
    </div>
@stop
