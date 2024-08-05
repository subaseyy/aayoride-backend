@extends('adminmodule::layouts.master')

@section('title', translate('Employee_List'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <h2 class="fs-22 text-capitalize">{{ translate('employee_list') }}</h2>

            <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                    <li class="nav-item">
                        <a href="{{ url()->current() }}?status=all"
                           class="nav-link {{ !request()->has('status') || request()->get('status') =='all' ? 'active' : '' }}">{{ translate('all') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url()->current() }}?status=active"
                           class="nav-link {{ request()->get('status') === 'active' ? 'active' : '' }}">{{ translate('active') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url()->current() }}?status=inactive"
                           class="nav-link {{ request()->get('status') === 'inactive' ? 'active' : '' }}">{{ translate('inactive') }}</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-capitalize">{{ translate('total_employees') }}:</span>
                    <span class="text-primary fs-16 fw-bold" id="total_record_count">{{ $employees->total() }}</span>
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
                                        <input type="search" name="search" value="{{ request()->get('search') }}"
                                               id="search"
                                               class="theme-input-style search-form__input"
                                               placeholder="{{ translate('Search_Here_by_Employee_Name') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary search-submit"
                                            data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                </form>

                                <div class="d-flex flex-wrap gap-3">
                                    @can('super-admin')
                                        <a href="{{ route('admin.employee.index', ['status' => request('status')]) }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                           data-bs-title="{{ translate('refresh') }}">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>

                                        <a href="{{ route('admin.employee.trash') }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                           data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                            <i class="bi bi-recycle"></i>
                                        </a>
                                    @endcan
                                    @can('user_log')
                                        <a href="{{ route('admin.employee.log') }}" class="btn btn-outline-primary px-3"
                                           data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                            <i class="bi bi-clock-fill"></i>
                                        </a>
                                    @endcan

                                    @can('user_export')
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-download"></i>
                                                {{ translate('download') }}
                                                <i class="bi bi-caret-down-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                <li><a class="dropdown-item"
                                                       href="{{ route('admin.employee.export') }}?status={{ request()->get('status') ?? "all" }}&search={{ request()->get('search') }}&file=excel">{{ translate('excel') }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endcan
                                    @can('user_add')
                                        <a href="{{ route('admin.employee.create') }}" type="button"
                                           class="btn btn-primary text-capitalize">
                                            <i class="bi bi-plus fs-16"></i> {{ translate('add_employee') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle">
                                    <thead class="table-light align-middle">
                                    <tr>
                                        <th class="sl">{{ translate('SL') }}</th>
                                        <th class="employee_name text-capitalize">{{ translate('employee_name') }}
                                        </th>
                                        <th class="employee_position text-capitalize">
                                            {{ translate('employee_position') }}</th>
                                        <th class="module_access text-capitalize">{{ translate('module_access') }}
                                        </th>
                                        @can('user_edit')
                                            <th class="status">{{ translate('status') }}</th>
                                        @endcan
                                        <th class="text-center action">{{ translate('action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse($employees as $key => $employee)

                                        <tr id="hide-row-{{ $employee->id }}" class="record-row">
                                            <td class="sl">{{ $key + $employees->firstItem() }}</td>
                                            <td class="employee_name">
                                                <div class="media gap-3 align-items-center">
                                                    <div class="custom-box-size" style="--size: 36px">
                                                        <img src="{{ onErrorImage(
                                                                    $employee?->profile_image,
                                                                    asset('storage/app/public/employee/profile') . '/' . $employee?->profile_image,
                                                                    asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                    'employee/profile/',
                                                                ) }}"
                                                             class="dark-support fit-object" alt="">
                                                    </div>
                                                    <div class="media-body">
                                                        {{ $employee?->first_name . ' ' . $employee?->last_name }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="employee_position">{{ $employee?->role?->name }}</td>
                                            <td class="module_access">
                                                <div class="max-w300 text-wrap text-capitalize">
                                                    @php($comma = '')
                                                    @foreach ($employee?->moduleAccess as $module)
                                                        {{ $comma . translate($module?->module_name) }}
                                                        @php($comma = ', ')
                                                    @endforeach
                                                </div>
                                            </td>
                                            @can('user_edit')
                                                <td class="status">
                                                    <label class="switcher">
                                                        <input class="switcher_input status-change" type="checkbox"
                                                               {{ $employee->is_active == 1 ? 'checked' : '' }}
                                                               data-url="{{ route('admin.employee.update-status') }}"
                                                               id="{{ $employee->id }}">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </td>
                                            @endcan
                                            <td class="action">
                                                <div class="d-flex justify-content-center gap-2 align-items-center">
                                                    @can('user_log')
                                                        <a type="button" class="btn btn-outline-primary btn-action"
                                                           href="{{ route('admin.employee.log') }}?id={{ $employee->id }}">
                                                            <i class="bi bi-clock-fill"></i>
                                                        </a>
                                                    @endcan
                                                    @can('user_edit')
                                                        <a href="{{ route('admin.employee.edit', ['id' => $employee->id]) }}"
                                                           class="btn btn-outline-info btn-action">
                                                            <i class="bi bi-pen"></i>
                                                        </a>
                                                    @endcan
                                                    @can('user_view')
                                                        <a href="{{ route('admin.employee.show',$employee->id) }}"
                                                           class="btn btn-outline-info btn-action">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                    @endcan
                                                    @can('user_delete')
                                                        <button data-id="delete-{{ $employee->id }}"
                                                                data-message="{{ translate('want_to_delete_this_employee?') }}"
                                                                type="button"
                                                                class="btn btn-outline-danger btn-action form-alert">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                        <form
                                                            action="{{ route('admin.employee.delete', ['id' => $employee->id]) }}"
                                                            method="post" id="delete-{{ $employee->id }}">
                                                            @csrf
                                                            @method('delete')
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14">
                                                <div
                                                    class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                    <img
                                                        src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}"
                                                        alt="" width="100">
                                                    <p class="text-center">{{translate('no_data_available')}}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div
                                class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                                <p class="mb-0"></p>

                                <div
                                    class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                    <nav>
                                        {!! $employees->links() !!}
                                    </nav>
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
