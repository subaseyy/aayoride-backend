@extends('adminmodule::layouts.master')

@section('title', translate('Parcel_Delivery_Fare_Setup'))

@section('content')
    @php($unit = businessConfig('parcel_weight_unit', BUSINESS_INFORMATION)?->value)
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{ translate('parcel_delivery_fare_setup')}}</h2>

            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{route('admin.fare.parcel.store')}}" method="post">
                        @csrf
                        <input type="hidden" name="zone_id" value="{{$zone->id}}">
                        <h5 class="text-primary text-uppercase mb-4">{{ translate('parcel_delivery_fare_default_price')}}
                            ({{$zone->name}} {{ translate('zone')}})</h5>

                        <h6 class="mb-3 text-capitalize">{{translate('available_parcel_category_in_this_zone')}}</h6>

                        <div class="d-flex flex-wrap align-items-center gap-4 gap-xl-5 mb-30">
                            @forelse($parcelCategory as $pc)
                                @if($pc->is_active)
                                    <label class="custom-checkbox">
                                        <input type="checkbox" name="parcel_category[]" value="{{$pc->id}}"
                                               @forelse($fares?->fares?? [] as $fare)
                                                   @if($fare->parcel_category_id == $pc->id)
                                                       checked
                                            @endif
                                        @empty
                                            @endforelse
                                        >
                                        {{$pc->name}}
                                    </label>
                                @endif
                            @empty
                            @endforelse
                        </div>

                        <div class="row gy-4 parcel-fare-setup-class">
                            <div class="col-sm-6 col-lg-4">
                                <label for="base_fare" class="form-label">{{ translate('Base_Fare') }}</label>
                                <div class="input-group_tooltip">
                                    <input type="number" class="form-control" name="base_fare" id="base_fare"
                                           value="{{($fares?->base_fare) + 0}}"
                                           placeholder="{{ translate('Base_Fare')}}" step=".01" min="0.01" required>
                                    <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                       data-bs-title="{{ translate('set_the_base_fare_for_calling_a_vehicle_for_parcel_delivery')}}"></i>
                                </div>
                            </div>
                        </div>

                        <h5 class="d-flex align-items-center gap-2 mb-1 mt-5 text-capitalize">
                            <i class="bi bi-person-fill-gear"></i>
                            {{ translate('category_wise_delivery_fee')}}
                        </h5>

                        <div class="col-12 pt-3">
                            <div class="table-responsive border border-primary-light rounded">
                                <table class="table align-middle table-borderless table-variation">
                                    <thead class="border-bottom border-primary-light">
                                    <tr>
                                        <th>{{ translate('fare')}}</th>
                                        <th>
                                            {{ translate('base_fare')}}
                                            <span class="fs-10">/{{ translate($unit)}}</span>
                                        </th>
                                        @forelse($parcelWeight as $pw)
                                            @if($pw['is_active'] == 1)
                                                <th>{{($pw->min_weight+0) . '-' . ($pw->max_weight+0) .' /'.translate($unit)}}</th>
                                            @endif
                                        @empty
                                        @endforelse
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($parcelCategory as $pc)
                                        @if($pc->is_active)
                                            @php($fare =$fares?->fares->where('parcel_category_id', $pc->id)->first())
                                            <tr>
                                                <td class="{{$pc->id}} {{$fare?->parcel_category_id == $pc->id ? '' : 'd-none'}}">
                                                    <div
                                                        class="d-flex align-items-center gap-2 text-primary fw-semibold">
                                                        <div>{{translate($pc->name)}} <span
                                                                class="fs-10">/ km</span></div>
                                                        <i class="bi bi-info-circle-fill fs-14" data-bs-toggle="tooltip"
                                                           data-bs-title="{{ translate('set_the_fare_for_each_kilometer_added_with_the_base_fare') }}"></i>
                                                    </div>
                                                </td>
                                                <td class="category-fare-class {{$pc->id}} {{$fare?->parcel_category_id == $pc->id ? '' : 'd-none'}}">
                                                    <input type="number" name="base_fare_{{$pc->id}}"
                                                           value="{{ ($fare?->base_fare) ?? ($fares?->base_fare) }}"
                                                           class="form-control base_fare" step=".01" min="0.01" required>
                                                </td>
                                                @forelse($parcelWeight as $pw)
                                                    @php($weightFare =$fares?->fares->where('parcel_weight_id', $pw->id)->where('parcel_category_id', $pc->id)->first())
                                                    @if($pw->is_active ==1)
                                                        <td class="{{$pc->id}} {{$fare?->parcel_category_id == $pc->id ? '' : 'd-none'}}">
                                                            <input type="number" name="weight_{{$pc->id}}[{{$pw->id}}]"
                                                                   class="form-control {{$pc->id}}"
                                                                   value="{{$weightFare?->fare_per_km + 0}}" step=".01" min="0.01"
                                                                   {{$pc->id}} {{$fare?->parcel_category_id == $pc->id ? '' : 'disabled'}} required>
                                                        </td>
                                                    @endif
                                                @empty
                                                @endforelse
                                            </tr>
                                        @endif

                                    @empty
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-3 mt-3">
                            <button class="btn btn-primary text-uppercase"
                                    type="submit">{{ translate('submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->

@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module/js/fare-management/parcel/create.js')}}"></script>
    <script>
        "use strict";
        $("form").submit(function () {
            if ($('input[type="checkbox"]:checked').length <= 0) {
                toastr.error('{{translate('must_select_at_least_one_parcel_category')}}')
                return false;
            }
        });

        const inputParcelElements = document.querySelectorAll('.parcel-fare-setup-class input[type="number"]');

        inputParcelElements.forEach(input => {
            input.addEventListener('input', function () {
                if (parseFloat(this.value) < 0) {
                    // this.value = 1;
                    toastr.error('{{translate('the_value_must_greater_than_0')}}')
                }
            });
        });

        const inputCategoryElements = document.querySelectorAll('.category-fare-class input[type="number"]');

        inputCategoryElements.forEach(input => {
            input.addEventListener('input', function () {
                if (parseFloat(this.value) < 0) {
                    // this.value = 1;
                    toastr.error('{{translate('the_value_must_greater_than_0')}}')
                }
            });
        });
    </script>
@endpush
