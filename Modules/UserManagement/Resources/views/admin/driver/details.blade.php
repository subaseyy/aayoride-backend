@extends('adminmodule::layouts.master')

@section('title', translate('Driver_Details'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-3">{{ translate('driver') }} # {{ $commonData['driver']->id }}</h2>

            <div class="card mb-30">
                <div class="card-body">
                    <div class="row gy-5">
                        <div class="col-lg-6">
                            <div class="">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-4">
                                    <h5 class="text-capitalize d-flex align-items-center gap-2 text-primary">
                                        <i class="bi bi-person-fill-gear"></i>
                                        {{ translate('driver_information') }}
                                    </h5>
                                </div>

                                <div class="media flex-wrap gap-3 gap-lg-4">
                                    <div class="avatar avatar-135 rounded">
                                        <img src="{{ onErrorImage(
                                            $commonData['driver']?->profile_image,
                                            asset('storage/app/public/driver/profile') . '/' . $commonData['driver']?->profile_image,
                                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                                            'driver/profile/',
                                        ) }}"
                                            class="rounded dark-support custom-box-size" alt=""
                                            style="--size: 136px">
                                    </div>
                                    <div class="media-body">
                                        <div class="d-flex flex-column align-items-start gap-1">
                                            <h6 class="mb-10">
                                                {{ $commonData['driver']?->first_name . ' ' . $commonData['driver']?->last_name }}
                                            </h6>
                                            <div class="d-flex gap-3 align-items-center mb-1">
                                                <div class="badge bg-primary text-capitalize">
                                                    {{ $commonData['driver']->level->name ?? translate('no_level_found') }}
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    {{ number_format($commonData['driver']->receivedReviews->avg('rating'), 1) }}
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                </div>
                                            </div>
                                            <a
                                                href="tel:{{ $commonData['driver']->phone }}">{{ $commonData['driver']->phone }}</a>
                                            <a
                                                href="mailto:{{ $commonData['driver']->email }}">{{ $commonData['driver']->email }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-4">
                                    <h5 class="d-flex align-items-center text-primary gap-2">
                                        <i class="bi bi-person-fill-gear text-primary text-capitalize"></i>
                                        {{ translate('driver_rate_info') }}
                                    </h5>
                                </div>

                                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center">
                                    <div class="text-success text-capitalize">
                                        {{ translate('average_active_rate/day') }}</div>
                                    <div class="d-flex gap-2 align-items-center flex-grow-1">
                                        <div class="progress flex-grow-1">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ round($commonData['avg_active_day']) }}%"
                                                aria-valuenow="{{ round($commonData['avg_active_day'], 2) }}"
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-success">{{ round($commonData['avg_active_day'], 2) }}%</div>
                                    </div>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-around flex-wrap gap-3">
                                            <div class="d-flex align-items-center flex-column gap-3">
                                                <div class="circle-progress"
                                                    data-parsent="{{ round($commonData['driver_avg_earning'], 2) }}"
                                                    data-color="#56DBCB">
                                                    <div class="content">
                                                        <h6 class="persent fs-12">
                                                            {{ abbreviateNumber($commonData['driver_avg_earning']) }}{{ getSession('currency_symbol') }}
                                                        </h6>
                                                    </div>
                                                </div>
                                                <h6 class="fw-semibold fs-12" style="color: #56DBCB">
                                                    {{ translate('avg._earning_value') }}</h6>
                                            </div>

                                            <div class="d-flex align-items-center flex-column gap-3">
                                                <div class="circle-progress"
                                                    data-parsent="{{ round($commonData['positive_review_rate']) ?? 0 }}"
                                                    data-color="#3B72FF">
                                                    <div class="content">
                                                        <h6 class="persent fs-12">
                                                            {{ round($commonData['positive_review_rate']) ?? 0 }}
                                                            %</h6>
                                                    </div>
                                                </div>
                                                <h6 class="fw-semibold fs-12 text-capitalize positive-review-color">
                                                    {{ translate('positive_review_rate') }}</h6>
                                            </div>

                                            <div class="d-flex align-items-center flex-column gap-3">
                                                <div class="circle-progress text-capitalize"
                                                    data-parsent="{{ round($commonData['success_rate'], 2) }}"
                                                    data-color="#76C351">
                                                    <div class="content">
                                                        <h6 class="persent fs-12">{{ round($commonData['success_rate'], 2) }}
                                                            %</h6>
                                                    </div>
                                                </div>
                                                <h6 class="fw-semibold fs-12 text-capitalize success-rate-color">
                                                    {{ translate('success_rate') }}</h6>
                                            </div>

                                            <div class="d-flex align-items-center flex-column gap-3">
                                                <div class="circle-progress"
                                                    data-parsent="{{ round($commonData['cancel_rate'], 2) }}"
                                                    data-color="#FF6767">
                                                    <div class="content">
                                                        <h6 class="persent fs-12">{{ round($commonData['cancel_rate'], 2) }}
                                                            %</h6>
                                                    </div>
                                                </div>
                                                <h6 class="fw-semibold fs-12 text-capitalize cancellation-rate-color">
                                                    {{ translate('cancelation_rate') }}</h6>
                                            </div>
                                            <div class="d-flex align-items-center flex-column gap-3">
                                                <div class="circle-progress"
                                                    data-parsent="{{ round($commonData['idle_rate_today'], 2) }}"
                                                    data-color="#FFA800">
                                                    <div class="content">
                                                        <h6 class="persent fs-12">{{ round($commonData['idle_rate_today'], 2) }}
                                                            %</h6>
                                                    </div>
                                                </div>
                                                <h6 class="fw-semibold fs-12" style="color: #FFA800">Today Idle Hour
                                                    Rate</h6>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-30">
                <div class="card-body">
                    <div class="row justify-content-between align-items-center g-2 mb-3">
                        <div class="col-sm-6">
                            <h5 class="text-capitalize d-flex align-items-center gap-2 text-primary">
                                <i class="bi bi-person-fill-gear"></i>
                                {{ translate('wallet_info') }}
                            </h5>
                        </div>
                    </div>
                    <div class="row g-4" id="order_stats">
                        <div class="col-lg-4">

                            <div class="card h-100 d-flex justify-content-center align-items-center">
                                <div class="card-body d-flex flex-column gap-10 align-items-center justify-content-center">
                                    <img width="48" src="{{ asset('public/assets/admin-module/img/media/cc.png') }}"
                                        alt="">
                                    <h3 class="fw-bold mb-0 fs-3">
                                        {{ getCurrencyFormat($commonData['collectable_amount']) }}</h3>
                                    <div class="fw-bold text-capitalize mb-30">
                                        {{ translate('collectable_cash') }}
                                    </div>
                                </div>
                                @if($commonData['collectable_amount']>0)
                                    <a href="{{ route('admin.driver.cash.index', [$commonData['driver']->id]) }}"
                                       class="text-capitalize btn btn-primary mb-4">{{ translate('collect_cash') }}</a>
                                @endif
                            </div>

                        </div>
                        <div class="col-lg-8">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card card-body h-100 justify-content-center py-5">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="fw-bold mb-1 fs-3">
                                                    {{ getCurrencyFormat($commonData['pending_withdraw']) }}</h3>
                                                <div class="text-capitalize mb-0 text-capitalize fw-bold">
                                                    {{ translate('pending_withdraw') }}</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2"
                                                    src="{{ asset('public/assets/admin-module/img/media/pw.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-body h-100 justify-content-center py-5">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="fw-bold mb-1 fs-3">
                                                    {{ getCurrencyFormat($commonData['already_withdrawn']) }}</h3>
                                                <div class="fw-bold text-capitalize mb-0">
                                                    {{ translate('already_withdrawn') }}</div>
                                            </div>
                                            <div>
                                                <img width="40"
                                                    src="{{ asset('public/assets/admin-module/img/media/aw.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card card-body h-100 justify-content-center py-5">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fs-3 fw-bold">
                                                    {{ getCurrencyFormat($commonData['withdrawable_amount']) }}
                                                </h3>
                                                <div class="fw-bold text-capitalize mb-0">
                                                    {{ translate('withdrawable_amount') }}</div>
                                            </div>
                                            <div>
                                                <img width="40" class="mb-2"
                                                    src="{{ asset('public/assets/admin-module/img/media/withdraw.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="card card-body h-100 justify-content-center py-5">
                                        <div class="d-flex gap-2 justify-content-between align-items-center">
                                            <div class="d-flex flex-column align-items-start">
                                                <h3 class="mb-1 fs-3 fw-bold">
                                                    {{ getCurrencyFormat($commonData['total_earning'] + $commonData['already_withdrawn']) }}
                                                </h3>
                                                <div class="text-capitalize mb-0 fw-bold">
                                                    {{ translate('total_earning') }}</div>
                                            </div>
                                            <div>
                                                <img width="40"
                                                    src="{{ asset('public/assets/admin-module/img/media/withdraw-icon.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="d-flex mb-4">
                <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'overview']) }}"
                            class="nav-link {{ $commonData['tab'] == 'overview' ? 'active' : '' }}">{{ translate('overview') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $commonData['tab'] == 'vehicle' ? 'active' : '' }}"
                            href="{{ route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'vehicle']) }}"
                            tabindex="-1">{{ translate('vehicle') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'trips']) }}"
                            class="nav-link {{ $commonData['tab'] == 'trips' ? 'active' : '' }}"
                            tabindex="-1">{{ translate('trips') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'transaction']) }}"
                            class="nav-link {{ $commonData['tab'] == 'transaction' ? 'active' : '' }}" role="tab"
                            tabindex="-1">Transaction</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'review', 'reviewed_by' => 'customer']) }}"
                            class="nav-link {{ $commonData['tab'] == 'review' ? 'active' : '' }}"
                            tabindex="-1">{{ translate('review') }}</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                @if ($commonData['tab'] == 'overview')
                    @include('usermanagement::admin.driver.partials.overview', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'vehicle')
                    @include('usermanagement::admin.driver.partials.vehicle', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'trips')
                    @include('usermanagement::admin.driver.partials.trips', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'transaction')
                    @include('usermanagement::admin.driver.partials.transaction', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'review')
                    @include('usermanagement::admin.driver.partials.review', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
            </div>
        </div>
    </div>
    <!-- End Main Content -->

@endsection

@push('script')
    <!-- Apex Chart -->
    <script src="{{ asset('public/assets/admin-module/plugins/apex/apexcharts.min.js') }}"></script>
@endpush
