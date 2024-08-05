@extends('adminmodule::layouts.master')

@section('title', translate('Trips'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-30 mb-3 gap-3">
                        <h2 class="fs-22 text-capitalize">{{translate('deleted_trips')}}</h2>

                        <div class="d-flex align-items-center gap-2 text-capitalize">
                            <span class="text-muted">{{translate('total_trips')}} : </span>
                            <span class="text-primary fs-16 fw-bold">{{$trips->total()}}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="{{url()->current()}}" class="search-form search-form_style-two">
                                    <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        <input type="search" name="search" class="theme-input-style search-form__input" placeholder="{{translate('Search_here_by_Trip_ID')}}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                </form>
                            </div>
                            <div id="trip-list-view">
                                <div class="table-responsive mt-3">
                                    <table class="table table-borderless align-middle table-hover">
                                        <thead class="table-light align-middle text-capitalize text-nowrap">
                                        <tr>
                                            <th class="sl">{{translate('SL')}}</th>
                                            <th class="trip-id">{{translate('trip_ID')}}</th>
                                            <th class="date">{{translate('date')}}</th>
                                            <th class="customer-name">{{translate('customer')}}</th>
                                            <th class="driver">{{translate('driver')}}</th>
                                            <th class="trip-cost">{{translate('trip_cost')}} ({{getSession('currency_symbol')}})</th>
                                            <th class="coupon-discount">{{translate('coupon')}} <br /> {{translate('discount')}} ({{getSession('currency_symbol')}})</th>
                                            <th class="additional-fee text-capitalize">{{translate('additional_fee')}} ({{getSession('currency_symbol')}})</th>
                                            <th class="text-capitalize total-trip-cost">{{translate('total_trip')}} <br />  {{translate('cost')}} ({{getSession('currency_symbol')}})</th>
                                            <th class="admin-commission">{{translate('admin')}} <br />  {{translate('commission')}} ({{getSession('currency_symbol')}})</th>
                                            <th class="trip-status">{{translate('trip_status')}}</th>
                                            <th class="action text-center">{{translate('action')}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($trips as $key => $trip)
                                            @php((($trip->current_status == 'completed' || $trip->current_status == 'failed') && $trip->driver_id)? $trip_amount = $trip->paid_fare : $trip_amount = $trip->estimated_fare )
                                            <tr>
                                                <td class="sl">{{$trips->firstItem() + $key}}</td>
                                                <td class="trip-id"># {{$trip->ref_id}}</td>
                                                <td class="text-nowrap date">{{date('d F Y', strtotime($trip->created_at))}}, <br /> {{date('h:i a', strtotime($trip->created_at))}}</td>
                                                @php($c_name = $trip->customer?->id ? $trip->customer?->first_name. ' ' . $trip->customer?->last_name : 'no_customer_assigned')
                                                <td class="customer-name"><a target="_blank"
                                                                             @if($trip->customer)
                                                                                 href="{{route('admin.customer.show', [$trip->customer?->id])}}"

                                                        @endif
                                                    >{{$c_name}}</a></td>
                                                @php($d_name = $trip->driver?->id ? $trip->driver?->first_name. ' ' . $trip->driver?->last_name : 'no_driver_assigned')
                                                <td class="text-capitalize driver"><a target="_blank">{{translate($d_name)}}</a>
                                                </td>
                                                <td class="trip-cost">{{$trip->paid_fare}}</td>
                                                <td class="coupon-discount">{{$trip->coupon_amount + 0}}</td>
                                                <td class="min-w200 text-capitalize additional-fee">
                                                    <div>{{translate('waiting_fee')}} : {{set_currency_symbol($trip->fee?->waiting_fee)}}</div>
                                                    <div>{{translate('idle_fee')}}: {{set_currency_symbol($trip->fee?->idle_fee)}}</div>
                                                    <div>{{translate('cancellation_fee')}}: {{set_currency_symbol($trip->fee?->cancellation_fee)}}</div>
                                                </td>
                                                <td class="total-trip-cost">{{$trip->actual_fare}}</td>
                                                <td class="admin-commission">{{set_currency_symbol($trip->fee?->admin_commission)}}</td>
                                                <td class="trip-status"><span class="badge badge-{{ $trip->current_status == 'completed'? 'primary' : 'warning' }}">{{translate($trip->current_status)}}</span></td>
                                                <td class="action">
                                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                                        <a href="{{route('admin.trip.show', ['type' => '', 'id' => $trip->id, 'page' => 'summary'])}}" class="btn btn-outline-info btn-action">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{route('admin.trip.restore', ['id' => $trip->id])}}" class="btn btn-outline-primary btn-action">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </a>
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

                                <div class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                                    <p class="mb-0"></p>
                                    {{$trips->render()}}
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

