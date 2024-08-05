@extends('adminmodule::layouts.master')

@section('title', translate('Trips'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{ translate('trip_list')}}</h2>

            <div class="row g-4">
                @include('tripmanagement::admin.trip.partials._trip-inline-menu')
            </div>

            <div class="d-flex flex-wrap justify-content-lg-end gap-3 mt-4 mb-2">
                @if($type == 'all')
                <div class="dropdown custom-date-dropdown">
                    <select name="date_range" id="date-range" class="js-select btn btn-outline-primary form-control">
                        {{-- <option disabled selected class="text-primary">{{translate('Select_Date_Range')}}</option> --}}
                        <option value="all_time" class="text-primary" selected>{{translate('all_time')}}</option>
                        <option value="today">{{translate('today')}}</option>
                        <option value="previous_day">{{translate('previous_day')}}</option>
                        <option value="this_week">{{translate('this_week')}}</option>
                        <option value="this_month">{{translate('this_month')}}</option>
                        <option value="last_7_days">{{translate('last_7_days')}}</option>
                        <option value="last_week">{{translate('last_week')}}</option>
                        <option value="last_month">{{translate('last_month')}}</option>
                    </select>
                </div>
                @endif

                <div id="data-input" class="d-none">
                    <input class="btn btn-outline-primary show-calender me-3" id="start_date" type="date">
                    <input onchange="getDate()" class="btn btn-outline-primary show-calender" id="end_date" type="date">
                </div>
            </div>
            @if($type == 'all')
                <div id="trip-stats">
                    @include('tripmanagement::admin.trip.partials._trip-list-stat')
                </div>
            @endif
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mt-30 mb-3 gap-3">
                        <h2 class="fs-22 text-capitalize">{{translate('all_trips')}}</h2>

                        <div class="d-flex align-items-center gap-2 text-capitalize">
                            <span class="text-muted">{{translate('total_trips')}} : </span>
                            <span class="text-primary fs-16 fw-bold" id="">{{$trips->total()}}</span>
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
                                        <input type="search" name="search" value="{{request()->search}}" class="theme-input-style search-form__input" placeholder="{{translate('Search_here_by_Trip_ID')}}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                </form>

                                <div class="d-flex flex-wrap gap-3">
                                    @can('super-admin')
                                        <a href="{{ route('admin.trip.index', ['type' => request('type')]) }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>

{{--                                        <a href="{{ route('admin.trip.trashed') }}"--}}
{{--                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">--}}
{{--                                            <i class="bi bi-recycle"></i>--}}
{{--                                        </a>--}}
                                    @endcan
                                    @can('trip_log')
                                    <a href="{{route('admin.trip.log')}}" class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                        <i class="bi bi-clock-fill"></i>
                                    </a>
                                    @endcan

                                    @can('trip_export')
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                            <i class="bi bi-download"></i>
                                            {{translate('download')}}
                                            <i class="bi bi-caret-down-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <li><a class="dropdown-item" href="{{route('admin.trip.export')}}?search={{$search}}&&type={{$type}}&&file=excel">{{ translate('excel') }}</a></li>
                                        </ul>
                                    </div>
                                    @endcan

                                </div>
                            </div>
                            <div id="trip-list-view">
                                @include('tripmanagement::admin.trip.partials._trip-list')
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
    <script>
        function loadPartialView(url, divId, data) {
            $.get({
                url: url,
                dataType: 'json',
                data: {data},
                beforeSend: function () {
                    $('#resource-loader').show();
                },
                success: function (response) {
                    $(divId).empty().html(response)
                },
                complete: function () {
                    $('#resource-loader').hide();
                },
                error: function () {
                    $('#resource-loader').hide();
                    toastr.error('{{translate('failed_to_load_data')}}')
                },
            });
        }
    </script>
    <script>
        let data_range = $('#date-range');
        let data_input = $('#data-input');

        data_range.on('change', function (){
            if (data_range.val() === 'custom_date') {
                data_input.css('display', 'flex')
            } else {
                data_input.css('display', 'none')
                loadPartialView('{{url()->full()}}', '#trip-stats', data_range.val())
            }
        });
    </script>

    <script>
        function getDate() {
            let start = $('#start_date').val()
            let end = $('#end_date').val()
            if(!start || !end || start > end) {
                toastr.error('{{translate('please_select_proper_date_range')}}');
                return ;
            }
            let data = {start: start, end: end}
            loadPartialView('{{url()->full()}}', '#trip-stats', data)
        }
    </script>

@endpush
