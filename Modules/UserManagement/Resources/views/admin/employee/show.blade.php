@extends('adminmodule::layouts.master')

@section('title', translate('employee_details'))

@section('content')

    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-3 text-capitalize">{{ translate('employee_details') }}</h2>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-3 mb-4">
                        <h4 class="text-primary">#EMP {{ $employee->id }}</h4>

                        <label class="switcher">
                            <input class="switcher_input status-change" type="checkbox"
                                {{ $employee->is_active == 1 ? 'checked' : '' }}
                                data-url="{{ route('admin.employee.update-status') }}" id="{{ $employee->id }}">
                            <span class="switcher_control"></span>
                        </label>
                    </div>

                    <form>
                        <div class="row gy-3">
                            <div class="col-lg-8">
                                <div class="media flex-wrap gap-xl-5 gap-4">
                                    <img src="{{ onErrorImage(
                                        $employee?->profile_image,
                                        asset('storage/app/public/employee/profile') . '/' . $employee?->profile_image,
                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                        'employee/profile/',
                                    ) }}"
                                        class="dark-support custom-box-size" alt="" style="--size: 260px">
                                    <div class="media-body">
                                        <h4 class="mb-2">{{ $employee?->first_name . ' ' . $employee?->last_name }}</h4>
                                        <div class="fs-12 fw-medium text-primary mb-4">{{ $employee?->role?->name }}</div>
                                        <ul class="list-info">
                                            <li class="align-items-center">
                                                <i class="bi bi-person-badge text-primary"></i>
                                                ID: #{{ $employee->id }}
                                            </li>
                                            <li class="align-items-center">
                                                <i class="bi bi-phone text-primary"></i>
                                                <a href="tel:880372786552">{{ $employee->phone }}</a>
                                            </li>
                                            <li class="align-items-center">
                                                <i class="bi bi-envelope text-primary"></i>
                                                <a href="mailto:example@email.com">{{ $employee->email }}</a>
                                            </li>
                                            @php($employeeAddress = count($employee?->addresses) > 0 ? $employee?->addresses[0]->address : null)
                                            <li class="align-items-center">
                                                <i class="bi bi-map text-primary"></i>
                                                {{ $employeeAddress ?? 'Not Found' }}
                                            </li>
                                            <li class="align-items-center">
                                                <i class="bi bi-credit-card text-primary"></i>
                                                {{ str_replace('_', ' ', $employee->identification_type) }}
                                                - {{ $employee->identification_number }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="p-3 bg-light rounded-10">
                                    <div class="card border-0 mb-3">
                                        @php($time_format = getSession('time_format'))
                                        <div class="card-body d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-week text-primary"></i>
                                            Join: {{ date(DATE_FORMAT, strtotime($employee->created_at)) }}
                                        </div>
                                    </div>
                                    <div class="card border-0">
                                        <div class="card-body">
                                            <div
                                                class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-person-lines-fill text-primary"></i> Access
                                                    Available :
                                                </div>
                                                <a href="{{ route('admin.employee.edit', ['id' => $employee->id]) }}">
                                                    <i class="bi bi-pencil-square text-primary cursor-pointer"></i>
                                                </a>
                                            </div>
                                            <div class="d-flex gap-2 flex-wrap">
                                                @if($employee?->role)
                                                    @forelse($employee->moduleAccess as $key => $module)
                                                        <div class="badge badge-primary">
                                                            {{ translate($module->module_name) }}</div>
                                                    @empty
                                                    @endforelse
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <a type="text" href="{{ route('admin.employee.edit', [$employee->id]) }}"
                                        class="btn btn-primary">{{ translate('edit') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-end mt-4 mb-3 gap-3">
                <h2 class="fs-22">Activity Log</h2>

                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted">Total Activity Log:</span>
                    <span class="text-primary fs-16 fw-bold">{{ $logs->count() }}</span>
                </div>
            </div>

            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                        <form action="javascript:;" method="GET" class="search-form search-form_style-two">
                            <div class="input-group search-form__input_group">
                                <span class="search-form__icon">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" name="search" id="search"
                                    class="theme-input-style search-form__input"
                                    placeholder="{{ translate('Search_Here') }}">
                            </div>
                            <button type="submit" class="btn btn-primary search-submit"
                                data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                        </form>


                        <div class="d-flex flex-wrap gap-3">
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    {{ translate('download') }}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><a class="dropdown-item" target="_blank"
                                            href="{{ route('admin.employee.log') }}?id={{ $employee->id }}&&file=excel">{{ translate('excel') }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div id="log_data">
                        <div class="table-responsive mt-3">

                            <table class="table table-borderless align-middle">
                                <thead class="table-light align-middle">
                                    <tr>
                                        <th class="text-capitalize">{{ translate('edited_date') }}</th>
                                        <th class="text-capitalize">{{ translate('edited_time') }}</th>
                                        <th class="text-capitalize">{{ translate('edited_by') }}</th>
                                        <th class="text-capitalize">{{ translate('edited_object') }}</th>
                                        <th class="text-capitalize">{{ translate('before_edit_status') }}</th>
                                        <th class="text-capitalize">{{ translate('after_edit_status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ date('Y-m-d', strtotime($log->created_at)) }}</td>
                                            <td>{{ date('h:i A', strtotime($log->created_at)) }}</td>
                                            <td>{{ $log->users?->email }}</td>
                                            @php($objects = explode('\\', $log->logable_type))
                                            <td>{{ end($objects) }}</td>
                                            <td>
                                                @if ($log->before)
                                                    @foreach ($log->before as $key => $before)
                                                        <?php $before = gettype($before) == 'array' ? json_encode($before) : $before; ?>
                                                        {{ $key . ' : ' . $before }} <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if ($log->after)
                                                    @foreach ($log->after as $key => $after)
                                                        <?php $after = gettype($after) == 'array' ? json_encode($after) : $after; ?>
                                                        {{ $key . ' : ' . $after }} <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <td colspan="6">
                                            <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                                                <p class="text-center">{{translate('no_data_available')}}</p>
                                            </div>
                                        </td>
                                    @endforelse

                                </tbody>
                            </table>

                        </div>

                        <div
                            class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                            <p class="mb-0"></p>

                            <div
                                class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                {!! $logs->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
