@extends('adminmodule::layouts.master')

@section('title', translate('Trip_Fare_Setup'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4">{{ translate('Trip_Fare_Setup') }}</h2>


            <form action="{{ route('admin.fare.trip.store') }}" method="post" id="trip-store-form">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-primary text-uppercase mb-30">{{ translate('fare_default_price') }}
                            ({{ $zone->name }} {{ translate('zone') }})</h5>

                        <h6 class="mb-3 text-capitalize">{{ translate('available_vehicle_categories_in_this_zone') }}</h6>

                        <div class="d-flex flex-wrap align-items-center gap-4 gap-xl-5 mb-30">
                            @forelse($vehicleCategories as $vehicleCategory)
                                @php($trip = $tripFares?->where('vehicle_category_id', $vehicleCategory->id)->first())
                                <label class="custom-checkbox">
                                    <input type="checkbox" class="test" name="vehicle_category_{{ $vehicleCategory->id }}"
                                        value="{{ $vehicleCategory->id }}" @if ($trip?->vehicle_category_id == $vehicleCategory->id) checked @endif>
                                    {{ $vehicleCategory->name }}
                                </label>
                            @empty
                            @endforelse
                        </div>
                        <input type="hidden" name="zone_id" value="{{ $zone->id }}">
                        <input type="hidden" name="default_fare_id" value="{{ $defaultTripFare?->id }}">

                        <div class="col-12">
                            <h6 class="fw-medium mb-3 d-flex align-items-center gap-2 text-capitalize">
                                {{ translate('use_category_wise_different_fare') }} ?
                                <i class="bi bi-info-circle-fill text-primary fs-16" data-bs-toggle="tooltip"
                                    data-bs-title="{{ translate('if_Yes,_each_vehicle_category_has_different_fares.') . ' ' . translate('otherwise_all_categories_will_share_the_same_fare') }}"></i>
                            </h6>
                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="d-flex gap-2 align-items-center">
                                    <input name="category_wise_different_fare" class="use_category_wise" type="radio"
                                        value="1" id="use_category_wise1"
                                        {{ empty($defaultTripFare) || (!empty($defaultTripFare) && $defaultTripFare?->category_wise_different_fare == 1) ? 'checked' : '' }}>
                                    <label for="use_category_wise1">{{ translate('yes') }}</label>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input name="category_wise_different_fare" class="use_category_wise" type="radio"
                                        value="0" id="use_category_wise2"
                                        {{ !empty($defaultTripFare) && $defaultTripFare?->category_wise_different_fare == 0 ? 'checked' : '' }}>
                                    <label for="use_category_wise2">{{ translate('no') }}</label>
                                </div>
                            </div>
                        </div>


                        <div class="row gy-4 custom-class-fare mt-3">




                            <div class="col-sm-6 col-lg-4">
                                <label for="base_fare" class="form-label">{{ translate('Base_Fare') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" class="form-control part-1-input copy-value" step=".01"
                                        min="0.01" name="base_fare" id="base_fare"
                                        placeholder="{{ translate('Base_Fare') }}"
                                        value="{{ $defaultTripFare->base_fare ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_initial_fare_for_starting_a_trip') }}"></i>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label for="base_fare_per_km" class="form-label">{{ translate('Fare_(Per_km)') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" class="form-control part-1-input copy-value" step=".01"
                                        min="0.01" name="base_fare_per_km"
                                        placeholder="{{ translate('Fare_(Per_km)') }}" id="base_fare_per_km"
                                        value="{{ $defaultTripFare->base_fare_per_km ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_fare_(per_km)_which_will_be_added_with_the_base_fare') }}"></i>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label for="cancellation_fee"
                                    class="form-label">{{ translate('Cancellation_Fee_(%)') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" name="cancellation_fee" min="0" max="100"
                                        step=".01" class="form-control part-1-input copy-value"
                                        placeholder="{{ translate('Cancellation_Fee_(%)') }}" id="cancellation_fee"
                                        value="{{ $defaultTripFare->cancellation_fee_percent ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_trip_cancellation_fee_in_percentage_from_the_total_fee_for_the_users_which_will_be_counted_after_exceeding_the_minimum_cancellation_fee') }}"></i>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label for="min_cancellation_fee"
                                    class="form-label">{{ translate('Minimum_Cancellation_Fee_($)') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" name="min_cancellation_fee" step=".01" min="0"
                                        class="form-control part-1-input copy-value"
                                        placeholder="{{ translate('Minimum_Cancellation_Fee_($)') }}"
                                        id="min_cancellation_fee"
                                        value="{{ $defaultTripFare->min_cancellation_fee ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_minimum_cancellation_fee_for_the_users_to_cancel_the_trip') }}"></i>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label for="idle_fee" class="form-label">
                                    {{ translate('Idle_Fee_(Per_min)') }}
                                </label>
                                <div class="input-group_tooltip">
                                    <input type="number" name="idle_fee" step=".01"
                                        class="form-control part-1-input copy-value"
                                        placeholder="{{ translate('Idle_Fee_(Per_min)') }}" id="idle_fee"
                                        value="{{ $defaultTripFare->idle_fee_per_min ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_fee_(per_min_)_for_the_customer_when_he/she_requests_to_wait_the_driver_on_an_ongoing_trip') }}"></i>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <label for="trip_delay_fee"
                                    class="form-label">{{ translate('Trip_Delay_Fee_(Per_min)') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" name="trip_delay_fee" step=".01"
                                        class="form-control part-1-input copy-value"
                                        placeholder="{{ translate('Trip_Delay_Fee_(Per_min)') }}" id="trip_delay_fee"
                                        value="{{ $defaultTripFare->trip_delay_fee_per_min ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_fee_(per_min)_for_the_customer_when_the_trip_took_longer_than_the_estimated_time') }}"></i>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-4">
                                <label for="minimum_pickup_distance"
                                    class="form-label">{{ translate('Minimum Pickup Distance') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" name="minimum_pickup_distance" step="1"
                                        class="form-control part-1-input copy-value"
                                        placeholder="{{ translate('Minimum Pickup Distance') }}"
                                        id="minimum_pickup_distance"
                                        value="{{ $defaultTripFare->minimum_pickup_distance ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="{{ translate('set_the_fee_(per_min)_for_the_customer_when_the_trip_took_longer_than_the_estimated_time') }}"></i>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-4">
                                <label for="pickup_bonus_amount"
                                    class="form-label">{{ translate('Pickup Bonus Amount') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" name="pickup_bonus_amount" step="5"
                                        class="form-control part-1-input copy-value"
                                        placeholder="{{ translate('Pickup Bonus Amount') }}" id="pickup_bonus_amount"
                                        value="{{ $defaultTripFare->pickup_bonus_amount ?? 0 }}" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                        data-bs-title="Set the pickup bonus amount"></i>
                                </div>
                            </div>

                            <div class="col-12 pt-3" id="different-fare-div">
                                <div class="table-responsive border border-primary rounded"
                                    style="--bs-border-opacity: .2">
                                    <table class="table align-middle table-borderless table-variation">
                                        <thead class="border-bottom border-primary" style="--bs-border-opacity: .2">
                                            <tr>
                                                <th>{{ translate('fare') }}</th>
                                                <th class="text-capitalize">{{ translate('default_price') }}</th>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->firstWhere('vehicle_category_id', $vehicleCategory->id))
                                                    <th
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        {{ $vehicleCategory->name }}</th>
                                                @empty
                                                @endforelse
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div class="text-capitalize">
                                                            {{ translate('base_fare') }}
                                                            ({{ session()->get('currency_symbol') ?? '$' }})
                                                        </div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('set_the_base_fare_for_starting_a_trip') }}">
                                                        </i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input disabled type="number" class="form-control base_fare_default"
                                                        value="{{ $defaultTripFare->base_fare ?? 0 }}" required>
                                                </td>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->firstWhere('vehicle_category_id', $vehicleCategory->id))
                                                    <td
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        <input type="number" step=".01" min="0.01"
                                                            name="base_fare_{{ $vehicleCategory->id }}"
                                                            class="form-control base_fare_default part-2-input {{ $vehicleCategory->id }}"
                                                            value="{{ $trip?->base_fare ? round($trip->base_fare, 2) : 0 }}"
                                                            {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}
                                                            required>
                                                    </td>
                                                @empty
                                                @endforelse

                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div class="text-capitalize">
                                                            {{ translate('fare_per_km') }}
                                                            ({{ session()->get('currency_symbol') ?? '$' }})
                                                        </div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('set_the_fare_for_each_kilometer_added_with_the_base_fare') }}"></i>
                                                    </div>
                                                </td>
                                                <td><input disabled type="number"
                                                        class="form-control base_fare_per_km_default"
                                                        value="{{ $defaultTripFare->base_fare_per_km ?? 0 }}">
                                                </td>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->firstWhere('vehicle_category_id', $vehicleCategory->id))
                                                    <td
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        <input type="number" step=".01" min="0.01"
                                                            name="base_fare_per_km_{{ $vehicleCategory->id }}"
                                                            class="form-control base_fare_per_km_default part-2-input {{ $vehicleCategory->id }}"
                                                            value="{{ $trip?->base_fare_per_km ? round($trip->base_fare_per_km, 2) : 0 }}"
                                                            {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                    </td>
                                                @empty
                                                @endforelse

                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div class="text-capitalize">{{ translate('cancellation_fee') }}
                                                            (%)
                                                        </div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('set_the_trip_cancellation_fee_(in_percentage_of_the_total_trip_fee)_here._') .
                                                                translate('if_the_user_cancels_the_trip_they_must_pay_this_fee.') }}">
                                                        </i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input disabled type="number"
                                                        value="{{ $defaultTripFare->cancellation_fee_percent ?? 0 }}"
                                                        class="form-control cancellation_fee_default">
                                                </td>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->firstWhere('vehicle_category_id', $vehicleCategory->id))
                                                    <td
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        <input type="number" step=".01" min="0"
                                                            max="100"
                                                            name="cancellation_fee_{{ $vehicleCategory->id }}"
                                                            value="{{ $trip?->cancellation_fee_percent ? round($trip->cancellation_fee_percent, 2) : 0 }}"
                                                            class="form-control cancellation_fee_default part-2-input {{ $vehicleCategory->id }}"
                                                            {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                    </td>
                                                @empty
                                                @endforelse
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div class="text-capitalize">
                                                            {{ translate('minimum_cancellation_fee') }}
                                                            ({{ session()->get('currency_symbol') ?? '$' }})
                                                        </div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('set_the_minimum_trip_cancellation_fee_here._') .
                                                                translate('if_the_user_cancels_the_trip_they_must_pay_this_fee.') }}">
                                                        </i>
                                                    </div>
                                                </td>
                                                <td><input disabled type="number"
                                                        value="{{ $defaultTripFare->min_cancellation_fee ?? 0 }}"
                                                        class="form-control min_cancellation_fee_default">
                                                    @forelse($vehicleCategories as $vehicleCategory)
                                                        @php($trip = $tripFares?->where('vehicle_category_id', $vehicleCategory->id)->first())
                                                <td
                                                    class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                    <input type="number" step=".01" min="0"
                                                        name="min_cancellation_fee_{{ $vehicleCategory->id }}"
                                                        value="{{ $trip?->min_cancellation_fee ? round($trip->min_cancellation_fee, 2) : 0 }}"
                                                        class="form-control min_cancellation_fee_default part-2-input {{ $vehicleCategory->id }}"
                                                        {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                </td>
                                            @empty
                                                @endforelse
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div class="text-capitalize">
                                                            {{ translate('idle_fee') }}
                                                            ({{ session()->get('currency_symbol') ?? '$' }})
                                                        </div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('set_the_idle_fee_(per_min)_here._') .
                                                                translate('if_the_driver_remains_idle_then_the_user_must_pay_this_fee.') }}">

                                                        </i>
                                                    </div>
                                                </td>
                                                <td><input disabled type="number"
                                                        value="{{ $defaultTripFare->idle_fee_per_min ?? 0 }}"
                                                        class="form-control idle_fee_default">
                                                    @forelse($vehicleCategories as $vehicleCategory)
                                                        @php($trip = $tripFares?->where('vehicle_category_id', $vehicleCategory->id)->first())
                                                <td
                                                    class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                    <input type="number" step=".01" min="0"
                                                        name="idle_fee_{{ $vehicleCategory->id }}"
                                                        value="{{ $trip?->idle_fee_per_min ? round($trip->idle_fee_per_min, 2) : 0 }}"
                                                        class="form-control idle_fee_default part-2-input {{ $vehicleCategory->id }}"
                                                        {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                </td>
                                            @empty
                                                @endforelse
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div>{{ translate('Trip_Delay_Fee_(Per_min)') }}</div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('set_the_delay_fee_(per_min)_here._') .
                                                                translate('if_the_trip_takes_longer_then_estimated_time_then_the_user_must_pay_this_fee.') }}">
                                                        </i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input disabled type="number"
                                                        value="{{ $defaultTripFare->trip_delay_fee_per_min ?? 0 }}"
                                                        class="form-control trip_delay_fee_default">
                                                </td>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->where('vehicle_category_id', $vehicleCategory->id)->first())
                                                    <td
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        <input type="number" step=".01" min="0"
                                                            name="trip_delay_fee_{{ $vehicleCategory->id }}"
                                                            value="{{ $trip?->trip_delay_fee_per_min ? round($trip->trip_delay_fee_per_min, 2) : 0 }}"
                                                            class="form-control trip_delay_fee_default part-2-input {{ $vehicleCategory->id }}"
                                                            {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                    </td>
                                                @empty
                                                @endforelse
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div>{{ translate('Minimum Pickup Distance') }}</div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('Set the Minimum Pickup Distance') .
                                                                translate('The area radius for a customer to search for a ride') }}">
                                                        </i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input disabled type="number"
                                                        value="{{ $defaultTripFare->minimum_pickup_distance ?? 0 }}"
                                                        class="form-control minimum_pickup_distance_default">
                                                </td>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->where('vehicle_category_id', $vehicleCategory->id)->first())
                                                    <td
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        <input type="number" step=".01" min="0"
                                                            name="minimum_pickup_distance_{{ $vehicleCategory->id }}"
                                                            value="{{ $trip?->minimum_pickup_distance ?? 0 }}"
                                                            class="form-control minimum_pickup_distance_default part-2-input {{ $vehicleCategory->id }}"
                                                            {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                    </td>
                                                @empty
                                                @endforelse
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div>{{ translate('Pickup Bonus Amount') }}</div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                            data-bs-title="{{ translate('Set the pickup bonus amount') .
                                                                translate('If the rider is out of the minimum range then the bonus is added to rider') }}">
                                                        </i>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input disabled type="number"
                                                        value="{{ $defaultTripFare->pickup_bonus_amount ?? 0 }}"
                                                        class="form-control pickup_bonus_amount_default">
                                                </td>
                                                @forelse($vehicleCategories as $vehicleCategory)
                                                    @php($trip = $tripFares?->where('vehicle_category_id', $vehicleCategory->id)->first())
                                                    <td
                                                        class="{{ $vehicleCategory->id }} {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'd-none' }}">
                                                        <input type="number" step=".01" min="0"
                                                            name="pickup_bonus_amount_{{ $vehicleCategory->id }}"
                                                            value="{{ $trip?->pickup_bonus_amount ?? 0 }}"
                                                            class="form-control pickup_bonus_amount_default part-2-input {{ $vehicleCategory->id }}"
                                                            {{ $vehicleCategory->id == $trip?->vehicle_category_id ? '' : 'disabled' }}>
                                                    </td>
                                                @empty
                                                @endforelse
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-3 mt-3">
                            <button class="btn btn-primary text-uppercase" type="submit"
                                id="submit">{{ translate('submit') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->

@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/fare-management/trip/create.js') }}"></script>

    <script>
        "use strict";

        const inputCustomElements = document.querySelectorAll('.custom-class-fare input[type="number"]');

        inputCustomElements.forEach(input => {
            input.addEventListener('input', function() {
                if (parseFloat(this.value) < 0) {
                    // this.value = 1;
                    toastr.error('{{ translate('the_value_must_greater_than_0') }}')
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('trip-store-form');
            const part1Inputs = Array.from(form.querySelectorAll('.part-1-input'));
            const part2Inputs = Array.from(form.querySelectorAll('.part-2-input'));
            form.addEventListener('submit', function(event) {
                if ($('input[type="checkbox"]:checked').length <= 0) {
                    event.preventDefault();
                    toastr.error('{{ translate('must_select_at_least_one_vehicle_category') }}')
                    return false;
                }
                const part1Filled = part1Inputs.some(input => input.value.trim() !== '');
                const part2Filled = part2Inputs.some(input => input.value.trim() !== '');

                if (!part1Filled && !part2Filled) {
                    event.preventDefault();
                    toastr.error(
                        '{{ translate('please_enter_vehicle_wise_or_category_wise_information') }}')
                }
            });
        });

        const inputDifferentElements = document.querySelectorAll('#different-fare-div input[type="number"]');

        inputDifferentElements.forEach(input => {
            input.addEventListener('input', function() {
                if (parseFloat(this.value) < 0) {
                    // this.value = 1;
                    toastr.error('{{ translate('the_value_must_greater_than_0') }}')
                }
            });
        });
    </script>
@endpush
