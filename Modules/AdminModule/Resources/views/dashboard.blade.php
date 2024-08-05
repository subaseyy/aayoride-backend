@section('title', translate('dashboard'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/apex/apexcharts.css')}}"/>
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="row align-items-center mb-3 g-2">
                <div class="col-12">
                    <div class="media gap-3">
                        <img width="38" src="{{asset('public/assets/admin-module/img/media/car.png')}}" loading="eager"
                             alt="">
                        <div class="media-body text-dark">
                            <h4 class="mb-1">{{ translate('welcome')}} {{auth('web')->user()?->first_name}}</h4>
                            <p class="fs-12 text-capitalize">{{ translate('monitor_your')}}
                                <strong>{{ getSession('business_name') ?? 'DriveMond' }}</strong> {{ translate('business_statistics')}}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @can('dashboard')
                <div class="row gy-4">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="p-30">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="level-status fs-5 p-2 bg-info w-48 aspect-1 mb-3">
                                                <img src="{{asset('public/assets/admin-module/img/svg/user-grp.svg')}}"
                                                     class="svg" alt="">
                                            </div>
                                            <h3 class="fs-24">{{$customers}}</h3>
                                            <div class="title-color text-capitalize">{{ translate('Total Active Customers')}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="p-30">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="level-status fs-5 p-2 bg-warning w-48 aspect-1 mb-3">
                                                <img src="{{asset('public/assets/admin-module/img/svg/earning.svg')}}"
                                                     class="svg" alt="">
                                            </div>
                                            <h3 class="fs-24">{{ set_currency_symbol($superAdminAccount->received_balance + $superAdminAccount->receivable_balance) }}</h3>
                                            <div class="title-color text-capitalize">{{ translate('Total Earnings')}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="p-30">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="level-status text-white fs-5 p-2 w-48 aspect-1 mb-3">
                                                <img width="24"
                                                     src="{{asset('public/assets/admin-module/img/svg/driver2.svg')}}"
                                                     class="svg" alt="">
                                            </div>
                                            <h3 class="fs-24">{{$drivers}}</h3>
                                            <div class="title-color text-capitalize">{{ translate('Total Active Drivers')}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="p-30">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="level-status fs-5 p-2 bg-success w-48 aspect-1 mb-3">
                                                <img src="{{asset('public/assets/admin-module/img/svg/revenue.svg')}}"
                                                     class="svg" alt="">
                                            </div>
                                            <h3 class="fs-24">{{ $totalTrips }}</h3>
                                            <div class="title-color text-capitalize">{{ translate('Total Trips')}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-3 h-100">
                            <div class="card-header d-flex flex-wrap justify-content-between gap-10">
                                <div class="d-flex flex-column gap-1">
                                    <h6 class="text-capitalize">{{ translate('zone-wise_trip_statistics')}}</h6>
                                    <p>{{ translate('total')}} {{$zones->count()}} {{ translate('zone')}}</p>
                                </div>
                                <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                                    <select class="js-select" id="zoneWiseRideDate">
                                        <option disabled>{{ translate('Select_Duration')}}</option>
                                        <option value="today" {{ env('APP_MODE') != 'demo' ? "selected" : "" }}>{{ translate('today')}}</option>
                                        <option value="previous_day">{{ translate('Previous_Day')}}</option>
                                        <option value="this_week">{{translate('This_Week')}}</option>
                                        <option value="this_month">{{translate('This_Month')}}</option>
                                        <option value="last_7_days">{{translate('Last_7_Days')}}</option>
                                        <option value="last_week">{{translate('Last_Week')}}</option>
                                        <option value="last_month">{{translate('Last_Month')}}</option>
                                        <option value="all_time" {{ env('APP_MODE') != 'demo' ?  "" : "selected" }}>{{translate('All_Time')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="load-all-data">
                                    <div id="zoneWiseTripStatistics"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admin Earning Statistics -->
                <div class="card my-3">
                    <div class="card-header d-flex flex-wrap justify-content-between gap-10">
                        <div class="d-flex flex-column gap-1">
                            <h5 class="text-capitalize">{{translate('admin_earning_statistics')}}</h5>
                            <p>{{translate('total')}} {{$zones->count()}} {{translate('zone')}}</p>
                        </div>
                        <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                            <select class="js-select" id="rideZone">
                                <option disabled>{{translate('Select_Area')}}</option>
                                <option selected value="all">{{translate('all')}}</option>
                                @forelse($zones as $zone)
                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                @empty
                                @endforelse
                            </select>
                            <select class="js-select" id="rideDate">
                                <option disabled>{{translate('Select_Duration')}}</option>
                                <option value="all_time" {{ env('APP_MODE') != 'demo' ? "" : "selected" }}>{{translate('All_Time')}}</option>
                                <option value="today" {{ env('APP_MODE') != 'demo' ? "selected" : "" }}>{{translate('today')}}</option>
                                <option value="previous_day">{{translate('Previous_Day')}}</option>
                                <option value="this_week">{{translate('This_Week')}}</option>
                                <option value="last_7_days">{{translate('Last_7_Days')}}</option>
                                <option value="this_month">{{translate('This_Month')}}</option>
                                <option value="last_month">{{translate('last_month')}}</option>
                                <option value="this_year">{{translate('this_year')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body hide-2nd-line-of-chart" id="updating_line_chart">
                        <div id="apex_line-chart"></div>
                    </div>
                </div>
                <!-- End Admin Earning Statistics -->

                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <h5 class="text-capitalize">{{translate('leader_board')}}</h5>
                                    <span class="badge bg-primary">{{translate('driver')}}</span>
                                </div>


                                <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button value="today"
                                                class="nav-link text-capitalize leader-board-driver {{ env('APP_MODE') != 'demo' ? "active" : "" }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#today-tab-pane" aria-selected="{{ env('APP_MODE') != 'demo' ? "true" : "false" }}"
                                                role="tab">{{translate('today')}}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button value="this_week"
                                                class="nav-link text-capitalize leader-board-driver"
                                                data-bs-toggle="tab"
                                                data-bs-target="#week-tab-pane" aria-selected="false"
                                                role="tab" tabindex="-1">{{translate('this_week')}}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button value="this_month"
                                                class="nav-link text-capitalize leader-board-driver"
                                                data-bs-toggle="tab"
                                                data-bs-target="#month-tab-pane" aria-selected="false"
                                                role="tab" tabindex="-1">{{translate('this_month')}}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button value="all_time"
                                                class="nav-link text-capitalize leader-board-driver {{ env('APP_MODE') != 'demo' ? "" : "active" }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#all-time-tab-pane" aria-selected="{{ env('APP_MODE') != 'demo' ? "false" : "true" }}"
                                                role="tab" tabindex="-1">{{translate('all_time')}}</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div id="leader-board-driver"></div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- Recent Transaction -->
                        <div class="card recent-transactions max-h-460px">
                            <div class="card-header">
                                <h4 class="mb-2">{{translate('recent_transactions')}}</h4>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-arrow-up text-primary"></i>
                                        <p class="opacity-75">{{ translate('last') }} {{$transactions->count()}} {{ translate('transactions_this_month') }}</p>
                                    </div>
                                    <a href="{{route('admin.transaction.index')}}"
                                       class="btn-link text-capitalize">{{translate('view_all')}}</a>
                                </div>

                            </div>
                            <div class="card-body overflow-y-auto">

                                <div class="events">
                                    @forelse ($transactions as $transaction)
                                        <div class="event">
                                            <div class="knob"></div>
                                            <div class="title">
                                                @if($transaction->debit>0)
                                                    <h5>{{ getCurrencyFormat($transaction->debit ?? 0) }} Debited
                                                        from {{ucwords(str_replace('_',' ', $transaction->account))}}</h5>
                                                @else
                                                    <h5>{{ getCurrencyFormat($transaction->credit ?? 0) }} Credited
                                                        to {{ucwords(str_replace('_',' ', $transaction->account))}}</h5>
                                                @endif
                                            </div>
                                            @php($time_format = getSession('time_format'))
                                            <div class="description">
                                                <p>{{date(DATE_FORMAT,strtotime($transaction->created_at))}}</p>
                                            </div>
                                        </div>
                                    @empty

                                    @endforelse
                                    <div class="line"></div>
                                </div>
                            </div>
                        </div>
                        <!-- End Recent Transaction -->
                    </div>
                </div>
                <div class="row g-3 pt-3">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div class="d-flex flex-column align-items-start gap-1">
                                    <h5 class="text-capitalize">{{translate('leader_board')}}</h5>
                                    <span class="badge bg-primary">{{translate('customer')}}</span>
                                </div>

                                <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button value="today"
                                                class="nav-link text-capitalize leader-board-customer {{ env('APP_MODE') != 'demo' ? "active" : "" }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#today-tab-pane" aria-selected="{{ env('APP_MODE') != 'demo' ? "true" : "false" }}"
                                                role="tab">{{translate('today')}}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button value="this_week"
                                                class="nav-link text-capitalize leader-board-customer"
                                                data-bs-toggle="tab"
                                                data-bs-target="#today-tab-pane" aria-selected="false"
                                                role="tab">{{translate('this_week')}}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button value="this_month"
                                                class="nav-link text-capitalize leader-board-customer"
                                                data-bs-toggle="tab"
                                                data-bs-target="#today-tab-pane" aria-selected="false"
                                                role="tab">{{translate('this_month')}}</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button value="all_time"
                                                class="nav-link text-capitalize leader-board-customer {{ env('APP_MODE') != 'demo' ? "" : "active" }}"
                                                data-bs-toggle="tab"
                                                data-bs-target="#today-tab-pane" aria-selected="{{ env('APP_MODE') != 'demo' ? "false" : "true" }}"
                                                role="tab">{{translate('all_time')}}</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div id="leader-board-customer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <!-- Recent Trips Activity -->
                        <div class="card recent-activities max-h-460px">
                            <div class="card-header d-flex justify-content-between gap-10">
                                <div class="d-flex flex-column gap-1">
                                    <h5 class="text-capitalize">{{translate('recent_trips_activity')}}</h5>
                                    <p class="text-capitalize">{{translate('all_activities')}}</p>
                                </div>
                                <a href="{{route('admin.trip.index', ['all'])}}"
                                   class="btn-link text-capitalize">{{translate('view_all')}}</a>
                            </div>
                            <div class="card-body overflow-y-auto" id="recent_trips_activity">
                            </div>
                        </div>
                        <!-- End Recent Trips Activity -->
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection

@push('script')
    <!-- Apex Chart -->
    <script src="{{asset('public/assets/admin-module/plugins/apex/apexcharts.min.js')}}"></script>
    <script src="{{asset('public/assets/admin-module/js/admin-module/dashboard.js')}}"></script>
    <!-- Google Map -->

    <script>
        "use strict";

        $(".leader-board-customer").on('click', function () {
            let data = $(this).val();
            loadPartialView('{{route('admin.leader-board-customer')}}', '#leader-board-customer', data)
        })
        $(".leader-board-driver").on('click', function () {
            let data = $(this).val();
            loadPartialView('{{route('admin.leader-board-driver')}}', '#leader-board-driver', data)
        })


        $("#rideZone,#rideDate").on('change', function () {
            let date = $("#rideDate").val();
            let zone = $("#rideZone").val();
            adminEarningStatistics(date, zone)
        })

        function adminEarningStatistics(date, zone = null) {
            $.get({
                url: '{{route('admin.earning-statistics')}}',
                dataType: 'json',
                data: {date: date, zone: zone},
                beforeSend: function () {
                    $('#resource-loader').show();
                },
                success: function (response) {

                    let hours = response.label;
                    // Remove double quotes from each string value
                    hours = hours.map(function (hour) {
                        return hour.replace(/"/g, '');
                    });
                    document.getElementById('apex_line-chart').remove();
                    let graph = document.createElement('div');
                    graph.setAttribute("id", "apex_line-chart");
                    document.getElementById("updating_line_chart").appendChild(graph);
                    let options = {
                        series: [
                            {
                                name: "Total Trips",
                                data: [0].concat(Object.values(response.totalTripRequest))
                            },
                            {
                                name: "Admin Commission",
                                data: [0].concat(Object.values(response.totalAdminCommission))
                            }
                        ],
                        chart: {
                            height: 366,
                            type: 'line',
                            dropShadow: {
                                enabled: true,
                                color: '#000',
                                top: 18,
                                left: 0,
                                blur: 10,
                                opacity: 0.1
                            },
                            toolbar: {
                                show: false
                            }
                        },
                        colors: ['#F4A164', '#14B19E'],
                        dataLabels: {
                            enabled: false,
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 2,
                        },
                        grid: {
                            yaxis: {
                                lines: {
                                    show: true
                                }
                            },
                            borderColor: '#ddd',
                        },
                        markers: {
                            size: 2,
                            strokeColors: ['#F4A164', '#14B19E'],
                            strokeWidth: 1,
                            fillOpacity: 0,
                            hover: {
                                sizeOffset: 2
                            }
                        },
                        theme: {
                            mode: 'light',
                        },
                        xaxis: {
                            categories: ['00'].concat(hours),
                            labels: {
                                offsetX: 0,
                            },
                        },
                        legend: {
                            show: false,
                            position: 'bottom',
                            horizontalAlign: 'left',
                            floating: false,
                            offsetY: -10,
                            itemMargin: {
                                vertical: 10
                            },
                        },
                        yaxis: {
                            tickAmount: 10,
                            labels: {
                                offsetX: 0,
                            },
                        }
                    };

                    if (localStorage.getItem('dir') === 'rtl') {
                        options.yaxis.labels.offsetX = -20;
                    }

                    let chart = new ApexCharts(document.querySelector("#apex_line-chart"), options);
                    chart.render();
                },
                complete: function () {
                    $('#resource-loader').hide();
                },
                error: function (xhr, status, error) {
                    let err = eval("(" + xhr.responseText + ")");
                    // alert(err.Message);
                    $('#resource-loader').hide();
                    toastr.error('{{translate('failed_to_load_data')}}')
                },
            });

        }

        $("#zoneWiseRideDate").on('change', function () {
            let date = $("#zoneWiseRideDate").val()
            zoneWiseTripStatistics(date)
        })

        function zoneWiseTripStatistics(date) {
            $.get({
                url: '{{route('admin.zone-wise-statistics')}}',
                dataType: 'json',
                data: {date: date},
                beforeSend: function () {
                    $('#resource-loader').show();
                },
                success: function (response) {
                    $('#zoneWiseTripStatistics').empty().html(response)
                },
                complete: function () {
                    $('#resource-loader').hide();
                },
                error: function (xhr, status, error) {
                    $('#resource-loader').hide();
                    toastr.error('{{translate('failed_to_load_data')}}')
                },
            });

        }

        // partial view
        loadPartialView('{{route('admin.recent-trip-activity')}}', '#recent_trips_activity', null);
        loadPartialView('{{route('admin.leader-board-driver')}}', '#leader-board-driver', '{{ env('APP_MODE') != 'demo' ? "today" : "all_time" }}');
        loadPartialView('{{route('admin.leader-board-customer')}}', '#leader-board-customer', '{{ env('APP_MODE') != 'demo' ? "today" : "all_time" }}');
        zoneWiseTripStatistics(document.getElementById('zoneWiseRideDate').value);
        adminEarningStatistics('{{ env('APP_MODE') != 'demo' ? "today" : "all_time" }}', 'all')

    </script>
    @include('adminmodule::partials.dashboard.map')

@endpush
