@extends('adminmodule::layouts.master')

@section('title', translate('deleted_driver_list'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mt-4 text-capitalize mb-3">{{ translate('deleted_driver_list') }}</h2>
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-end my-3 gap-3">
                        <div class="d-flex gap-2">
                            <span class="text-muted">{{ translate('total_driver') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $drivers->total() }}</span>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="driver_all" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="javascript:;" method="GET"
                                              class="search-form search-form_style-two">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" name="search"
                                                       value="{{ request()->get('search') }}"
                                                       id="search" class="theme-input-style search-form__input"
                                                       placeholder="{{ translate('search_here_by_name') }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                    data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-borderless align-middle table-hover">
                                            <thead class="table-light align-middle text-capitalize">
                                                <tr>
                                                    <th>{{ translate('SL') }}</th>
                                                    <th class="name">{{ translate('name') }}</th>
                                                    <th class="contact-info">{{ translate('contact_info') }}</th>
                                                    <th class="profile-status">{{ translate('profile_status') }}</th>
                                                    <th class="level">{{ translate('level') }}</th>
                                                    <th class="total-trip">{{ translate('total_trip') }}</th>
                                                    <th class="earning">{{ translate('earning') }}</th>
                                                    <th class="text-center action">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($drivers as $key => $driver)
                                                    <tr id="hide-row-{{ $driver->id }}">
                                                        <td>{{ $key + $drivers->firstItem() }}</td>
                                                        <td class="name">
                                                            <a href="#" class="media align-items-center gap-10">
                                                                <img width="20" loading="lazy" class="rounded"
                                                                    src="{{ onErrorImage(
                                                                        $driver?->profile_image,
                                                                        asset('storage/app/public/driver/profile') . '/' . $driver?->profile_image,
                                                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                        'driver/profile/',
                                                                    ) }}"
                                                                    alt="">
                                                                <div class="media-body">{{ $driver?->first_name }}
                                                                    {{ $driver?->last_name }}</div>
                                                            </a>
                                                        </td>
                                                        <td class="contact-info">
                                                            <div class="title-color"><a
                                                                    href="tel:{{ $driver->phone }}">{{ $driver->phone }}</a>
                                                            </div>
                                                            <div><a
                                                                    href="mailto:{{ $driver->email }}">{{ $driver->email }}</a>
                                                            </div>
                                                        </td>
                                                        @php($count = 0)
                                                        @if (!is_null($driver?->first_name))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver?->last_name))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->email))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->phone))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->gender))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->identification_number))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->identification_type))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->identification_image))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->other_documents))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->date_of_birth))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($driver->profile_image))
                                                            @php($count++)
                                                        @endif

                                                        <td class="profile-status">{{ round(($count / 11) * 100) }}%</td>
                                                        <td class="level">{{ $driver->level?->name }}</td>
                                                        <td class="total-trip">{{ $driver->driverTrips->count() }}</td>
                                                        <td>
                                                            @php($ids = $driver->driverTripsStatus->whereNotNull('completed')->pluck('trip_request_id'))
                                                            @php($earning = $driver->driverTrips->whereIn('id', $ids)->sum('paid_fare'))
                                                            {{ $earning }}
                                                        </td>
                                                        <td class="action">
                                                            <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                                <button
                                                                    data-bs-toggle="tooltip" data-bs-title="{{ translate('restore_driver') }}"
                                                                    data-route="{{ route('admin.driver.restore', ['id' => $driver->id]) }}"
                                                                    data-message="{{ translate('Want_to_recover_this_driver?_') . translate('if_yes,_this_driver_will_be_available_again_in_the_Driver_List') }}"
                                                                    class="btn btn-outline-primary btn-action restore-data">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </button>
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

                                    <div
                                        class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                                        <p class="mb-0"></p>

                                        <div
                                            class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                            <div class="d-flex align-items-center gap-1">
                                            </div>
                                            {!! $drivers->links() !!}

                                        </div>
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
