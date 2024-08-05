@extends('adminmodule::layouts.master')

@section('title', translate('Business_Information'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('business_management')}}</h2>

            <form action="{{route('admin.business.setup.info.update-settings')}}" method="post" id="settings_form"
                  enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <div class="">
                            @include('businessmanagement::admin.business-setup.partials._business-setup-inline')
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="fw-medium d-flex align-items-center gap-2 text-capitalize">
                                    <i class="bi bi-briefcase-fill"></i>
                                    {{translate('business_settings')}}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-sm-6 col-lg-6">
                                        <div class="">
                                            <label for="business_name"
                                                   class="mb-2">{{translate('trip_commission')}} <span
                                                    class="text-danger fs-12">(%)</span>
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="trip_commission"
                                                       class="form-control" id="business_name"
                                                       placeholder="{{translate('Ex: 15')}}" step="0.1"
                                                       value="{{$settings->firstWhere('key_name', 'trip_commission')->value ?? ''}}"
                                                >
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('Set the commission (in percentage) the admin will receive from drivers')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div class="">
                                            <label for="business_contact_num"
                                                   class="mb-2">{{translate('vat')}} <span
                                                    class="text-danger fs-12">(%)</span></label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="vat_percent" class="form-control"
                                                       id="business_contact_num"
                                                       placeholder="{{translate('Ex: 15')}}" step="0.1"
                                                       value="{{$settings->firstWhere('key_name', 'vat_percent')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('in_percent')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div class="">
                                            <label for="business_email" class="mb-2 d-flex flex-wrap gap-2 align-items-end">
                                                <span>{{translate('search_radius')}}</span>
                                                <span class="text-danger fs-12">(km)</span>
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="search_radius" class="form-control"
                                                       step="any"
                                                       id="business_email" placeholder="{{translate('Ex: 15')}}"
                                                       value="{{$settings->firstWhere('key_name', 'search_radius')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('Customers can search for drivers within the radius (in kilometer) you have set here') . '. ' . translate('By default, it is set to 5 kilometers')}}">
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div class="">
                                            <label for="driver_completion_radius"
                                                   class="mb-2 d-flex flex-wrap gap-2 align-items-end"> <span>{{translate('driver_completion_radius')}}</span> <span
                                                    class="text-danger fs-12">(Meter)</span>
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="driver_completion_radius"
                                                       class="form-control" step="any"
                                                       id="driver_completion_radius"
                                                       placeholder="{{translate('Ex: 15')}}"
                                                       value="{{$settings->firstWhere('key_name', 'driver_completion_radius')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('Drivers can complete this ride within the radius (in meter) you have set here') . '. ' . translate('By default, it is set to 10 meters')}}">
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div>
                                            <label for="websocket_url" class="mb-2">{{translate('websocket_url')}}
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="text" name="websocket_url" class="form-control"
                                                       id="websocket_url"
                                                       placeholder="{{translate('Ex: your_domain_name')}}"
                                                       value="{{$settings->firstWhere('key_name', 'websocket_url')->value ?? env('PUSHER_HOST')}}" readonly>
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('Socket connection establishing URL is base url, Its is not editable')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div>
                                            <label for="business_email"
                                                   class="mb-2">{{translate('websocket_port')}}
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="websocket_port" class="form-control"
                                                       id="business_email" placeholder="{{translate('Ex: 6001')}}"
                                                       value="{{$settings->firstWhere('key_name', 'websocket_port')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('Socket connection establishing port')}} {{translate('default 6001')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6 mt-4">
                                        <div>
                                            <label for="maximum_login_hit"
                                                   class="mb-2">{{translate('maximum_login_attempt')}}
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="maximum_login_hit" class="form-control"
                                                       id="maximum_login_hit" placeholder="{{translate('Ex: 10')}}"
                                                       value="{{$settings->firstWhere('key_name', 'maximum_login_hit')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('the_maximum_login_hit_is_a_measure_of_how_many_times_a_user_can_submit_password_within_a_period')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div>
                                            <label for="temporary_login_block_time"
                                                   class="mb-2 d-flex flex-wrap gap-2 align-items-end">{{translate('temporary_login_block_time')}}
                                                <span class="text-danger">({{translate('in_seconds')}})</span>
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="temporary_login_block_time"
                                                       class="form-control" id="temporary_login_block_time"
                                                       placeholder="{{translate('Ex: 10')}}"
                                                       value="{{$settings->firstWhere('key_name', 'temporary_login_block_time')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('temporary_login_block_time_refers_to_a_security_measure_implemented_by_systems_to_restrict_access_for_a_specific_period_of_time_for_wrong_password_submission')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div>
                                            <label for="maximum_otp_hit" class="mb-2">
                                                {{translate('maximum_OTP_submit_attempt')}}
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="maximum_otp_hit" class="form-control"
                                                       id="maximum_otp_hit" placeholder="{{translate('Ex: 10')}}"
                                                       value="{{$settings->firstWhere('key_name', 'maximum_otp_hit')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('the_maximum_OTP_hit_is_to_measure_of_how_many_times_a_specific_one-time_password_will_be_generated_and_used_within_a_period')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div>
                                            <label for="otp_resend_time" class="mb-2 d-flex flex-wrap gap-2 align-items-end">
                                                <span>{{translate('OTP_resend_time')}}</span>
                                                <span class="text-danger">({{translate('in_seconds')}})</span>
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="otp_resend_time" class="form-control"
                                                       id="otp_resend_time" placeholder="{{translate('Ex: 60')}}"
                                                       value="{{$settings->firstWhere('key_name', 'otp_resend_time')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('if_the_user_fails_to_get_the_OTP_within_a_certain_time,_user_can_request_a_resend')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div>
                                            <label for="temporary_block_time"
                                                   class="mb-2 d-flex flex-wrap gap-2 align-items-end">
                                                   <span>{{translate('temporary_OTP_block_time')}}</span>
                                                <span class="text-danger">({{translate('in_seconds')}})</span>
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="temporary_block_time" class="form-control"
                                                       id="temporary_block_time" placeholder="{{translate('Ex: 600')}}"
                                                       value="{{$settings->firstWhere('key_name', 'temporary_block_time')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('temporary_OTP_block_time_refers_to_a_security_measure_implemented_by_systems_to_restrict_access_to_OTP_service_for_a_specified_period_of_time_for_wrong_OTP_submission')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div class="">
                                            <label for="business_email"
                                                   class="mb-2">{{translate('pagination_limit')}}
                                            </label>
                                            <div class="input-group_tooltip">
                                                <input type="number" name="pagination_limit"
                                                       class="form-control" id="business_email"
                                                       placeholder="{{translate('Ex: 15')}}"
                                                       value="{{$settings->firstWhere('key_name', 'pagination_limit')->value ?? ''}}">
                                                <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('The number of rows shows in the data table._')}} {{translate('default 10')}}"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-6">
                                        <div class="border rounded border-primary-light px-4 py-3 d-flex justify-content-between">
                                            <h6 class="d-flex text-capitalize">{{translate('bid_on_fare_')}}
                                                <i class="px-1 bi bi-info-circle-fill text-primary tooltip-icon"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{translate('fare_negotiation')}}"></i>
                                            </h6>
                                            <div class="position-relative">
                                                <label class="switcher">
                                                    <input class="switcher_input" type="checkbox" name="bid_on_fare"
                                                           id="loyalty_point_switch"
                                                        {{ (businessConfig('bid_on_fare', 'business_settings')->value ?? 0) == 1 ? 'checked' : ''}}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary text-capitalize">
                                            {{translate('save_information')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')

    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#settings_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

@endpush
