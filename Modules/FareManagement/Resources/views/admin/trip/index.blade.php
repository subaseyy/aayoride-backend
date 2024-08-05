@extends('adminmodule::layouts.master')

@section('title', translate('Trip_Fare_Setup'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{ translate('trip_fare_setup')}}</h2>

            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary text-uppercase mb-30">{{ translate('operation_zone_wise_ride_fare_setup')}}</h5>
                    @forelse($zones as $zone)

                        <div class="card bg-primary-light border-0 mb-3">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class="col-lg-4">
                                        <div class="media flex-wrap gap-3">
                                            <span
                                                class="fw-medium bg-primary text-white circle-24">{{ $loop->iteration }}</span>
                                            <div class="media-body">
                                                <h6 class="mb-3">{{$zone->name}}</h6>
{{--                                                <h6 class="text-muted mb-2 text-capitalize">{{ translate('total_customer ')}}--}}
{{--                                                    : <span class="text-success">{{$zone->customers_count}}</span></h6>--}}
                                                <h6 class="text-muted text-capitalize">{{ translate('total_driver')}}
                                                    : <span class="text-primary">{{$zone->drivers_count}}</span></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <h6 class="mb-3 text-capitalize">{{ translate('available_vehicle_category_in_this_zone')}}</h6>
                                        <div class="d-flex flex-wrap align-items-center gap-4">
                                            @forelse($vehicleCategories as $vc)
                                                @if($fares->where('zone_id', $zone->id)->firstWhere('vehicle_category_id', $vc->id))
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="rounded-circle lh-1 bg-primary text-white"><i
                                                                class="bi bi-check"></i></span>
                                                        {{$vc->name}}
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="rounded-circle p-2 bg-grey"></span> {{$vc->name}}
                                                    </div>
                                                @endif
                                            @empty
                                                <div class="d-flex align-items-center gap-2">
                                                    <p>{{ translate('no_vehicle_category_available_for_this_zone') }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="d-flex justify-content-lg-end">
                                            <a href="{{route('admin.fare.trip.create', [ $zone->id])}}"
                                               class="btn btn-primary text-capitalize">
                                                <i class="bi bi-gear-fill"></i> {{ translate('view_fare_setup')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="card bg-primary-light border-0 mb-3">
                            <div class="card-body">
                                <div class="row gy-4">
                                    <div class="col-lg-12">
                                        <div class="text-capitalize d-flex justify-content-center gap-3">
                                            <div>
                                                <h6 class="mb-4 text-capitalize">{{ translate('please_add_or_activate_a_zone')}}</h6>
                                                <a href="{{route('admin.zone.index')}}"
                                                   class="btn btn-primary text-capitalize justify-content-center">
                                                    <i class="bi bi-arrow-left"></i> {{ translate('go_to_zone_setup')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
