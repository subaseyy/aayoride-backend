@section('title', translate('add_New_Coupon'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <form action="{{ route('admin.promotion.coupon-setup.store') }}" method="POST" id="coupon_form">
                @csrf

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('add_new_coupon') }}</h5>

                                <div class="mb-4">
                                    <label for="discount_title" class="mb-2">{{ translate('coupon_title') }}
                                        <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                           title="{{ translate('write_the_coupon_title_within_50_characters.') }}"></i>
                                    </label>
                                    <input type="text" id="coupon_title" value="{{old('coupon_title')}}"
                                           name="coupon_title" class="form-control" placeholder="Ex: 20% Coupon"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="short_desc" class="mb-2">{{ translate('short_description') }}
                                        <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                           title="{{ translate('write_the_short_description_title_within_255_characters') }}"></i>

                                    </label>
                                    <textarea id="short_desc" name="short_desc" cols="30" rows="5" class="form-control"
                                              placeholder="{{ translate('type_here') }}..."
                                              required>{{old('short_desc')}}</textarea>
                                </div>

                                <div class="row align-items-end">
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_type" class="mb-2">
                                                {{ translate('coupon_type') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('when_you_choose_a_coupon_type_and_submit_it_once.') . ' ' . translate('_you_can_not_change_it_in_future') }}"></i>
                                            </label>
                                            <select class="js-select" id="coupon_type" name="coupon_type" required>
                                                <option value="" selected disabled>
                                                    -- {{ translate('Select_Coupon_Type') }} --
                                                </option>
                                                <option value="default">{{ translate('default') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between">
                                                <label for="coupon_code" class="mb-2">{{ translate('coupon_code') }}
                                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                       title="{{ translate('type_the_coupon_code_using_either_the_"underscore"_') . "(_)" .  translate('_or_no_space_within_30_characters._') . "e.g., newyear23 or new_year_23" }}"></i>
                                                </label>
                                                <a href="javascript:void(0)" class="float-right text-primary fz-12"
                                                   id="generateCode">{{translate('generate_code')}}</a>
                                            </div>
                                            <input type="text" id="coupon_code" name="coupon_code" class="form-control"
                                                   placeholder="Ex: New Year 23" value="{{old('coupon_code')}}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="limit_same_user"
                                                   class="mb-2">{{ translate('limit_for_the_same_user') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('set_how_many_times_a_user_can_use_this_coupon') }}"></i>
                                            </label>
                                            <input type="number" id="limit_same_user" name="limit_same_user"
                                                   class="form-control"
                                                   placeholder="Ex: 10" value="{{old('limit_same_user')}}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="user_type" class="mb-2">
                                                {{ translate('customer') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer" title="{{ translate('you_can_choose_one_or_all_customers_who_will_be_eligible_for_this_coupon._') .
                                                        translate('you_can_not_change_customers_once_it_is_submitted.') }}"></i>
                                            </label>
                                            <select class="js-select-customer" id="customer" name="user_id" required>
                                                <option value="" selected disabled>
                                                    -- {{ translate('select_customer') }} --
                                                </option>
                                                <option value="all">{{ translate('all_customer') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4 user_level">
                                        <div class="mb-4">
                                            <label for="customer_level" class="mb-2">
                                                {{ translate('customer_level') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('you_can_not_change_the_customer_level_once_it_is_submitted') }}"></i>
                                            </label>
                                            <select class="js-select" id="user_level_id" name="user_level_id">
                                                <option value="" selected disabled>
                                                    -- {{ translate('select_Customer_Level') }} --
                                                </option>
                                                <option value="all"
                                                        class="text-capitalize">{{ translate('all_level') }}</option>
                                                @foreach ($levels as $level)
                                                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="amount_type"
                                                   class="mb-2">{{ translate('coupon_amount_type') }}</label>
                                            <select class="js-select" id="amount_type" name="amount_type" required>
                                                <option value="" selected disabled>
                                                    -- {{ translate('select_Coupon_Amount_Type') }} --
                                                </option>
                                                <option value="amount">{{ translate('fixed_amount') }}</option>
                                                <option value="percentage">{{ translate('percentage') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_amount" class="mb-2"
                                                   id="coupon_amount_label">{{ translate('coupon_amount') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})</label>
                                            <input type="number" id="coupon" value="{{old('coupon')}}"
                                                   name="coupon" class="form-control" placeholder="Ex: 5" step="any"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4 min_trip_amount">
                                        <div class="mb-4">
                                            <label for="minimum_trip_amount"
                                                   class="mb-2">{{ translate('minimum_trip_amount') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('set_the_minimum_trip_amount_that_is_required_to_use_this_coupon') }}"></i>
                                            </label>
                                            <input type="number" id="minimum_trip_amount" name="minimum_trip_amount"
                                                   class="form-control"
                                                   placeholder="Ex: 100" step="any"
                                                   value="{{old('minimum_trip_amount')}}" required>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="max_coupon"
                                                   class="mb-2">{{ translate('maximum_discount_limit') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('set_the_maximum_discount_limit_for_this_coupon,_and_the_discount_amount_will_not_increase_after_reaching_this_limit') }}"></i>
                                            </label>
                                            <input type="number" id="max_coupon" name="max_coupon_amount"
                                                   class="form-control"
                                                   placeholder="Ex: 60" value="{{old('max_coupon_amount')}}" step=".01">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="start_date"
                                                   class="mb-2">{{ translate('start_date') }}</label>
                                            <input type="date" value="{{old('start_date')}}" id="start_date"
                                                   min="{{date('Y-m-d',strtotime(now()))}}"
                                                   name="start_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="end_date" class="mb-2">{{ translate('end_date') }}</label>
                                            <input type="date" id="end_date" value="{{old('end_date')}}" name="end_date"
                                                   min="{{date('Y-m-d',strtotime(now()))}}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="coupon_rules" class="mb-2">
                                                {{ translate('coupon_rules') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('choose_whether_this_coupon_will_be_applied_to_all_vehicles_or_any_specific_vehicle_category._'). translate('if_“vehicle_category_wise”_is_selected,_“vehicle_category”_menu_will_appear._') . translate('once_submitted,_you_can_not_change_the_coupon_rule.') }}"></i>
                                            </label>
                                            <select class="js-select" id="coupon_rules" name="coupon_rules" required>
                                                <option selected disabled>-- {{ translate('select_Coupon_Rules') }}
                                                    --
                                                </option>
                                                <option value="default">{{ translate('default') }}</option>
                                                <option
                                                    value="vehicle_category_wise">{{ translate('vehicle_category_wise') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4 vehicle_category d-none">
                                        <div class="mb-4 text-capitalize">
                                            <label for="vehicle_category"
                                                   class="mb-2">{{ translate('vehicle_category') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{ translate('choose_in_which_vehicle_category_this_coupon_will_be_applicable.') }}"></i>
                                            </label>
                                            <select id="vehicle_category" class="js-select-ajax" name="categories[]"
                                                    multiple="multiple"
                                                    data-placeholder="-- {{ translate('select_vehicle_category') }} --">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-primary"
                                            type="submit">{{ translate('submit') }}</button>
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
    <script src="{{asset('public/assets/admin-module/js/promotion-management/coupon-setup/create.js')}}"></script>

    <script>
        "use strict";
        $('#coupon_form').submit(function (e) {
            if (!$('#amount_type').val()) {
                toastr.error('{{ translate('please_select_amount_type') }}');
                e.preventDefault();
            }
        });

        $(document).ready(function () {

            const amountType = $('#amount_type');
            const maxCoupon = $('#max_coupon');
            amountType.on('change', function () {
                if (amountType.val() == 'amount') {

                    maxCoupon.attr("readonly", "true");
                    maxCoupon.attr("title", "not editable");
                    maxCoupon.val(0);

                    $("#coupon_amount_label").text("{{translate('Coupon Amount')}} ({{session()->get('currency_symbol') ?? '$'}})");
                    $("#coupon").attr("placeholder", "Ex: 500")
                } else {
                    maxCoupon.removeAttr("readonly");
                    maxCoupon.removeAttr("title");
                    $("#coupon_amount_label").text("{{translate('Coupon Percent ')}}(%)")
                    $("#coupon").attr("placeholder", "Ex: 50%")
                }
            });

            $('#coupon_form').submit(function (e) {

                if (!$('#customer').val()) {
                    e.preventDefault();
                    toastr.error('{{ translate('please_select_customer') }}');
                }
                if (!$('#coupon_rules').val()) {
                    e.preventDefault();
                    toastr.error('{{ translate('please_select_coupon_rules') }}');
                }
                if (!$('#coupon_type').val()) {
                    e.preventDefault();
                    toastr.error('{{ translate('please_select_coupon_type') }}');
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

        $('.js-select-customer').select2({
            ajax: {
                url: '{{route('admin.customer.get-all-ajax')}}',
                data: function (params) {
                    return {
                        search: params.term, // search term
                        page: params.page,
                        all_customer: 1
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
