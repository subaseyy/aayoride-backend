@extends('adminmodule::layouts.master')

@section('title', translate('earning_reports'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/apex/apexcharts.css')}}"/>
@endpush
@section('content')

    <div class="main-content">
        <div class="container-fluid">
            <h4 class="text-capitalize mb-3">{{ translate('Report Analytics') }}</h4>
            <div class="d-flex mb-3">
                <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{route('admin.report.earning')}}" class="nav-link active">Earning</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{route('admin.report.expense')}}" class="nav-link">Expense</a>
                    </li>
                </ul>
            </div>
            <div class="mb-4 row g-4">
                <div class="col-md-5">
                    <div class="card h-100">
                        <div
                            class="card-header d-flex flex-wrap justify-content-between gap-10 border-0 align-items-center pb-0">
                            <h5 class="text-capitalize m-0">{{translate('Earning Statistics')}}</h5>
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                                <select class="js-select" id="dateRangeForEarningStatistics">
                                    <option value="{{ALL_TIME}}" selected>{{translate(ALL_TIME)}}</option>
                                    <option value="{{TODAY}}">{{translate(TODAY)}}</option>
                                    <option value="{{PREVIOUS_DAY}}">{{translate(PREVIOUS_DAY)}}</option>
                                    <option value="{{LAST_7_DAYS}}">{{translate(LAST_7_DAYS)}}</option>
                                    <option value="{{THIS_WEEK}}">{{translate(THIS_WEEK)}}</option>
                                    <option value="{{THIS_MONTH}}">{{translate(THIS_MONTH)}}</option>
                                    <option value="{{LAST_MONTH}}">{{translate(LAST_MONTH)}}</option>
                                    <option value="{{THIS_YEAR}}">{{translate(THIS_YEAR)}}</option>
                                </select>
                            </div>
                            <div class="w-100 border-bottom pt-3"></div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-4">
                                <div class="flex-grow-1">
                                    <table
                                        class="table table-borderless align-middle table-hover text-nowrap trip-table">
                                        <thead class="table-light align-middle text-capitalize">
                                        <tr>
                                            <th>{{translate("Source")}}</th>
                                            <th>{{translate("Earnings")}}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-wrap align-items-center gap-1">
                                                    <span class="pt-1 pb-2 px-2 rounded-1 bg-warning"></span>
                                                    <span>{{translate("Parcel")}}</span>
                                                </div>
                                            </td>
                                            <td>{{getSession('currency_symbol')}}<span id="parcelEarning"></span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-wrap align-items-center gap-1">
                                                    <span class="pt-1 pb-2 px-2 rounded-1 bg-info"></span>
                                                    <span>{{translate("Ride Request")}}</span>
                                                </div>
                                            </td>
                                            <td >{{getSession('currency_symbol')}}<span id="rideEarning"></span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div>
                                    <div class="position-relative pie-chart">
                                        <div class="pie-placeholder"></div>
                                        <div id="dognut-pie" class="pie-chart-inner"></div>
                                        <div class="total--orders">
                                            <h4 class="text-uppercase mb-xxl-2">{{getSession('currency_symbol')}}<span id="totalEarning"></span></h4>
                                            <span class="text-capitalize">{{translate("Earnings")}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card h-100">
                        <div
                            class="card-header d-flex flex-wrap justify-content-between gap-10 pb-0 border-0 align-items-center">
                            <h5 class="text-capitalize m-0">{{translate('Zone Wise  Statistics')}}</h5>
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                                <select class="js-select" id="dateRange" name="date_range">
                                    <option value="{{ALL_TIME}}" selected>{{translate(ALL_TIME)}}</option>
                                    <option value="{{TODAY}}">{{translate(TODAY)}}</option>
                                    <option value="{{PREVIOUS_DAY}}">{{translate(PREVIOUS_DAY)}}</option>
                                    <option value="{{LAST_7_DAYS}}">{{translate(LAST_7_DAYS)}}</option>
                                    <option value="{{THIS_WEEK}}">{{translate(THIS_WEEK)}}</option>
                                    <option value="{{THIS_MONTH}}">{{translate(THIS_MONTH)}}</option>
                                    <option value="{{LAST_MONTH}}">{{translate(LAST_MONTH)}}</option>
                                    <option value="{{THIS_YEAR}}">{{translate(THIS_YEAR)}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body hide-apexcharts-tooltip-title hide-1st-line-of-chart hide-2nd-line-of-chart" id="updating_line_chart">
                            <div id="apex_line-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <h4 class="text-capitalize pt-2 mb-4">{{ translate('Trip Wise Earning') }}</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                        <form action="{{url()->current()}}" class="search-form search-form_style-two">
                            <div class="input-group search-form__input_group">
                                <span class="search-form__icon">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" value="{{request()->search}}" id="search" name="search"
                                       class="theme-input-style search-form__input"
                                       placeholder="{{translate('Search')}}">
                            </div>
                            <button type="submit" class="btn btn-primary search-submit">{{translate('Search')}}</button>
                        </form>

                        <div class="d-flex flex-wrap gap-3">
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    {{translate("download")}}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li>
                                        <a class="dropdown-item" target="_blank" href="{{route('admin.report.earningReportExport',['file' => 'excel', request()->getQueryString()])}}">
                                            {{translate("excel")}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-borderless align-middle table-hover text-nowrap trip-table">
                            <thead class="table-light align-middle text-capitalize">
                            <tr>
                                <th class="sl">{{translate("SL")}}</th>
                                <th>{{translate("Trip ID")}}</th>
                                <th>{{translate("Date")}}</th>
                                <th>{{translate("Zone")}}</th>
                                <th class="text-center">{{translate("Trip Type")}}</th>
                                <th class="text-end">{{translate("Total Trip Cost")}} ({{getSession('currency_symbol')}}
                                    )
                                </th>
                                <th class="text-end">{{translate("Commission Earning")}}
                                    ({{getSession('currency_symbol')}})
                                </th>
                                <th class="text-end">{{translate("Tax Collected")}}({{getSession('currency_symbol')}})
                                <th class="text-end">{{translate("Earning")}}({{getSession('currency_symbol')}})
                                </th>
                                <th class="text-center">{{translate("Action")}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($trips as $key => $trip)
                                <tr>
                                    <td>{{$trips->firstItem() + $key}}</td>
                                    <td><a href="{{route('admin.trip.show', ['id' => $trip->id, 'page' => 'summary'])}}">#{{$trip->ref_id}}</a></td>
                                    <td>
                                        {{date('d F Y', strtotime($trip->created_at))}},
                                        <br/> {{date('h:i a', strtotime($trip->created_at))}}
                                    </td>
                                    <td>{{$trip?->zone?->name}}</td>
                                    <td>
                                        @if($trip->type=="parcel")
                                            <div class="text-center">
                                                <span class="badge badge-warning">{{translate($trip->type)}}</span>
                                            </div>
                                        @else
                                            <div class="text-center">
                                                <span class="badge badge-info">{{translate($trip->type)}}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end">{{ getCurrencyFormat($trip->paid_fare) }}</td>
                                    <td class="text-end">{{ getCurrencyFormat($trip?->fee?->admin_commission-$trip?->fee?->vat_tax) }}</td>
                                    <td class="text-end">{{ getCurrencyFormat($trip?->fee?->vat_tax) }}</td>
                                    <td class="text-end">{{ getCurrencyFormat($trip?->fee?->admin_commission) }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2 align-items-center">
                                            <a target="_blank" href="{{route('admin.report.singleEarningReportExport',$trip->id)}}" class="btn btn-outline-primary btn-action">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12.6667 6H10V2H6V6H3.33333L8 11.3333L12.6667 6ZM2.66666 12.6667H13.3333V14H2.66666V12.6667Z"
                                                        fill="currentColor"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14">
                                        <div
                                            class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                            <img
                                                src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}"
                                                alt="" width="100">
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
                        {{$trips->render()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module/plugins/apex/apexcharts.min.js')}}"></script>
    <script>
        "use strict";
        let point = {{(int)getSession('currency_decimal_point') ?? 0}};
        $("#dateRange").on('change', function () {
            let date = $("#dateRange").val();
            dateZoneWiseEarningStatistics(date)
        })
        function dateZoneWiseEarningStatistics(date) {
            $.get({
                url: '{{route('admin.report.dateZoneWiseEarningStatistics')}}',
                dataType: 'json',
                data: {date: date},
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
                                name: "{{translate("Total Commission Earning")}}",
                                data: [0].concat(Object.values(response.totalAdminCommission))
                            },
                            {
                                name: "{{translate("Total Tax Earning")}}",
                                data: [0].concat(Object.values(response.totalVatTax))
                            },
                            {
                                name: "{{translate("Total Trips")}}",
                                data: [0].concat(Object.values(response.totalTripRequest))
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
                        colors: [ '#14B19E','#F4A164'],
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
                            strokeColors: [ '#14B19E','#F4A164'],
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
        dateZoneWiseEarningStatistics("{{ALL_TIME}}")
        $("#dateRangeForEarningStatistics").on('change', function () {
            let date = $("#dateRangeForEarningStatistics").val();
            dateRideTypeWiseEarningStatistics(date)
        })
        function abbreviateNumber(num) {
            if (num >= 1_000_000_000_000) {
                return (num / 1_000_000_000_000).toFixed(point) + 'T';
            } else if (num >= 1_000_000_000) {
                return (num / 1_000_000_000).toFixed(point) + 'B';
            } else if (num >= 1_000_000) {
                return (num / 1_000_000).toFixed(point) + 'M';
            } else if (num >= 1_000) {
                return (num / 1_000).toFixed(point) + 'K';
            } else {
                return num.toString();
            }
        }

        function dateRideTypeWiseEarningStatistics(date) {
            $.get({
                url: '{{route('admin.report.dateRideTypeWiseEarningStatistics')}}',
                dataType: 'json',
                data: {date: date},
                beforeSend: function () {
                    $('#resource-loader').show();
                },
                success: function (response) {
                    let parcelEarning = parseFloat(response.totalAdminCommission.parcel);
                    let rideEarning = parseFloat(response.totalAdminCommission.ride_request);
                    $("#parcelEarning").html(parcelEarning.toFixed(point))
                    $("#rideEarning").html(rideEarning.toFixed(point))
                    $("#totalEarning").html(abbreviateNumber((parcelEarning+rideEarning).toFixed(point)))
                    let options;
                    let chart;
                    if(parcelEarning > 0 || rideEarning > 0){
                        $('.pie-placeholder').hide()
                        $('.pie-chart-inner').css('opacity', '1');
                    } else {
                        $('.pie-placeholder').show();
                        $('.pie-chart-inner').css('opacity', '0');
                    }
                    options = {
                        series: [parcelEarning, rideEarning],
                        chart: {
                            width: 200,
                            type: 'donut',
                        },
                        labels: ['{{ translate('Parcel') }}', '{{ translate('Ride Request') }}'],
                        dataLabels: {
                            enabled: false,
                            style: {
                                colors: ['#FFA84A', '#0177CD']
                            }
                        },
                        responsive: [{
                            breakpoint: 1650,
                            options: {
                                chart: {
                                    width: 240
                                },
                            }
                        }],
                        colors: ['#FFA84A', '#0177CD'],
                        fill: {
                            colors: ['#FFA84A', '#0177CD']
                        },
                        legend: {
                            show: false
                        },
                    };

                    chart = new ApexCharts(document.querySelector("#dognut-pie"), options);
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
        dateRideTypeWiseEarningStatistics("{{ALL_TIME}}")

    </script>
@endpush
