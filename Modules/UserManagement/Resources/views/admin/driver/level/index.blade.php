@extends('adminmodule::layouts.master')

@section('title', translate('Driver_Levels'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('user_view')
                <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-3 mb-sm-2">
                    <h2 class="fs-22">{{ translate('Driver_Levels') }}</h2>
                    <div>
                        <select class="js-select driver-level-statistics">
                            <option disabled>{{ translate('select_duration') }} </option>
                            <option value="all_time" selected>{{ translate('all_time') }} </option>
                            <option value="today">{{ translate('today') }} </option>
                            <option value="this_week">{{ translate('this_week') }} </option>
                            <option value="this_month">{{ translate('this_month') }} </option>
                            <option value="this_year">{{ translate('This_Year') }} </option>
                        </select>
                    </div>
                </div>
                <div id="statistics">

                </div>
            @endcan
            <div class="row g-4">
                <div class="col-12">
                    <h2 class="fs-22 mt-4 text-capitalize">{{ translate('level_list') }}</h2>
                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ url()->current() }}?status=all"
                                   class="nav-link
                                {{ !request()->has('status') || request()->get('status') === 'all' ? 'active' : '' }}
                                ">{{ translate('all') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ url()->current() }}?status=active"
                                   class="nav-link
                                    {{ request()->get('status') == 'active' ? 'active' : '' }}
                                ">{{ translate('active') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ url()->current() }}?status=inactive"
                                   class="nav-link {{ request()->get('status') == 'inactive' ? 'active' : '' }}">{{ translate('inactive') }}</a>
                            </li>
                        </ul>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_levels') }} : </span>
                            <span class="text-primary fs-16 fw-bold"
                                  id="total_record_count">{{ $levels->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="javascript:;" method="GET"
                                              class="search-form search-form_style-two">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" name="search" id="search"
                                                       value="{{ request()->get('search') }}"
                                                       class="theme-input-style search-form__input"
                                                       placeholder="{{ translate('search_here_by_Level_name') }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                    data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            @can('super-admin')
                                                <a href="{{ route('admin.driver.level.index',['status'=>request('status')]) }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('refresh') }}">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>

                                                <a href="{{ route('admin.driver.level.trash') }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                                    <i class="bi bi-recycle"></i>
                                                </a>
                                            @endcan
                                            @can('user_log')
                                                <a href="{{ route('admin.driver.level.log') }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('view_Log') }}">
                                                    <i class="bi bi-clock-fill"></i>
                                                </a>
                                            @endcan
                                            @can('user_export')
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-outline-primary"
                                                            data-bs-toggle="dropdown">
                                                        <i class="bi bi-download"></i>
                                                        {{ translate('download') }}
                                                        <i class="bi bi-caret-down-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li><a class="dropdown-item"
                                                               href="{{ route('admin.driver.level.export') }}?status={{ request()->get('status') ?? 'all' }}&&search={{ request()->get('search') }}&&file=excel">{{ translate('excel') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endcan
                                            @can('user_add')
                                                <a href="{{ route('admin.driver.level.create') }}" type="button"
                                                   class="btn btn-primary text-capitalize">
                                                    <i class="bi bi-plus fs-16"></i> {{ translate('add_level') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-3" id="printableTable">
                                        <table class="table table-borderless align-middle table-hover text-nowrap"
                                               id="printTable">
                                            <thead class="table-light align-middle text-capitalize">
                                            <tr>
                                                <th class="sl">{{ translate('SL') }}</th>
                                                <th class="level_name">{{ translate('level_name') }}</th>
                                                <th class="level_name -center">
                                                    {{ translate('Target to proceed level') }}
                                                    <div class="text-muted fs-12">({{ translate('For next level') }})
                                                    </div>
                                                </th>
                                                <th class="total_ride_complete text-center">
                                                    {{ translate('total_trip') }}
                                                </th>

                                                <th class="maximum_cancellation_rate text-center">
                                                    {{ translate('maximum') }}
                                                    <br>{{ translate('cancellation_rate') }}
                                                </th>
                                                <th class="total_driver text-center">
                                                    {{ translate('total_driver') }}</th>
                                                @can('user_edit')
                                                    <th class="status">{{ translate('status') }}</th>
                                                @endcan
                                                <th class="action text-center">{{ translate('action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($levels as $key => $level)
                                                    <?php
                                                    $totalTrip = 0;
                                                    $completedTrip = 0;
                                                    $cancelledTrip = 0;
                                                    $trip_earning = 0;
                                                    ?>

                                                @forelse($level->users as $user)
                                                    @php($totalTrip += $user->driverTrips->count())
                                                    @php($completedTrip += $user->driverTrips?->where('current_status', 'completed')->count())
                                                    @php($cancelledTrip += $user->driverTrips?->where('current_status', 'cancelled')->count())
                                                @empty
                                                @endforelse
                                                <tr id="hide-row-{{ $level->id }}" class="record-row">
                                                    <td class="sl">{{ $levels->firstItem() + $key }}</td>
                                                    <td class="level_name">
                                                        <div
                                                            class="media gap-2 gap-xl-3 align-items-center max-content">
                                                            <img src="{{ onErrorImage(
                                                                    $level?->image,
                                                                    asset('storage/app/public/driver/level') . '/' . $level?->image,
                                                                    asset('public/assets/admin-module/img/media/level5.png'),
                                                                    'driver/level/',
                                                                ) }}"
                                                                 class="dark-support custom-box-size" alt=""
                                                                 style="--size: 45px">
                                                            <div class="media-body">{{ $level->name }}</div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-1">
                                                            <div class="d-flex gap-4 align-items-center">
                                                                <span
                                                                    class="min-w100">{{translate('Ride Complete')}}</span>:
                                                                <span>{{ $level->targeted_ride }} ({{$level->targeted_ride_point}} {{translate('points')}})</span>
                                                            </div>
                                                            <div class="d-flex gap-4 align-items-center">
                                                                <span
                                                                    class="min-w100">{{translate('Earning Amount')}}</span>:
                                                                <span>{{ set_currency_symbol($level->targeted_amount ?? 0) }} ({{$level->targeted_amount_point}} {{translate('points')}})</span>
                                                            </div>
                                                            <div class="d-flex gap-4 align-items-center">
                                                                <span
                                                                    class="min-w100">{{translate('Cancellation Rate')}}</span>:
                                                                <span>{{ $level->targeted_cancel }}% ({{$level->targeted_cancel_point}} {{translate('points')}})</span>
                                                            </div>
                                                            <div class="d-flex gap-4 align-items-center">
                                                                <span class="min-w100">Given Review</span>:
                                                                <span>{{ $level->targeted_review }} ({{$level->targeted_review_point}} {{ translate('points') }})</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="total_ride_complete text-center">
                                                        {{ $completedTrip }}
                                                    </td>
                                                    <td class="maximum_cancellation_rate text-center">
                                                        {{ number_format($totalTrip == 0 ? 0 : ($cancelledTrip / $totalTrip) * 100, 2) }}
                                                        %
                                                    </td>
                                                    <td class="total_driver text-center">{{ $level->users->count() }}
                                                    </td>
                                                    @can('user_edit')
                                                        <td class="status">
                                                            <label class="switcher">
                                                                <input class="switcher_input status-change-level"
                                                                       type="checkbox"
                                                                       {{ $level->is_active == 1 ? 'checked' : '' }}
                                                                       data-url="{{ route('admin.driver.level.update-status') }}"
                                                                       data-icon="{{$level->is_active == 0 ? asset('public/assets/admin-module/img/level-up-on.png') : asset('public/assets/admin-module/img/level-up-off.png')}}"
                                                                       data-title="{{$level->is_active == 0?translate('Turn on ').$level->name .'?' : translate('Turn off ').$level->name}}.'?'"
                                                                       data-sub-title="{{$level->is_active == 0?translate('If you activate this level, drivers will be able to see this level in the app. Make sure setup its priority and other data in order to function properly.') : translate('If you deactivate this level, all driver under this level will not be able to see the level details in their profile.')}}"
                                                                       id="{{ $level->id }}" {{$level->users_count > 0 ? "disabled" : ($level->sequence == 1 ? "disabled" : "")}}>
                                                                @if($level->users_count > 0 || $level->sequence == 1)
                                                                    <span class="switcher_control" data-bs-toggle="tooltip" data-bs-title="{{translate("If there is any existing user in this level, you can not change the status")}}"></span>
                                                                @else
                                                                    <span class="switcher_control" ></span>
                                                                @endif
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    <td class="action">
                                                        <div
                                                            class="d-flex justify-content-center gap-2 align-items-center">
                                                            @can('user_log')
                                                                <a href="{{ route('admin.driver.level.log') }}?id={{ $level->id }}"
                                                                   class="btn btn-outline-primary btn-action">
                                                                    <i class="bi bi-clock-fill"></i>
                                                                </a>
                                                            @endcan
                                                            @can('user_edit')
                                                                <a href="{{ route('admin.driver.level.edit', ['id' => $level->id]) }}"
                                                                   class="btn btn-outline-info btn-action">
                                                                    <i class="bi bi-pencil-fill"></i>
                                                                </a>
                                                            @endcan
                                                            @can('user_delete')
                                                                @if ($level->users->count() < 1)
                                                                    @if($level->sequence != 1)
                                                                            <button data-id="delete-{{ $level->id }}"
                                                                                    data-message="{{ translate('want_to_delete_this_level?') }}"
                                                                                    type="button"
                                                                                    class="btn btn-outline-danger btn-action form-alert">
                                                                                <i class="bi bi-trash-fill"></i>
                                                                            </button>
                                                                            <form
                                                                                action="{{ route('admin.driver.level.delete', ['id' => $level->id]) }}"
                                                                                method="post" id="delete-{{ $level->id }}"
                                                                                class="d-none">
                                                                                @csrf
                                                                                @method('delete')
                                                                            </form>
                                                                    @endif
                                                                @endif
                                                            @endcan
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

                                        <div
                                            class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                            {!! $levels->links() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
    <input type="hidden" name="url" id="url" value="{{businessConfig(DRIVER_LEVEL)->value??0}}">
@endsection

@push('script')
    <script>
        if ($("#url").val() == 0) {
            let bootstrapModal = new bootstrap.Modal(document.getElementById("driverLevelSettingWarningModal"));
            let url = "{{route("admin.business.setup.driver.index")}}"
                $("#driverLevelSettingWarningModalConfirmBtn").attr('href',url)
            bootstrapModal.show();
        }

        "use strict";
        $(".driver-level-statistics").on('change', function () {
            let data = $(this).val();
            loadPartialView('{{ route('admin.driver.level.statistics') }}?date_range=' + data, '#statistics')
        })
        loadPartialView('{{ route('admin.driver.level.statistics') }}', '#statistics')
    </script>
@endpush
