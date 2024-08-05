@section('title', translate('coupon_List'))

@extends('adminmodule::layouts.master')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">


            <div class="row g-4 mt-1">
                <div class="col-12">

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <h2 class="ffs-22 mt-4 text-capitalize">{{ translate('deleted_coupons') }}</h2>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_coupons') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $coupons->total() }}</span>
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
                                            placeholder="{{ translate('search_here_by_Cupon_Title') }}">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                                </form>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle table-hover text-nowrap text-center">
                                    <thead class="table-light align-middle text-capitalize">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th class="coupon_title">{{ translate('coupon_title') }}</th>
                                            <th class="coupon_type">{{ translate('coupon_type') }}</th>
                                            <th class="coupon_amount">{{ translate('coupon_amount') }}</th>
                                            <th class="duration">{{ translate('duration') }}</th>
                                            <th class="total_times_used">{{ translate('total_times_used') }}</th>
                                            <th class="total_coupon_amount">{{ translate('total_coupon') }}
                                                <br> {{ translate('amount') }}
                                                ({{ session()->get('currency_symbol') ?? '$' }})
                                            </th>
                                            <th class="average_coupon">{{ translate('average_coupon') }}</th>
                                            <th class="coupon_status">{{ translate('coupon_status') }}</th>

                                            <th class="text-center action">{{ translate('action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($coupons as $key =>$coupon)
                                            <tr id="hide-row-{{ $coupon->id }}">
                                                <td>{{ $coupons->firstItem() + $key }}</td>
                                                <td class="coupon_title">{{ $coupon->name }}</td>
                                                <td class="coupon_type text-capitalize">
                                                    {{ str_replace('_', ' ', $coupon->coupon_type) }}</td>
                                                <td class="coupon_amount">
                                                    {{ $coupon->amount_type == 'percentage' ? $coupon->coupon . '%' : '$' . $coupon->coupon }}
                                                </td>
                                                <td class="duration" class="text-capitalize vehicle-features">
                                                    Start : {{ $coupon->start_date }} <br>
                                                    End : {{ $coupon->end_date }} <br>
                                                    Duration
                                                    :
                                                    {{ Carbon\Carbon::parse($coupon->end_date)->diffInDays($coupon->start_date) }}
                                                    Days
                                                </td>
                                                <td class="total_times_used">{{ (int)$coupon->total_used }}</td>
                                                <td class="total_coupon_amount">{{ getCurrencyFormat($coupon->total_amount) }}</td>
                                                <td class="average_coupon">
                                                    {{ getCurrencyFormat($coupon->total_used > 0 ? $coupon->total_amount / $coupon->total_used : 0) }}

                                                </td>
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
                                                            class="badge badge-sucess">{{ translate(RUNNING) }}</span>
                                                    @endif
                                                </td>

                                                <td class="action">
                                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                                        <button
                                                            data-route="{{ route('admin.promotion.coupon-setup.restore', $coupon->id) }}"
                                                            data-message="{{ translate('Want_to_recover_this_coupon?_') . translate('if_yes,_this_coupon_will_be_available_again_in_the_Coupon_List') }}"
                                                            class="btn btn-outline-primary btn-action restore-data">
                                                            <i class="bi bi-arrow-repeat"></i>
                                                        </button>

{{--                                                        <button data-id="delete-{{ $coupon->id }}"--}}
{{--                                                            data-message="{{ translate('want_to_permanent_delete_this_coupon?') }} {{ translate('you_cannot_revert_this_action') }}"--}}
{{--                                                            type="button"--}}
{{--                                                            class="btn btn-outline-danger btn-action form-alert">--}}
{{--                                                            <i class="bi bi-trash-fill"></i>--}}
{{--                                                        </button>--}}

{{--                                                        <form--}}
{{--                                                            action="{{ route('admin.promotion.coupon-setup.permanent-delete', ['id' => $coupon->id]) }}"--}}
{{--                                                            id="delete-{{ $coupon->id }}" method="post">--}}
{{--                                                            @csrf--}}
{{--                                                            @method('delete')--}}
{{--                                                        </form>--}}
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
                                {!! $coupons->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
