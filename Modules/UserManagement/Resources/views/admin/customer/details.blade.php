@extends('adminmodule::layouts.master')

@section('title', translate('Customer_Details'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-3">{{ translate('customer') }} # {{ $commonData['customer']->id }}</h2>

            <div class="row gy-4 mb-30">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-4">
                                <h5 class="d-flex align-items-center gap-2 text-primary text-capitalize">
                                    <i class="bi bi-person-fill-gear"></i>
                                    {{ translate('customer_info') }}
                                </h5>
                            </div>

                            <div class="media flex-wrap gap-3 gap-lg-4">
                                <div class="custom-box-size rounded-circle avatar-hover" style="--size: 136px">

                                    <img src="{{ onErrorImage(
                                        $commonData['customer']?->profile_image,
                                        asset('storage/app/public/customer/profile') . '/' . $commonData['customer']?->profile_image,
                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                        'customer/profile/',
                                    ) }}"
                                         class="rounded-circle dark-support fit-object" alt="">
                                    <h6 class="level text-center">{{ $commonData['customer']?->level->name }}</h6>
                                </div>
                                <div class="media-body">
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <h3 class="mb-1">
                                            {{ $commonData['customer']?->first_name . ' ' . $commonData['customer']?->last_name }}
                                        </h3>
                                        <a href="tel:+0902342734">{{ $commonData['customer']->phone }}</a>
                                        <a href="mailto:lee@gmail.com">{{ $commonData['customer']->email }}</a>
                                        @php($address = $commonData['customer']->addresses()->where('address_label', 'default')?->first() ?? '')
                                        <p>{{ $address->address ?? '' }}<br class="d-none d-lg-block"/>
                                            {{ $address->city ?? '' }}</p>
                                    </div>
                                </div>
                                '
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-4">
                                <h5 class="d-flex align-items-center text-primary gap-2">
                                    <i class="bi bi-person-fill-gear text-primary"></i>
                                    {{ translate('customer_rate_info') }}
                                </h5>
                            </div>

                            <div class="row gy-4 gy-lg-5 gx-xl-5">
                                <div class="col-sm-6">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="text-capitalize text-info">
                                            {{ translate('total_digital_payment') }}</div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="progress flex-grow-1">
                                                <div class="progress-bar bg-info" role="progressbar"
                                                     style="width: {{ $commonData['digitalPaymentPercentage'] }}%"
                                                     aria-valuenow="{{ number_format($commonData['digitalPaymentPercentage'], 2) }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-success">
                                                {{ number_format($commonData['digitalPaymentPercentage'], 2) }}
                                                %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="text-capitalize text-success">{{ translate('success_rate') }}
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="progress flex-grow-1">
                                                <div class="progress-bar bg-succestext-success" role="progressbar"
                                                     style="width: {{ $commonData['success_percentage'] }}%"
                                                     aria-valuenow="{{ number_format($commonData['success_percentage'], 2) }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-success">
                                                {{ number_format($commonData['success_percentage'], 2) }}
                                                %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="text-capitalize text-warning">
                                            {{ translate('total_review_given') }}</div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="progress flex-grow-1">
                                                <div class="progress-bar bg-warning" role="progressbar"
                                                     style="width: {{ ($commonData['customer_total_review_count'] / ($customer->customerTrips->count() == 0 ? 1 : $customer->customerTrips->count())) * 100 }}%"
                                                     aria-valuenow="{{ number_format(($commonData['customer_total_review_count'] / ($customer->customerTrips->count() == 0 ? 1 : $customer->customerTrips->count())) * 100, 2) }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-warning">
                                                {{ number_format(($commonData['customer_total_review_count'] / ($customer->customerTrips->count() == 0 ? 1 : $customer->customerTrips->count())) * 100, 2) }}
                                                %
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="text-capitalize text-danger">{{ translate('cancellation_rate') }}
                                        </div>
                                        <div class="d-flex gap-2 align-items-center flex-grow-1">
                                            <div class="progress flex-grow-1">
                                                <div class="progress-bar bg-danger" role="progressbar"
                                                     style="width: {{ $commonData['cancel_percentage'] }}%"
                                                     aria-valuenow="{{ number_format($commonData['cancel_percentage'], 2) }}"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <div class="text-danger">
                                                {{ number_format($commonData['cancel_percentage'], 2) }}
                                                %
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
                        <a href="{{ route('admin.customer.show', ['id' => $commonData['customer']->id, 'tab' => 'overview']) }}"
                           class="nav-link {{ $commonData['tab'] == 'overview' ? 'active' : '' }}">{{ translate('overview') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.customer.show', ['id' => $commonData['customer']->id, 'tab' => 'trips']) }}"
                           class="nav-link {{ $commonData['tab'] == 'trips' ? 'active' : '' }}"
                           tabindex="-1">{{ translate('trips') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.customer.show', ['id' => $commonData['customer']->id, 'tab' => 'transaction']) }}"
                           class="nav-link {{ $commonData['tab'] == 'transaction' ? 'active' : '' }}" role="tab"
                           tabindex="-1">{{ translate('transaction') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="{{ route('admin.customer.show', ['id' => $commonData['customer']->id, 'tab' => 'review', 'reviewed_by' => 'customer']) }}"
                           class="nav-link {{ $commonData['tab'] == 'review' ? 'active' : '' }}"
                           tabindex="-1">{{ translate('review') }}</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                @if ($commonData['tab'] == 'overview')
                    @include('usermanagement::admin.customer.partials.overview', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'vehicle')
                    @include('usermanagement::admin.customer.partials.vehicle', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'trips')
                    @include('usermanagement::admin.customer.partials.trips', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'transaction')
                    @include('usermanagement::admin.customer.partials.transaction', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
                @if ($commonData['tab'] == 'review')
                    @include('usermanagement::admin.customer.partials.review', [
                        'commonData' => $commonData,
                        'otherData' => $otherData,
                    ])
                @endif
            </div>
        </div>
    </div>
    <!-- End Main Content -->


    <!-- End wrapper -->

@endsection

@push('script')
@endpush
