@section('title', translate('edit_Coupon'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <form action="{{ route('admin.promotion.coupon-setup.update', ['id'=>$coupon->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('edit_coupon') }}</h5>

                                <div class="mb-4">
                                    <label for="coupon_title" class="mb-2">{{ translate('coupon_title') }}</label>
                                    <input type="text" id="coupon_title" name="coupon_title" value="{{ $coupon->name }}"
                                           class="form-control" placeholder="Ex: 20% Coupon">
                                </div>

                                <div class="mb-4">
                                    <label for="short_des" class="mb-2">{{ translate('short_desciption') }}</label>
                                    <textarea id="short_desc" name="short_desc" cols="30" rows="5" class="form-control"
                                              placeholder="Type Here...">{{ $coupon->description }}</textarea>
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_type" class="mb-2">
                                                {{ translate('coupon_type') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('you_will_be_not_able_to_change_coupon_type_in_future') }}"></i>
                                            </label>
                                            <select class="js-select" id="coupon_type" name="coupon_type">
                                                <option
                                                    value="default" {{ $coupon->coupon_type == 'default'?'selected':'' }}>{{ translate('default') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="limit_same_user"
                                                   class="mb-2">{{ translate('limit_for_the_same_user') }}</label>
                                            <input type="number" id="limit_same_user" name="limit_same_user"
                                                   value="{{ $coupon->limit }}" class="form-control"
                                                   placeholder="Ex: 10">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_code"
                                                   class="mb-2">{{ translate('coupon_code') }}</label>
                                            <input type="coupon_code" id="coupon_code" name="coupon_code"
                                                   value="{{ $coupon->coupon_code }}" class="form-control"
                                                   placeholder="Ex: 10" disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="user_type" class="mb-2">
                                                {{ translate('customer') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('coupon_customer_note') }}"></i>
                                            </label>
                                            <select class="js-select" id="customer" name="user_id" disabled>

                                                @if (!($coupon->user_id == 'all'))
                                                    <option
                                                        value="{{ $coupon->customer?->id }}">{{ $coupon->customer?->first_name }} {{ $coupon->customer?->last_name }}</option>
                                                @else
                                                    <option value="all"
                                                            class="text-capitalize">{{ translate('all_customer') }}</option>
                                                @endif

                                            </select>
                                        </div>
                                    </div>
                                    <div
                                        class="col-sm-6 col-xl-4 user_level {{$coupon->user_id != 'all' ? 'd-none' : ''}}">
                                        <div class="mb-4">
                                            <label for="customer_level" class="mb-2">
                                                {{ translate('customer_level') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('you_will_be_not_able_to_change_customer_level_in_future') }}"></i>
                                            </label>
                                            <select class="js-select" id="user_level_id" name="user_level_id" disabled>
                                                @if (!($coupon->user_level_id == 'all'))
                                                    <option
                                                        value="{{ $coupon?->level?->id }}">{{ $coupon?->level?->name }}</option>
                                                @else
                                                    <option value="0"
                                                            class="text-capitalize">{{ translate('all_level') }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="amount_type"
                                                   class="mb-2">{{ translate('coupon_amount_type') }}</label>
                                            <select class="js-select" id="amount_type" name="amount_type">
                                                <option
                                                    value="amount" {{ $coupon->amount_type == 'amount'?'selected':'' }}>{{ translate('fixed_amount') }}</option>
                                                <option
                                                    value="percentage" {{ $coupon->amount_type == 'percentage'?'selected':'' }}>{{ translate('percentage') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_amount" id="coupon_amount_label"
                                                   class="mb-2">{{ translate('coupon_amount') }}</label>
                                            <input type="number" id="coupon" step="any" name="coupon"
                                                   value="{{ $coupon->coupon }}" class="form-control"
                                                   placeholder="Ex: 500">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4 min_trip_amount">
                                        <div class="mb-4">
                                            <label for="minimum_trip_amount"
                                                   class="mb-2">{{ translate('minimum_trip_amount') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})</label>
                                            <input type="number" id="minimum_trip_amount" step="any"
                                                   name="minimum_trip_amount" value="{{ $coupon->min_trip_amount }}"
                                                   class="form-control"
                                                   placeholder="Ex: 100">
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="max_coupon" class="mb-2">{{ translate('maximum_coupon') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})</label>
                                            <input type="number" id="max_coupon" step=".01" name="max_coupon_amount"
                                                   value="{{ $coupon->max_coupon_amount }}" class="form-control"
                                                   placeholder="Ex: 60">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="start_date"
                                                   class="mb-2">{{ translate('start_date') }}</label>
                                            <input type="date" id="start_date" name="start_date" min="{{date('Y-m-d',strtotime(now()))}}"
                                                   value="{{ $coupon->start_date }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="start_date"
                                                   class="mb-2">{{ translate('end_date') }}</label>
                                            <input type="date" id="end_date" name="end_date" min="{{date('Y-m-d',strtotime(now()))}}"
                                                   value="{{ $coupon->end_date }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_rules" class="mb-2">
                                                {{ translate('coupon_rules') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('you_will_be_not_able_to_change_coupon_rules_in_future') }}"></i>
                                            </label>
                                            <select class="js-select" id="coupon_rules" name="coupon_rules" disabled>
                                                <option
                                                    value="default" {{ $coupon->rules == 'default'?'selected':'' }}>{{ translate('default') }}</option>
                                                <option
                                                    value="area_wise" {{ $coupon->rules == 'area_wise'?'selected':'' }}>{{ translate('area_wise') }}</option>
                                                <option
                                                    value="vehicle_category_wise" {{ $coupon->rules == 'vehicle_category_wise'?'selected': '' }}>{{ translate('vehicle_category_wise') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    @if (!($coupon->rules == 'default'))
                                        <div class="col-sm-6 col-xl-4 vehicle_category d-none">
                                            <div class="mb-4 text-capitalize">
                                                <label for="vehicle_category"
                                                       class="mb-2">{{ translate('vehicle_category') }}</label>
                                                <select id="vehicle_category" class="js-select-ajax" name="categories[]"
                                                        multiple="multiple"
                                                        data-placeholder="{{ translate('select_vehicle_category') }}"
                                                        disabled>
                                                    @if ($coupon->categories->isNotEmpty())
                                                        @foreach ($coupon->categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                    selected='selected'>{{ $category->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-primary"
                                            type="submit">{{ translate('update') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module/js/promotion-management/coupon-setup/edit.js')}}"></script>

    <script>
        "use strict";
        $(document).ready(function () {
            let rules = '{{ $coupon->rules }}';
            let amount_type = '{{ $coupon->amount_type }}';

            if (rules == 'vehicle_category_wise') {
                $('.vehicle_category').removeClass('d-none');
            } else {
                $('.vehicle_category').addClass('d-none');
            }

            // Coupon Type once loaded

            if (amount_type == 'amount') {
                $('#max_coupon').attr("readonly", "true");
                $('#max_coupon').val(0);

                $("#coupon_amount_label").text("Coupon amount");
                $("#coupon").attr("placeholder", "Ex: 500");
            } else {
                $('#max_coupon').removeAttr("readonly");
                $("#coupon_amount_label").text("Coupon percent");
                $("#coupon").attr("placeholder", "Ex: 50%");
            }

            $('#coupon_rules').change(function () {
                let ruleValue = this.value;
                if (ruleValue == 'vehicle_category_wise') {
                    $('.vehicle_category').removeClass('d-none');
                } else {
                    $('.vehicle_category').addClass('d-none');
                }
            });

            $('#amount_type').on('change', function () {
                if ($('#amount_type').val() == 'amount') {
                    $('#max_coupon').attr("readonly", true);
                    $('#max_coupon').val(0);

                    $("#coupon_amount_label").text("{{translate('Coupon Amount')}}");
                    $("#coupon").attr("placeholder", "{{ translate('Ex: 500') }}");
                } else {
                    $('#max_coupon').removeAttr("readonly");

                    $("#coupon_amount_label").text("{{ translate('Coupon Percent') }}")
                    $("#coupon").attr("placeholder", "{{ translate('Ex: 50%') }}")
                }
            });
        });


        $('.js-select-ajax').select2({
            ajax: {
                url: '{{ route('admin.customer.get-all-ajax') }}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });

        $('#vehicle_category').select2({
            ajax: {
                url: '{{ route('admin.vehicle.attribute-setup.category.all-categories') }}',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });
    </script>

@endpush
