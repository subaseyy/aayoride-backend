@extends('adminmodule::layouts.master')

@section('title', translate('Business_Info'))

@section('content')
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <h2 class="fs-22 mb-4 text-capitalize">{{translate('business_management')}}</h2>
                <div class="col-12 mb-3">
                    <div class="">
                        @include('businessmanagement::admin.business-setup.partials._business-setup-inline')
                    </div>
                </div>
                <div class="card mb-3 text-capitalize">
                    <form action="{{route('admin.business.setup.trip-fare.store')}}?type=trip_fare_settings" id="fare_and_penalty_form" method="POST">
                        @csrf
                        <div class="card-header">
                            <h5 class="d-flex align-items-center gap-2">
                                <i class="bi bi-person-fill-gear"></i>
                                {{ translate('fare_&_penalty_settings') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <label for="start_count_idle_fee" class="mb-2">{{ translate('start_count_idle_fee_after') }} ({{ translate('min') }})</label>
                                        <div class="input-group_tooltip">
                                            <input required type="number" class="form-control" placeholder="Ex: 5" id="start_count_idle_fee" name="idle_fee" value="{{$settings->where('key_name', 'idle_fee')->first()?->value}}">
                                            <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                                    data-bs-title="{{ translate('The idle fee will be applied after the specified time (in minutes)') . '.' . translate('No fees will be charged for durations shorter than this time') }}"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <label for="delay_fee" class="mb-2">{{ translate('start_count_delay_fee_after') }} ({{ translate('min') }})</label>
                                        <div class="input-group_tooltip">
                                            <input required type="number" class="form-control" placeholder="Ex: 5" id="delay_fee" name="delay_fee" value="{{$settings->firstWhere('key_name', 'delay_fee')?->value}}">
                                            <i class="bi bi-info-circle-fill text-primary tooltip-icon" data-bs-toggle="tooltip"
                                                    data-bs-title="{{ translate('The delay fee will be applied after the specified time (in minutes)') . '. ' .translate('No fees will be charged for durations shorter than this time') }})"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3 flex-wrap justify-content-end">
                                <button type="submit" class="btn btn-primary text-uppercase">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
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
        $('#fare_and_penalty_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

@endpush
