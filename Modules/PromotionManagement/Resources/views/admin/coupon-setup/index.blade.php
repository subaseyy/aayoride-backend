@section('title', translate('coupon_List'))

@extends('adminmodule::layouts.master')

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('promotion_view')
                <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-3 mb-sm-2">
                    <h2 class="fs-22 text-capitalize">{{translate('coupon_overview')}}</h2>
                    <div>
                        @php($queryString = request()->getQueryString())
                        <select name="date_range" id="dateRange" class="js-select date-range-change" data-url="{{url()->full()}}">
                            <option
                                value="all_time" {{$dateRangeValue == 'all_time' || $dateRangeValue == null?'selected':''}} selected>{{translate('all_time')}}</option>
                            <option
                                value="today" {{$dateRangeValue == 'today'?'selected':''}}>{{translate('today')}}</option>
                            <option
                                value="this_week" {{$dateRangeValue == 'this_week'?'selected':''}}>{{translate('This_Week')}}</option>
                            <option
                                value="this_month" {{$dateRangeValue == 'this_month'?'selected':''}}>{{translate('This_Month')}}</option>
                            <option
                                value="this_year" {{$dateRangeValue == 'this_year'?'selected':''}}>{{translate('This_Year')}}</option>
                        </select>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card border-0 h-100">
                            <div class="card-body">
                                <h6 class="mb-3">{{$dateRangeValue != null ? ucwords(str_replace('_', ' ', $dateRangeValue)) : translate('all_time') }} <i class="bi bi-arrow-up-circle text-primary ms-2"></i></h6>
                                <div class="row g-4">
                                    <div class="col-sm-6">
                                        <div class="card m-0">
                                            <div class="card-body text-center">
                                                <div class="py-4 py-sm-5">
                                                    <div class="d-flex justify-content-center mb-3">
                                                        <div class="level-status fs-5 p-2 bg-primary w-36 aspect-1">
                                                            %
                                                        </div>
                                                    </div>
                                                    <h2 class="mb-3 fs-22"> {{set_currency_symbol($cardValues['total_coupon_amount'])}}</h2>
                                                    <h6 class="text-muted text-capitalize">{{translate('total_coupon_amount_given')}}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card m-0">
                                            <div class="card-body text-center">
                                                <div class="py-4 py-sm-5">
                                                    <div class="d-flex justify-content-center mb-3">
                                                        <div class="level-status fs-5 p-2 bg-primary w-36 aspect-1">
                                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M6.34245 13.7083C6.0532 13.7092 5.76665 13.6526 5.49941 13.5419C5.23218 13.4312 4.98955 13.2686 4.7856 13.0635C4.51441 12.7984 4.31794 12.4665 4.21602 12.1012C4.1141 11.7359 4.11034 11.3503 4.20511 10.9831C4.25049 10.8184 4.25143 10.6447 4.20782 10.4795C4.16421 10.3144 4.07762 10.1637 3.95686 10.043C3.8361 9.92217 3.68548 9.83554 3.52035 9.79189C3.35522 9.74824 3.18147 9.74913 3.0168 9.79447C2.65009 9.88939 2.26484 9.88594 1.89989 9.78448C1.53494 9.68301 1.2032 9.48712 0.938104 9.21655C0.525305 8.80334 0.29329 8.24324 0.292969 7.65917C0.292649 7.07509 0.52405 6.51474 0.936395 6.10108L6.10095 0.936522C6.52029 0.536146 7.07778 0.312744 7.65756 0.312744C8.23734 0.312744 8.79482 0.536146 9.21416 0.936522C9.48534 1.2016 9.68181 1.53352 9.78372 1.89879C9.88564 2.26406 9.8894 2.64974 9.79462 3.01693C9.74961 3.18164 9.74887 3.35532 9.79248 3.52041C9.83609 3.6855 9.92251 3.83615 10.043 3.95715L10.0436 3.95744C10.1644 4.07798 10.3149 4.16442 10.4799 4.208C10.6448 4.25157 10.8184 4.25071 10.983 4.20552C11.3497 4.11066 11.7349 4.11414 12.0998 4.2156C12.4648 4.31706 12.7965 4.51292 13.0616 4.78345C13.4744 5.19666 13.7065 5.75675 13.7068 6.34083C13.7071 6.92491 13.4757 7.48526 13.0634 7.89893L7.89879 13.0635C7.69491 13.2685 7.45237 13.4311 7.18522 13.5418C6.91808 13.6525 6.63163 13.7091 6.34245 13.7083ZM3.27087 8.59362C3.59938 8.59312 3.92357 8.66851 4.21815 8.81393C4.51273 8.95934 4.76973 9.17083 4.96912 9.43192C5.1685 9.69301 5.30488 9.99663 5.36761 10.3191C5.43034 10.6416 5.41773 10.9742 5.33076 11.291C5.29003 11.4591 5.29428 11.6349 5.34308 11.8009C5.39188 11.9668 5.48351 12.117 5.60876 12.2363C5.80544 12.4258 6.06778 12.5318 6.34088 12.5322C6.61398 12.5327 6.87666 12.4274 7.07393 12.2386L12.2385 7.07408C12.4322 6.87983 12.541 6.61669 12.541 6.34235C12.541 6.06802 12.4322 5.80487 12.2385 5.61062C12.1192 5.48458 11.9686 5.39234 11.8021 5.34326C11.6357 5.29419 11.4592 5.29003 11.2906 5.3312C10.9273 5.43075 10.5442 5.43237 10.1801 5.3359C9.81607 5.23943 9.48402 5.04831 9.21773 4.78196C8.95144 4.51561 8.7604 4.18352 8.66402 3.81942C8.56764 3.45533 8.56935 3.07221 8.66898 2.709C8.70971 2.54089 8.70546 2.36502 8.65666 2.19907C8.60786 2.03313 8.51623 1.88295 8.39099 1.76365C8.1943 1.57417 7.93195 1.46813 7.65884 1.46771C7.38574 1.46728 7.12306 1.57252 6.92579 1.76139L1.76126 6.92591C1.56755 7.12017 1.45878 7.38331 1.45878 7.65764C1.45878 7.93198 1.56755 8.19512 1.76126 8.38937C1.88054 8.51548 2.03107 8.60776 2.19756 8.65684C2.36406 8.70592 2.54058 8.71004 2.70918 8.66879C2.89225 8.61897 3.08113 8.59369 3.27087 8.59362Z" fill="white"/>
                                                                <path d="M6.99984 9.33329C6.92323 9.33331 6.84736 9.31824 6.77658 9.28893C6.7058 9.25962 6.64149 9.21665 6.58731 9.16248C6.53314 9.10831 6.49018 9.044 6.46087 8.97321C6.43156 8.90243 6.41648 8.82657 6.4165 8.74996V5.24996C6.4165 5.09525 6.47796 4.94688 6.58736 4.83748C6.69675 4.72808 6.84513 4.66663 6.99984 4.66663C7.15455 4.66663 7.30292 4.72808 7.41232 4.83748C7.52171 4.94688 7.58317 5.09525 7.58317 5.24996V8.74996C7.58319 8.82657 7.56812 8.90243 7.53881 8.97321C7.5095 9.044 7.46653 9.10831 7.41236 9.16248C7.35819 9.21665 7.29388 9.25962 7.22309 9.28893C7.15231 9.31824 7.07645 9.33331 6.99984 9.33329Z" fill="white"/>
                                                                <path d="M5.35026 7.52266C5.75297 7.52266 6.07943 7.19621 6.07943 6.7935C6.07943 6.39079 5.75297 6.06433 5.35026 6.06433C4.94755 6.06433 4.62109 6.39079 4.62109 6.7935C4.62109 7.19621 4.94755 7.52266 5.35026 7.52266Z" fill="white"/>
                                                                <path d="M8.64958 7.93563C9.05228 7.93563 9.37874 7.60917 9.37874 7.20646C9.37874 6.80375 9.05228 6.47729 8.64958 6.47729C8.24687 6.47729 7.92041 6.80375 7.92041 7.20646C7.92041 7.60917 8.24687 7.93563 8.64958 7.93563Z" fill="white"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <h2 class="mb-3 fs-22">{{$cardValues['total_active']}}</h2>
                                                    <h6 class="text-muted text-capitalize">{{translate('active_Coupon_Offer_Running')}}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 mb-3">
                            <div class="card-header d-flex flex-wrap justify-content-between gap-10">
                                <div class="d-flex flex-column gap-1">
                                    <h5 class="text-primary">{{translate('coupon_analytics')}}</h5>
                                    <p>Monitor coupon statistics</p>
                                </div>
                                <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                                    <h6
                                        class="fs-12 text-dark">{{$dateRangeValue != null ? ucwords(str_replace('_', ' ', $dateRangeValue)) : translate('all_time') }}</h6>
                                </div>
                            </div>
                            <div class="">
                                <div id="apex_column-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="row g-4 mt-1">
                <div class="col-12">
                    <h2 class="ffs-22 mt-4 text-capitalize">{{ translate('all_coupon') }}</h2>

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{!request()->has('status') || request()->get('status')=='all'?'active':''}}"
                                   href="{{url()->current()}}?status=all">
                                    {{ translate('all') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{request()->get('status')=='active'?'active':''}}"
                                   href="{{url()->current()}}?status=active">
                                    {{ translate('active') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{request()->get('status')=='inactive'?'active':''}}"
                                   href="{{url()->current()}}?status=inactive">
                                    {{ translate('inactive') }}
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_coupons') }} : </span>
                            <span class="text-primary fs-16 fw-bold"
                                  id="total_record_count">{{ $coupons->total() }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="{{url()->full()}}"
                                      class="search-form search-form_style-two" method="GET">
                                    @foreach(request()->query() as $key => $value)
                                        @if ($key !== 'search') <!-- Exclude search parameter -->
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        <input type="search" class="theme-input-style search-form__input"
                                               value="{{request()->get('search')}}" name="search" id="search"
                                               placeholder="{{translate('search_here_by_Coupon_Title')}}">
                                    </div>
                                    <button type="submit"
                                            class="btn btn-primary">{{ translate('search') }}</button>
                                </form>


                                <div class="d-flex flex-wrap gap-3">
                                    @can('super-admin')
                                        <a href="{{ route('admin.promotion.coupon-setup.index',['status'=>request('status')]) }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>

                                        <a href="{{ route('admin.promotion.coupon-setup.trashed') }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                            <i class="bi bi-recycle"></i>
                                        </a>
                                    @endcan

                                    @can('promotion_log')
                                        <a href="{{route('admin.promotion.coupon-setup.log')}}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                            <i class="bi bi-clock-fill"></i>
                                        </a>
                                    @endcan

                                    @can('promotion_export')
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-download"></i>
                                                {{ translate('download') }}
                                                <i class="bi bi-caret-down-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                <li><a class="dropdown-item"
                                                       href="{{route('admin.promotion.coupon-setup.export')}}?status={{request()->get('status') ?? "all"}}&&file=excel">{{translate('excel')}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endcan

                                    @can('promotion_add')
                                        <a href="{{route('admin.promotion.coupon-setup.create')}}" type="button"
                                           class="btn btn-primary text-capitalize">
                                            <i class="bi bi-plus fs-16"></i> {{ translate('add_coupon') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle table-hover text-nowrap text-center">
                                    <thead class="table-light align-middle text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th class="coupon_title">{{ translate('coupon_title') }}</th>
                                        <th class="coupon_type">{{ translate('coupon_code') }}</th>
                                        <th class="coupon_type">{{ translate('coupon_type') }}</th>
                                        <th class="coupon_amount">{{ translate('coupon_amount') }}</th>
                                        <th class="duration">{{ translate('duration') }}</th>
                                        <th class="total_times_used">{{ translate('total_times_used') }}</th>
                                        <th class="total_coupon_amount">{{ translate('total_coupon') }}
                                            <br> {{ translate('amount') }}
                                            ({{session()->get('currency_symbol') ?? '$'}})
                                        </th>
                                        <th class="average_coupon">{{ translate('average_coupon_amount') }}</th>
                                        <th class="coupon_status">{{ translate('coupon_status') }}</th>
                                        @can('promotion_edit')
                                            <th class="status">{{ translate('status') }}</th>
                                        @endcan
                                        <th class="text-center action">{{ translate('action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($coupons as $key => $coupon)
                                        <tr id="hide-row-{{$coupon->id}}" class="record-row">
                                            <td>{{ $coupons->firstItem() + $key }}</td>
                                            <td class="coupon_title">{{ $coupon->name }}</td>
                                            <td class="coupon_title">{{ $coupon->coupon_code }}</td>
                                            <td class="coupon_type text-capitalize">{{ str_replace('_', ' ', $coupon->coupon_type) }}</td>
                                            <td class="coupon_amount">{{ $coupon->amount_type == 'percentage'? $coupon->coupon.'%': set_currency_symbol($coupon->coupon) }}</td>
                                            <td class="duration" class="text-capitalize vehicle-features">
                                                {{translate('start')}} : {{$coupon->start_date}} <br>
                                                {{translate('end')}} : {{$coupon->end_date}} <br>
                                                {{translate('duration')}}
                                                : {{ Carbon\Carbon::parse($coupon->end_date)->diffInDays($coupon->start_date)}}
                                                Days
                                            </td>
                                            <td class="total_times_used">{{ (int)$coupon->total_used }}</td>
                                            <td class="total_coupon_amount">{{ set_currency_symbol(round($coupon->total_amount,2)) }}</td>
                                            <td class="average_coupon">{{ set_currency_symbol(round($coupon->total_used > 0?($coupon->total_amount/$coupon->total_used):0,2)) }}</td>
                                            <td class="coupon_status">
                                                @php($date = Carbon\Carbon::now()->startOfDay())
                                                @if($date->gt($coupon->end_date))
                                                    <span
                                                        class="badge badge-danger">{{ translate(EXPIRED) }}</span>
                                                @elseif (!$coupon->is_active)
                                                    <span
                                                        class="badge badge-warning">{{ translate(CURRENTLY_OFF) }}</span>
                                                @elseif ($date->lt($coupon->start_date))
                                                    <span
                                                        class="badge badge-info">{{ translate(UPCOMING) }}</span>
                                                @elseif ($date->lte($coupon->end_date))
                                                    <span
                                                        class="badge badge-success">{{ translate(RUNNING) }}</span>
                                                @endif
                                            </td>
                                            @can('promotion_edit')
                                                <td class="status">
                                                    <label class="switcher mx-auto">
                                                        @if($date->gt($coupon->end_date))
                                                            <input class="switcher_input status-change"
                                                                   data-url={{ route('admin.promotion.coupon-setup.status') }} id="{{ $coupon->id }}"
                                                                   type="checkbox" disabled>
                                                            <span class="switcher_control" title="{{ translate('Coupon already completed, You do not change this status.') }}"></span>
                                                        @else
                                                            <input class="switcher_input status-change"
                                                                   data-url={{ route('admin.promotion.coupon-setup.status') }} id="{{ $coupon->id }}"
                                                                   type="checkbox" {{$coupon->is_active?'checked':''}}>
                                                            <span class="switcher_control"></span>
                                                        @endif
                                                    </label>
                                                </td>
                                            @endcan
                                            <td class="action">
                                                <div class="d-flex justify-content-center gap-2 align-items-center">
                                                    @can('promotion_log')
                                                        <a href="{{route('admin.promotion.coupon-setup.log')}}?id={{$coupon->id}}"
                                                           class="btn btn-outline-primary btn-action"
                                                           title="{{ translate('history_log') }}">
                                                            <i class="bi bi-clock-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('promotion_edit')
                                                        <a href="{{route('admin.promotion.coupon-setup.edit', ['id'=>$coupon->id])}}"
                                                           class="btn btn-outline-info btn-action"
                                                           title="{{ translate('edit_coupon') }}">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('promotion_delete')
                                                        <button title="{{ translate('delete_coupon') }}"
                                                                data-id="delete-{{ $coupon->id }}"
                                                                data-message="{{ translate('want_to_delete_this_coupon._?') }} {{ translate('You_can_recover_it_from_the_“Deleted_Coupon”_section') }}"
                                                                type="button"
                                                                class="btn btn-outline-danger btn-action form-alert">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>

                                                        <form
                                                            action="{{ route('admin.promotion.coupon-setup.delete', ['id'=>$coupon->id]) }}"
                                                            id="delete-{{ $coupon->id }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                        </form>
                                                    @endcan
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

                            <div class="d-flex justify-content-end">
                                {!! $coupons->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <!-- Apex Chart -->
    <script src="{{asset('public/assets/admin-module/plugins/apex/apexcharts.min.js')}}"></script>
    <script>
        "use strict";


        // Convert PHP array to JavaScript array
        let hours = [<?php echo implode(',', $label); ?>];

        // Remove double quotes from each string value
        hours = hours.map(function (hour) {
            return hour.replace(/"/g, '');
        });

        let options = {
            series: [{
                name: 'Coupon Amount Apply',
                data: [{{ implode(",",$data) }}],
            }],
            chart: {
                type: 'bar',
                height: 214,
                toolbar: {
                    show: false
                }
            },
            colors: ['#14B19E'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '25%',
                    endingShape: 'rounded',
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: hours,
                axisBorder: {
                    show: false,
                },
            },
            fill: {
                opacity: 1
            },
            grid: {
                yaxis: {
                    lines: {
                        show: false
                    }
                },
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    plotOptions: {
                        bar: {
                            columnWidth: '55%',
                            columnHeight: '55%',
                        },
                    },
                }
            }],
        };

        let chart = new ApexCharts(document.querySelector("#apex_column-chart"), options);
        chart.render();
    </script>
@endpush
