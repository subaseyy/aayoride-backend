@extends('adminmodule::layouts.master')

@section('title', translate('deleted_employee_list'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <h2 class="fs-22 text-capitalize mb-3">{{ translate('deleted_employee_list') }}</h2>

            <div class="d-flex flex-wrap justify-content-end align-items-center my-3 gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-capitalize">{{ translate('total_employees') }}:</span>
                    <span class="text-primary fs-16 fw-bold">{{ $employees->total() }}</span>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-body">

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
                                            <th class="status">{{ translate('status') }}</th>
                                            <th class="text-center action">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($employees as $key => $employee)
                                            <tr id="hide-row-{{ $employee->id }}">
                                                <td class="sl">{{ $key + $employees->firstItem() }}</td>
                                                <td class="employee_name">
                                                    <div class="media gap-3 align-items-center">
                                                        <div class="avatar">
                                                            <img src="{{ onErrorImage(
                                                                $employee?->profile_image,
                                                                asset('storage/app/public/employee/profile') . '/' . $employee?->profile_image,
                                                                asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                'employee/profile/',
                                                            ) }}"
                                                                class="dark-support fit-object" alt="">
                                                        </div>
                                                        <div class="meida-body">
                                                            {{ $employee?->first_name . ' ' . $employee?->last_name }}</div>
                                                    </div>
                                                </td>
                                                <td class="employee_position">{{ $employee?->role?->name }}</td>
                                                <td class="module_access">
                                                    <div class="max-w300 text-wrap text-capitalize">
                                                        @php($comma = '')
                                                        @if ($employee?->role && count($employee?->role?->modules)> 0)
                                                        @foreach ($employee?->role?->modules as $module)
                                                        {{ $comma . translate($module) }}
                                                        @php($comma = ', ')
                                                    @endforeach
                                                        @endif

                                                    </div>
                                                </td>
                                                <td class="status">
                                                    <label class="switcher">
                                                        <input class="switcher_input status-change" type="checkbox"
                                                            {{ $employee->is_active == 1 ? 'checked' : '' }}
                                                            data-url="{{ route('admin.employee.update-status') }}"
                                                            id="{{ $employee->id }}">
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </td>
                                                <td class="action">
                                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                                        <button
                                                            data-route="{{ route('admin.employee.restore', ['id' => $employee->id]) }}"
                                                            data-message="{{ translate('Want_to_recover_this_employee?_') . translate('if_yes,_this_employee_will_be_available_again_in_the_Employee_List') }}"
                                                            class="btn btn-outline-primary btn-action restore-data">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">
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
