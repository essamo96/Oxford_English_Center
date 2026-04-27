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
<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card">
                <div class="card-body py-4">
                    @include('admin.layout.error')
                    <form id="kt_modal_add_user_form" class="form" action="" method="POST">
                        <label class="fs-5 fw-bold form-label mb-2">@lang('app.choose_permissions')</label>
                        @foreach($permission_group as $row)
                        <div class="form-group row mb-10">
                            <label class="form-label"><b>{{ $row->{'name_' . trans('app.lang')} }}:</b></label>
                            <div class="kt-checkbox-inline">
                                @foreach($row->permissions as $item)
                                <div class="form-check form-check-inline">
                                    <input name="permissions[]" class="form-check-input" type="checkbox" value="{{ $item->id }}" id="prm{{ $item->id }}" {{ in_array($item->id,array_column ($role_permissions,'permission_id')) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="prm{{ $item->id }}">
                                        {{ trans('permissions.'.$item->name) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                        <div class="text-center pt-2">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">@lang('app.save') </button>
                    <a type="reset" href="{{ route($active_menu . '.view') }}" class="btn btn-light me-3">@lang('app.cancel')</a>
                </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop