@section('title', translate('trashed_discount_List'))

@extends('adminmodule::layouts.master')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">


            <div class="row g-4 mt-1">
                <div class="col-12">

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <h2 class="ffs-22 mt-4 text-capitalize">{{ translate('trashed_discounts') }}</h2>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_trashed_discounts') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $discounts->total() }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="{{ url()->full() }}" class="search-form search-form_style-two" method="GET">
                                    <div class="input-group search-form__input_group">
                                        <span class="search-form__icon">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="search" class="theme-input-style search-form__input"
                                            value="{{ request()->get('search') }}" name="search" id="search"
                                            placeholder="{{ translate('search_here_by_title') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                                </form>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle table-hover text-nowrap text-center">
                                    <thead class="table-light align-middle text-capitalize">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th class="discount_image">{{ translate('image') }}</th>
                                            <th class="discount_title">{{ translate('discount_title') }}</th>
                                            <th class="zone">{{ translate('zone') }}</th>
                                            <th class="customer_level">{{ translate('customer_level') }}</th>
                                            <th class="customer">{{ translate('customer') }}</th>
                                            <th class="module">{{ translate('module') }}</th>
                                            <th class="discount_amount">{{ translate('discount_amount') }}</th>
                                            <th class="duration">{{ translate('duration') }}</th>
                                            <th class="total_times_used">{{ translate('total_times_used') }}</th>
                                            <th class="total_discount_amount">{{ translate('total_discount') }}
                                                <br> {{ translate('amount') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})
                                            </th>
                                            <th class="discount_status">{{ translate('discount_status') }}</th>

                                            <th class="text-center action">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($discounts as $key =>$discount)
                                            <tr id="hide-row-{{ $discount->id }}">
                                                <td>{{ $discounts->firstItem() + $key }}</td>
                                                <td class="discount_image">
                                                    <img src="{{ onErrorImage(
                                                $discount?->image,
                                                asset('storage/app/public/promotion/discount') . '/' . $discount?->image,
                                                asset('public/assets/admin-module/img/media/banner-upload-file.png'),
                                                'promotion/discount/',
                                            ) }}"
                                                         class="custom-box-size-banner rounded dark-support" alt="">
                                                </td>
                                                <td class="discount_title">{{ $discount->title }}</td>
                                                <td class="zone">
                                                    @if($discount->zone_discount_type == ALL)
                                                        <span class="badge bg-info rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                    @else
                                                        @foreach($discount->zones as $zone)
                                                            <span
                                                                class="badge bg-info rounded-pill badge-sm text-capitalize">{{ $zone->name }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="customer_level">
                                                    @if($discount->customer_level_discount_type == ALL)
                                                        <span class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                    @else
                                                        @foreach($discount->customerLevels as $level)
                                                            <span
                                                                class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ $level->name }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="customer">
                                                    @if($discount->customer_discount_type == ALL)
                                                        <span class="badge bg-success rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                    @else
                                                        @foreach($discount->customers as $customer)
                                                            <span
                                                                class="badge bg-success rounded-pill badge-sm text-capitalize">{{ $customer->first_name }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="module">
                                                    @if(in_array(ALL,$discount->module_discount_type))
                                                        <span class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                    @elseif(in_array(PARCEL,$discount->module_discount_type) && in_array(CUSTOM,$discount->module_discount_type))
                                                        <span
                                                            class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ PARCEL }}</span>
                                                        @foreach($discount->vehicleCategories as $category)
                                                            <span
                                                                class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ $category->name }}</span>
                                                        @endforeach
                                                    @elseif(in_array(PARCEL,$discount->module_discount_type))
                                                        <span
                                                            class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ PARCEL }}</span>
                                                    @elseif(in_array(CUSTOM,$discount->module_discount_type))
                                                        @foreach($discount->vehicleCategories as $category)
                                                            <span
                                                                class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ $category->name }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="discount_amount">{{ $discount->discount_amount_type == PERCENTAGE? $discount->discount_amount.'%': set_currency_symbol($discount->discount_amount) }}</td>
                                                <td class="duration" class="text-capitalize vehicle-features">
                                                    {{translate('start')}} : {{date('Y-m-d',strtotime($discount->start_date))}} <br>
                                                    {{translate('end')}} : {{date('Y-m-d',strtotime($discount->end_date))}} <br>
                                                    {{translate('duration')}}
                                                    : {{ Carbon\Carbon::parse($discount->end_date)->diffInDays($discount->start_date)}}
                                                    Days
                                                </td>
                                                <td class="total_times_used">{{ (int)$discount->total_used }}</td>
                                                <td class="total_discount_amount">{{ set_currency_symbol(round($discount->total_amount,2)) }}</td>
                                                <td class="discount_status">
                                                    @php($date = Carbon\Carbon::now()->startOfDay())
                                                    @if($date->gt($coupon->end_date))
                                                        <span
                                                            class="badge badge-danger">{{ translate(EXPIRED) }}</span>
                                                    @elseif (!$coupon->is_active)
                                                        <span
                                                            class="badge badge-warning">{{ translate(CURRENTLY_OFF) }}</span>
                                                    @elseif ($date->lt($discount->start_date))
                                                        <span
                                                            class="badge badge-info">{{ translate(UPCOMING) }}</span>
                                                    @elseif ($date->lte($discount->end_date))
                                                        <span
                                                            class="badge badge-success">{{ translate(RUNNING) }}</span>
                                                    @endif
                                                </td>

                                                <td class="action">
                                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                                        <button
                                                            data-route="{{ route('admin.promotion.discount-setup.restore', $discount->id) }}"
                                                            data-message="{{ translate('Want_to_recover_this_discount?_') . translate('if_yes,_this_discount_will_be_available_again_in_the_discount_List') }}"
                                                            class="btn btn-outline-primary btn-action restore-data">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10">
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
                                {!! $discounts->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
