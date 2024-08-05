@section('title', 'Vehicle List')

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('vehicle_view')
                <h2 class="fs-22 mb-3 text-capitalize">{{ translate('vehicle_list') }}</h2>
                <div class="auto-items gap-2">
                    @forelse ($categories as $category)
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-end mb-2">
                                    <img src="{{ onErrorImage(
                                        $category?->image,
                                        asset('storage/app/public/vehicle/category') . '/' . $category?->image,
                                        asset('public/assets/admin-module/img/media/car2.png'),
                                        'vehicle/category/',
                                    ) }}"
                                        class="dark-support custom-box-size" alt="" style="--size: 40px">
                                </div>
                                <h6 class="text-primary mb-2 text-nowrap text-truncate">{{ $category->name }}</h6>
                                <h3 class="fs-27">{{ $category->vehicles->count() }}</h3>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            @endcan

            <div class="row g-4 mt-30">
                <div class="col-12">
                    <h2 class="fs-22 mb-3 text-capitalize">{{ translate('all_vehicles') }}</h2>

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ !request()->has('status') || request()->get('status') == 'all' ? 'active' : '' }}"
                                    href="{{ url()->current() }}?status=all">
                                    {{ translate('all') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request()->get('status') == 'active' ? 'active' : '' }}"
                                    href="{{ url()->current() }}?status=active">
                                    {{ translate('active') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ request()->get('status') == 'inactive' ? 'active' : '' }}"
                                    href="{{ url()->current() }}?status=inactive">
                                    {{ translate('inactive') }}
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_vehicle') }} : </span>
                            <span class="text-primary fs-16 fw-bold"
                                id="total_record_count">{{ $vehicles->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="javascript:;" class="search-form search-form_style-two"
                                            method="GET">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" class="theme-input-style search-form__input"
                                                    value="{{ request()->get('search') }}" name="search" id="search"
                                                    placeholder="{{ translate('search_here_by_Viin_&_License') }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            @can('super-admin')
                                                <a href="{{ route('admin.vehicle.index', ['status' => request('status')]) }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>

{{--                                                <a href="{{ route('admin.vehicle.trashed') }}"--}}
{{--                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">--}}
{{--                                                    <i class="bi bi-recycle"></i>--}}
{{--                                                </a>--}}
                                            @endcan

                                            @can('vehicle_log')
                                                <a href="{{ route('admin.vehicle.log') }}" class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                                    <i class="bi bi-clock-fill"></i>
                                                </a>
                                            @endcan

                                            @can('vehicle_export')
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-outline-primary"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-download"></i>
                                                        {{ translate('download') }}
                                                        <i class="bi bi-caret-down-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.vehicle.export') }}?search={{ request()->get('search') }}&&file=excel">{{ translate('excel') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endcan
                                            <a href="{{ route('admin.vehicle.create') }}" type="button"
                                                class="btn btn-primary text-capitalize">
                                                <i class="bi bi-plus fs-16"></i> {{ translate('add_new_vehicle') }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th class="sl">{{ translate('SL') }}</th>
                                                    <th class="text-capitalize total-vehicle brand-model">
                                                        {{ translate('vehicle_type') }}</th>
                                                    <th class="text-capitalize total-vehicle brand-model">
                                                        {{ translate('brand_&_model') }}</th>
                                                    <th class="text-capitalize total-vehicle viin-license">
                                                        {{ translate('license') }}</th>
                                                    <th class="text-capitalize total-vehicle owner">
                                                        {{ translate('owner') }}</th>
                                                    <th class="text-capitalize total-vehicle vehicle-features">
                                                        {{ translate('vehicle_features') }}</th>
                                                    @can('vehicle_edit')
                                                        <th class="status">{{ translate('status') }}</th>
                                                    @endcan
                                                    <th class="text-center action">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @forelse ($vehicles as $key => $vehicle)
                                                    <tr id="hide-row-{{ $vehicle->id }}" class="record-row">
                                                        <td class="sl">{{ $vehicles->firstItem() + $key }}</td>
                                                        <td class="sl">
                                                            <div class="avatar bg-transparent rounded">
                                                                <img src="{{ onErrorImage(
                                                                    $vehicle?->category?->image,
                                                                    asset('storage/app/public/vehicle/category') . '/' . $vehicle?->category?->image,
                                                                    asset('public/assets/admin-module/img/media/car.png'),
                                                                    'vehicle/category/',
                                                                ) }}"
                                                                    class="dark-support rounded custom-box-size"
                                                                    alt="" style="--size: 42px">
                                                            </div>
                                                        </td>
                                                        <td class="brand-model">
                                                            <div class="media gap-3 align-items-center">
                                                                <div class="media-body">
                                                                    <a
                                                                        href="{{ route('admin.vehicle.show', ['id' => $vehicle->id]) }}">
                                                                        {{ $vehicle?->brand?->name }}
                                                                        - {{ $vehicle?->model?->name }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="viin-license">{{ $vehicle->licence_plate_number }}
                                                        </td>
                                                        <td class="text-capitalize owner">{{ $vehicle->ownership }}</td>
                                                        <td class="text-capitalize vehicle-features">
                                                            {{ translate('mileage') }}
                                                            : {{ $vehicle?->model?->engine }} <br>
                                                            {{ translate('seat') }}
                                                            : {{ $vehicle?->model?->seat_capacity }}
                                                            <br> {{ translate('hatch_bag') }}
                                                            : {{ $vehicle?->model?->hatch_bag_capacity }}
                                                            <br> {{ translate('fuel') }}: {{ $vehicle->fuel_type }}
                                                        </td>
                                                        @can('vehicle_edit')
                                                            <td class="status">
                                                                <label class="switcher">
                                                                    <input class="switcher_input status-change"
                                                                        data-url={{ route('admin.vehicle.status') }}
                                                                        id="{{ $vehicle->id }}" type="checkbox"
                                                                        {{ $vehicle->is_active ? 'checked' : '' }}>
                                                                    <span class="switcher_control"></span>
                                                                </label>
                                                            </td>
                                                        @endcan

                                                        <td class="action">
                                                            <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                                @can('vehicle_log')
                                                                    <a href="{{ route('admin.vehicle.log') }}?id={{ $vehicle->id }}"
                                                                        class="btn btn-outline-primary btn-action">
                                                                        <i class="bi bi-clock-fill"></i>
                                                                    </a>
                                                                @endcan
                                                                <a href="{{ route('admin.vehicle.show', ['id' => $vehicle->id]) }}"
                                                                    class="btn btn-outline-info btn-action">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </a>
                                                                @can('vehicle_edit')
                                                                    <a href="{{ route('admin.vehicle.edit', ['id' => $vehicle->id]) }}"
                                                                        class="btn btn-outline-info btn-action">
                                                                        <i class="bi bi-pencil-fill"></i>
                                                                    </a>
                                                                @endcan
{{--                                                                @can('vehicle_delete')--}}
{{--                                                                    <button data-id="delete-{{ $vehicle->id }}"--}}
{{--                                                                        data-message="{{ translate('want_to_delete_this_vehicle?') }}"--}}
{{--                                                                        type="button"--}}
{{--                                                                        class="btn btn-outline-danger btn-action form-alert">--}}
{{--                                                                        <i class="bi bi-trash-fill"></i>--}}
{{--                                                                    </button>--}}

{{--                                                                    <form--}}
{{--                                                                        action="{{ route('admin.vehicle.delete', ['id' => $vehicle->id]) }}"--}}
{{--                                                                        id="delete-{{ $vehicle->id }}" method="post">--}}
{{--                                                                        @csrf--}}
{{--                                                                        @method('delete')--}}
{{--                                                                    </form>--}}
{{--                                                                @endcan--}}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
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
                                        {!! $vehicles->links() !!}
                                    </div>
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
@endpush
