@extends('adminmodule::layouts.master')

@section('title', translate('Driver_Levels'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <h2 class="fs-22 mt-4 text-capitalize mb-3">{{ translate('deleted_level_list') }}</h2>
                    <div class="d-flex flex-wrap justify-content-end align-items-center my-3 gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_levels') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $levels->total() }}</span>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
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
                                                    <tr id="hide-row-{{ $level->id }}">
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
                                                        <td class="action">
                                                            <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                                <button
                                                                    data-route="{{ route('admin.driver.level.restore', ['id' => $level->id]) }}"
                                                                    data-message="{{ translate('Want_to_recover_this_driver_level?_') . translate('if_yes,_this_driver_level_will_be_available_again_in_the_Driver_Level_List') }}"
                                                                    class="btn btn-outline-primary btn-action restore-data">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </button>
                                                                <button data-id="delete-{{ $level->id }}"
                                                                        data-message="{{ translate('want_to_permanent_delete_this_level?') }} {{ translate('you_cannot_revert_this_action') }}"
                                                                        type="button"
                                                                        class="btn btn-outline-danger btn-action form-alert">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>

                                                                <form
                                                                    action="{{ route('admin.driver.level.permanent-delete', ['id' => $level->id]) }}"
                                                                    id="delete-{{ $level->id }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
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

@endsection

@push('script')
    <script>
        "use strict";
        loadPartialView('{{ route('admin.driver.level.statistics') }}', '#statistics')
    </script>
@endpush
