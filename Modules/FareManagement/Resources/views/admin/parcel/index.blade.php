@extends('adminmodule::layouts.master')

@section('title', translate('Parcel_Delivery_Fare_Setup'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{ translate('parcel_delivery_fare_setup')}}</h2>

            <form action="#">
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
                                                    <h6 class="text-muted mb-2">{{ translate('total_customer ')}}:
                                                        <span class="text-success">{{$zone->customers_count}}</span>
                                                    </h6>
                                                    <h6 class="text-muted">{{ translate('total_driver')}}: <span
                                                            class="text-primary">{{$zone->drivers_count}}</span></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="">
                                                <h6 class="mb-3 text-capitalize">{{ translate('available_parcel_categories_in_this_zone')}}</h6>
                                                <div class="d-flex flex-wrap align-items-center gap-4">
                                                    @forelse($parcelCategory as $pc)
                                                        @if($fares->firstWhere('zone_id', $zone->id)?->fares->firstWhere('parcel_category_id', $pc->id)?->fare_per_km)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="rounded-circle lh-1 bg-primary text-white"><i
                                                                        class="bi bi-check"></i></span>
                                                                {{$pc->name}}
                                                            </div>
                                                        @else
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span class="rounded-circle p-2 bg-grey"></span>
                                                                {{$pc->name}}
                                                            </div>
                                                        @endif
                                                    @empty
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex justify-content-lg-end">
                                                <a href="{{ route('admin.fare.parcel.create', [ $zone->id ] )}}"
                                                   class="btn btn-primary text-capitalize"><i
                                                        class="bi bi-gear-fill"></i> {{ translate('view_fare_setup')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->

@endsection
