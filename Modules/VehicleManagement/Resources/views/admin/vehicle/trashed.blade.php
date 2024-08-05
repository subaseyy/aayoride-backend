@section('title', 'Vehicle List')

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <div class="row g-4 mt-30">
                <div class="col-12">

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <h2 class="fs-22 mb-3 text-capitalize">{{ translate('deleted_vehicles') }}</h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_vehicle') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $vehicles->total() }}</span>
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
                                                    placeholder="{{ translate('searche_here_by_Viin_&_License') }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th class="sl">{{ translate('SL') }}</th>
                                                    <th class="text-capitalize total-vehicle brand-model">
                                                        {{ translate('brand_&_model') }}</th>
                                                    <th class="text-capitalize total-vehicle viin-license">
                                                        {{ translate('viin_&_license') }}</th>
                                                    <th class="text-capitalize total-vehicle owner">
                                                        {{ translate('owner') }}</th>
                                                    <th class="text-capitalize total-vehicle vehicle-features">
                                                        {{ translate('vehicle_features') }}</th>
                                                    <th class="text-center action">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @forelse ($vehicles as $vehicle)
                                                    <tr id="hide-row-{{ $vehicle->id }}">
                                                        <td class="sl">{{ $loop->index + 1 }}</td>
                                                        <td class="brand-model">
                                                            <div class="media gap-3 align-items-center">
                                                                <div class="avatar bg-transparent rounded">
                                                                    <img src="{{ onErrorImage(
                                                                        $vehicle?->model?->image,
                                                                        asset('storage/app/public/vehicle/model') . '/' . $vehicle?->model?->image,
                                                                        asset('public/assets/admin-module/img/media/car.png'),
                                                                        'vehicle/model/',
                                                                    ) }}"
                                                                        class="dark-support fit-object-contain rounded"
                                                                        alt="">
                                                                </div>
                                                                <div class="media-body">
                                                                    <a
                                                                        href="{{ route('admin.vehicle.show', ['id' => $vehicle->id]) }}">
                                                                        {{ $vehicle->brand->name }}
                                                                        - {{ $vehicle->model->name }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="viin-license">{{ $vehicle->vin_number }}
                                                            <br> {{ $vehicle->licence_plate_number }}
                                                        </td>
                                                        <td class="text-capitalize owner">{{ $vehicle->ownership }}</td>
                                                        <td class="text-capitalize vehicle-features">
                                                            {{ translate('mileage') }}
                                                            : {{ $vehicle->model->engine }} <br>
                                                            {{ translate('seat') }}
                                                            : {{ $vehicle->model->seat_capacity }}
                                                            <br> {{ translate('hatch_bag') }}
                                                            : {{ $vehicle->model->hatch_bag_capacity }}
                                                            <br> {{ translate('fuel') }}: {{ $vehicle->fuel_type }}
                                                        </td>
                                                        <td class="action">
                                                            <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                                <a href="{{ route('admin.vehicle.restore', ['id' => $vehicle->id]) }}"
                                                                    class="btn btn-outline-primary btn-action">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </a>
                                                                <button data-id="delete-{{ $vehicle->id }}"
                                                                    data-message="{{ translate('want_to_permanent_delete_this_vehicle?') }} {{ translate('you_cannot_revert_this_action') }}"
                                                                    type="button"
                                                                    class="btn btn-outline-danger btn-action form-alert">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>

                                                                <form
                                                                    action="{{ route('admin.vehicle.permanent-delete', ['id' => $vehicle->id]) }}"
                                                                    id="delete-{{ $vehicle->id }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
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
