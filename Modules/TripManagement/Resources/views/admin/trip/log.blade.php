@extends('adminmodule::layouts.master')

@section('title', translate('Trips'))

@section('content')
    <!-- Main Content -->
    @php($current_status = $trip->current_status)
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4">{{ translate('trip')}} # {{$trip->ref_id}}</h2>

            @include('tripmanagement::admin.trip.partials._details-partials-inline')

            <div class="card">
                <div class="card-body pt-5">
                    <div class="border-bottom pb-4 mb-30">
                        <div class="row gy-4">
                            <div class="col-lg-3 col-sm-6 d-flex justify-content-center">
                                <div class="d-flex flex-column gap-2">
                                    <h4 class="text-primary text-capitalize">{{ translate('request_placed')}}</h4>
                                    @php($time_format = getSession('time_format'))
                                    <div class="fs-12">{{date('h:i A', strtotime($trip->created_at))}}</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 d-flex justify-content-center">
                                <div class="d-flex flex-column gap-2">
                                    <h4 class="text-primary text-capitalize">{{ translate('biding_status')}}</h4>
                                    <div
                                        class="fs-12">{{translate($trip->rise_request_count>0?spellOutNumber($trip->rise_request_count):"unavailable")}}</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 d-flex justify-content-center">
                                <div class="d-flex flex-column gap-2">
                                    <h4 class="text-primary">{{translate('payment')}}</h4>
                                    @if(!is_null($trip->payment_method))
                                        <div class="fs-12 text-capitalize">{{$trip->payment_method}}</div>
                                    @else
                                        <div class="fs-12 text-capitalize">{{translate('payment_not_selected')}}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 d-flex justify-content-center">
                                <div class="d-flex flex-column gap-2">
                                    <h4 class="text-primary text-capitalize">{{translate('ride_status')}}</h4>
                                    <div class="fs-12">{{translate($trip->current_status)}}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ride-process-steps">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 d-flex flex-column align-items-center gap-2 mb-4">
                                <div
                                    class="check-circle p-1 d-flex justify-content-center align-items-center bg-success text-white rounded-circle mb-2">
                                    <i class="bi bi-check2"></i>
                                </div>
                                <h6 class="text-capitalize">{{translate('ride_request_by_customer')}}</h6>
                                <div class="fs-12 text-capitalize">{{translate('request_created')}}
                                    : {{date('h:i A', strtotime($trip->created_at))}} </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 d-flex flex-column align-items-center gap-2 mb-4">
                                @php($accepted = $trip->tripStatus->accepted)
                                <div
                                    class="check-circle p-1 d-flex justify-content-center align-items-center {{$accepted? 'bg-success': 'bg-danger'}} text-white rounded-circle mb-2">
                                    @if($accepted)
                                        <i class="bi bi-check2"></i>
                                    @else
                                        <i class="bi bi-x"></i>
                                    @endif
                                </div>
                                <h6 class="text-capitalize">{{translate('request_accepted_by_rider')}}</h6>
                                @if($accepted)
                                    <div class="fs-12 text-capitalize">{{translate('request_accepted')}}
                                        : {{date('h:i A', strtotime($accepted))}} </div>
                                @endif
                            </div>
                            <div class="col-lg-3 col-sm-6 d-flex flex-column align-items-center gap-2 mb-4">
                                @php($ongoing = $trip->tripStatus->ongoing)
                                <div
                                    class="check-circle p-1 d-flex justify-content-center align-items-center {{$ongoing? 'bg-success': 'bg-danger'}} text-white rounded-circle mb-2">
                                    @if($ongoing)
                                        <i class="bi bi-check2"></i>
                                    @else
                                        <i class="bi bi-x"></i>
                                    @endif
                                </div>
                                <h6 class="text-capitalize">{{translate('ride_ongoing_to_destination')}}</h6>
                                @if($ongoing)
                                    <div class="fs-12 text-capitalize">{{translate('ongoing_ride')}}
                                        : {{date('h:i A', strtotime($trip->tripStatus->ongoing))}} </div>
                                @endif
                            </div>

                            @if($current_status == 'completed' || $current_status == 'cancelled'|| $current_status == 'failed')
                                <div class="col-lg-3 col-sm-6 d-flex flex-column align-items-center gap-2 mb-4">
                                    <div
                                        class="check-circle p-1 d-flex justify-content-center align-items-center {{$current_status == 'completed'? 'bg-success': 'bg-danger'}} text-white rounded-circle mb-2">
                                        @if($current_status == 'completed')
                                            <i class="bi bi-check2"></i>
                                        @else
                                            <i class="bi bi-x"></i>
                                        @endif
                                    </div>
                                    <h6 class="">{{translate($current_status)}}</h6>
                                    @if($current_status == 'completed')
                                        <div class="fs-12 text-capitalize">{{translate('destination_arrived')}}
                                            : {{date('h:i A', strtotime($trip->tripStatus->$current_status))}} </div>
                                    @else
                                        <div
                                            class="fs-12 text-capitalize">{{translate($current_status)}} {{translate('time')}}
                                            : {{date('h:i A', strtotime($trip->tripStatus->$current_status))}} </div>
                                    @endif
                                </div>
                            @else
                                <div class="col-lg-3 col-sm-6 d-flex flex-column align-items-center gap-2 mb-4">
                                    <div
                                        class="check-circle p-1 d-flex justify-content-center align-items-center bg-primary text-white rounded-circle mb-2">
                                        <i class="bi bi-check2"></i>
                                    </div>
                                    <h6 class="">{{translate($current_status)}}</h6>
                                    <div
                                        class="fs-12 text-capitalize">{{translate($current_status)}} {{translate('time')}}
                                        : {{date('h:i A', strtotime($trip->tripStatus->$current_status))}} </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection


