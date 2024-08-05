@extends('adminmodule::layouts.master')

@section('title', translate('Employee_Attributes'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center gap-3 justify-content-between mb-3">
                <h2 class="fs-22">{{translate('Employee_Attributes')}}</h2>
            </div>

            <div class="card">
                <form id="form_data" action="{{route('admin.employee.role.store')}}" method="post">
                    @csrf
                    <div class="card-body">
                        <h6 class="fw-semibold text-primary text-uppercase mb-4">{{translate('add_new_role')}}</h6>

                        <div class="mb-4">
                            <label for="role-name" class="mb-2">{{translate('role_name')}}</label>
                            <input type="text" id="role-name" name="name" class="form-control"
                                   placeholder="{{translate('Ex: Business Analyst')}}" required>
                        </div>

                        <h6 class="fw-medium mt-5 mb-3 text-capitalize">{{translate('available_modules')}}</h6>
                        <div class="row">
                            <div class="col-2 pb-2">
                                <label class="custom-checkbox">
                                    <input type="checkbox" id="select-all-modules">
                                    {{translate('Select_All')}}
                                </label>
                            </div>
                            @foreach(MODULES as $key => $module)
                                <div class="col-2 pb-2">
                                    <label class="custom-checkbox">
                                        <input type="checkbox" class="module-checkbox" name="modules[]" value="{{$key}}">
                                        {{translate($key)}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @can('user_add')
                            <div class="d-flex gap-3 flex-wrap justify-content-end mt-5">
                                <button type="submit"
                                        class="btn btn-primary text-uppercase">{{translate('submit')}}</button>
                            </div>
                        @endcan
                    </div>
                </form>
            </div>
            <div class="mt-4">
                <h2 class="fs-22 text-capitalize">{{translate('employee_role_list')}}</h2>

                <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                    <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=all"
                               class="nav-link {{!request()->has('status') || request()->get('status') =='all' ? 'active' : ''}}">{{translate('all')}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=active"
                               class="nav-link {{request()->get('status')=='active'?'active':''}}">{{translate('active')}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=inactive"
                               class="nav-link {{request()->get('status')=='inactive'?'active':''}}">{{translate('inactive')}}</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted text-capitalize">{{translate('total_designation')}}:</span>
                        <span class="text-primary fs-16 fw-bold">{{$roles->total()}}</span>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                    <form action="javascript:;" method="GET" class="search-form search-form_style-two">
                                        <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                            <input type="search" value="{{request()->get('search')}}" id="search"
                                                   class="theme-input-style search-form__input"
                                                   placeholder="{{ translate('Search_here_by_Role_Name') }} ">
                                        </div>
                                        <button type="submit" class="btn btn-primary search-submit"
                                                data-url="{{ url()->full() }}">{{translate('search')}}</button>
                                    </form>

                                    <div class="d-flex flex-wrap gap-3">
                                        @can('user_log')
                                            <a href="{{route('admin.employee.role.log')}}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                                <i class="bi bi-clock-fill"></i>
                                            </a>
                                        @endcan
                                        @can('user_export')
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-outline-primary"
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-download"></i>
                                                    {{translate('download')}}
                                                    <i class="bi bi-caret-down-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                    <li>
                                                        <a class="dropdown-item" target="_blank"
                                                           href="{{route('admin.employee.role.export')}}?file=excel&status={{request()->get('status') ?? "all"}}&search={{request()->get('search')}}">
                                                            {{ translate('Excel') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endcan
                                    </div>
                                </div>

                                <div class="table-responsive mt-3">
                                    <table class="table table-borderless align-middle">
                                        <thead class="table-light align-middle">
                                        <tr>
                                            <th>{{translate('SL')}}</th>
                                            <th class="text-capitalize">{{translate('role_name')}}</th>
                                            <th class="text-capitalize">{{translate('module_access')}}</th>
                                            @can('user_edit')
                                                <th>{{translate('status')}}</th>
                                            @endcan
                                            <th class="text-center">{{translate('action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($roles as $key => $role)
                                                <tr id="hide-row-{{$role->id}}" class="record-row">
                                                    <td>{{$roles->firstItem() + $key}}</td>
                                                    <td>{{$role->name}}</td>
                                                    <td>
                                                        <div class="max-w300 text-wrap text-capitalize">
                                                            @php( $end = array_key_last($role->modules) )
                                                            @foreach($role->modules as $key => $module)
                                                                {{translate($module)}}@if($key != $end)
                                                                    ,
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    @can('user_edit')
                                                        <td>
                                                            <label class="switcher">
                                                                <input class="switcher_input status-change" type="checkbox"
                                                                       data-url="{{route('admin.employee.role.update-status', ['id' => $role->id])}}"
                                                                       id="{{$role->id}}"
                                                                    {{$role->is_active ==1? 'checked' : ''}}>
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    <td>
                                                        <div
                                                            class="d-flex justify-content-center gap-2 align-items-center">
                                                            @can('user_log')
                                                                <a type="button" class="btn btn-outline-primary btn-action"
                                                                   href="{{route('admin.employee.role.log')}}?id={{$role->id}}">
                                                                    <i class="bi bi-clock-fill"></i>
                                                                </a>
                                                            @endcan
                                                            @can('user_edit')
                                                                <a href="{{route('admin.employee.role.edit', ['id' => $role->id])}}"
                                                                   class="btn btn-outline-info btn-action edit_btn">
                                                                    <i class="bi bi-pencil-fill"></i>
                                                                </a>
                                                            @endcan
                                                            @can('user_delete')
                                                                <button type="button"
                                                                        data-id="delete-{{ $role->id }}"
                                                                        data-message="{{ translate('want_to_delete_this_role?') }}"
                                                                        class="btn btn-outline-danger btn-action form-alert">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>
                                                                <form
                                                                    action="{{route('admin.employee.role.delete', ['id' => $role->id])}}"
                                                                    method="post"
                                                                    id="delete-{{$role->id}}">@csrf @method('delete')</form>
                                                            @endcan
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr class="">
                                                    <td colspan="14">
                                                        <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                            <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                                                            <p class="text-center">{{translate('no_data_available')}}</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end">
                                    {{$roles->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script
        src="{{ asset('public/assets/admin-module/js/business-management/system-settings/clean-database.js') }}"></script>
@endpush
