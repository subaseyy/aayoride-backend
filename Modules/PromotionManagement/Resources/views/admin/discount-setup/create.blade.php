@section('title', translate('add_New_Discount'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <form action="{{ route('admin.promotion.discount-setup.store') }}" method="POST" id="discountForm"
                  enctype="multipart/form-data">
                @csrf
                <h5 class="text-primary text-uppercase mb-4">{{ translate('New Discount') }}</h5>
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="title" class="mb-2">{{ translate('Title') }}
                                                </label>
                                                <input type="text" id="title" value="{{old('title')}}"
                                                       name="title" maxlength="100" class="form-control"
                                                       placeholder="Ex: 20% discount"
                                                       required>
                                            </div>
                                            <div class="col-12">
                                                <label for="shortDescription"
                                                       class="mb-2">{{ translate('short_description') }}
                                                    <small>({{translate('Max 150 character')}})</small>
                                                </label>
                                                <textarea id="shortDescription" name="short_description" cols="30"
                                                          rows="4" class="form-control" maxlength="150"
                                                          required>{{old('short_description')}}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label for="termsConditions"
                                                       class="mb-2">{{ translate('Terms & Conditions') }}
                                                    <small>({{translate('Max 400 character')}})</small>
                                                </label>
                                                <textarea id="termsConditions" name="terms_conditions" cols="30"
                                                          rows="5" class="form-control" maxlength="400"
                                                          required>{{old('terms_conditions')}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div
                                            class="bg-input h-100 rounded d-flex flex-column justify-content-center py-3">
                                            <div
                                                class="d-flex flex-column justify-content-around align-items-center gap-3 mb-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <h5 class="text-capitalize">{{ translate('discount_image') }}</h5>
                                                </div>

                                                <div class="d-flex">
                                                    <div class="upload-file">
                                                        <input type="file" class="upload-file__input" name="image"
                                                               accept=".jpg, .jpeg, .png" required>
                                                        <div class="upload-file__img upload-file__img_banner">
                                                            <img
                                                                src="{{ asset('public/assets/admin-module/img/media/banner-upload-file.png') }}"
                                                                alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <p class="opacity-75 mx-auto max-w220 text-center">
                                                    {{ translate('File Format - jpg, .jpeg, .png Image Size - Maximum Size 5 MB. Image Ratio - 3:1') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br/>
                                <br/>
                                <div class="row g-3">
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="zoneDiscountType" class="mb-2">
                                                {{ translate('select_zone') }}
                                            </label>
                                            <select class="js-select-2" id="zoneDiscountType"
                                                    name="zone_discount_type[]"
                                                    data-placeholder="{{translate('select_zone')}}"
                                                    multiple="multiple" required>
                                                <option value="{{ALL}}">{{translate('ALL')}}</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{$zone->id}}">{{$zone->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="customerLevelDiscountType"
                                                   class="mb-2">{{ translate('select_customer_level') }}
                                            </label>
                                            <select class="js-select-2" id="customerLevelDiscountType"
                                                    data-placeholder="{{translate('select_customer_level')}}"
                                                    name="customer_level_discount_type[]" multiple="multiple" required>
                                                <option value="{{ALL}}">{{translate('ALL')}}</option>
                                                @foreach($levels as $level)
                                                    <option value="{{$level->id}}">{{$level->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="customerDiscountType"
                                                   class="mb-2">{{ translate('select_customer') }}
                                            </label>
                                            <select class="js-select-2" id="customerDiscountType"
                                                    data-placeholder="{{translate('select_customer')}}"
                                                    name="customer_discount_type[]" multiple="multiple" required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div class="text-capitalize">
                                            <label for="limitPerUser" class="mb-2">
                                                {{ translate('Limit for Same User') }}
                                            </label>
                                            <input type="number" id="limitPerUser" name="limit_per_user"
                                                   value="{{old('limit_per_user')}}" min="1"
                                                   placeholder="{{translate('Ex : 10')}}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div class="text-capitalize">
                                            <label for="moduleDiscountType" class="mb-2">
                                                {{ translate('select_module') }}
                                            </label>
                                            <select class="js-select-2" id="moduleDiscountType"
                                                    data-placeholder="{{translate('select_module')}}"
                                                    name="module_discount_type[]" multiple="multiple" required>
                                                <option value="{{ALL}}">{{translate('ALL')}}</option>
                                                @foreach($vehicleCategories as $vehicleCategory)
                                                    <option
                                                        value="{{$vehicleCategory->id}}">{{ $vehicleCategory->name }}</option>
                                                @endforeach
                                                <option value="{{PARCEL}}">{{ translate(PARCEL) }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="discountAmountType"
                                                   class="mb-2">{{ translate('Discount Amount Type') }}</label>
                                            <select class="js-select" id="discountAmountType"
                                                    name="discount_amount_type" required>
                                                <option value="" selected disabled>
                                                    -- {{ translate('select_Discount_Amount_Type') }} --
                                                </option>
                                                <option value="{{AMOUNT}}">{{ translate('fixed_amount') }}</option>
                                                <option value="{{PERCENTAGE}}">{{ translate(PERCENTAGE) }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="discountAmount" id="discountAmountLabel"
                                                   class="mb-2">{{ translate('Discount Amount') }}</label>
                                            <input type="number" id="discountAmount" value="{{old('discount_amount')}}"
                                                   name="discount_amount" class="form-control" placeholder="Ex: 5"
                                                   step="any"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3 min_trip_amount">
                                        <div>
                                            <label for="maxDiscountAmount"
                                                   class="mb-2">{{ translate('Max Amount') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})
                                            </label>
                                            <input type="number" id="maxDiscountAmount" name="max_discount_amount"
                                                   class="form-control"
                                                   placeholder="Ex: 100" step="any" min="1"
                                                   value="{{old('max_discount_amount')}}">
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="minTripAmount"
                                                   class="mb-2">{{ translate('Min Trip Amount') }}
                                                ({{session()->get('currency_symbol') ?? '$'}})
                                            </label>
                                            <input type="number" id="minTripAmount" name="min_trip_amount" min="1"
                                                   class="form-control"
                                                   placeholder="Ex: 60" value="{{old('min_trip_amount')}}" step="any"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="start_date"
                                                   class="mb-2">{{ translate('start_date') }}</label>
                                            <input type="date" value="{{old('start_date')}}" id="start_date"
                                                   min="{{date('Y-m-d',strtotime(now()))}}"
                                                   name="start_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4 col-xl-3">
                                        <div>
                                            <label for="end_date" class="mb-2">{{ translate('end_date') }}</label>
                                            <input type="date" id="end_date" value="{{old('end_date')}}" name="end_date"
                                                   min="{{date('Y-m-d',strtotime(now()))}}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-3 mt-3">
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
    <script src="{{asset('public/assets/admin-module/js/promotion-management/discount-setup/create.js')}}"></script>

    <script>
        "use strict";


        $(document).ready(function () {

            const amountType = $('#discountAmountType');
            const maxDiscountAmount = $('#maxDiscountAmount');
            amountType.on('change', function () {
                if (amountType.val() == 'amount') {

                    maxDiscountAmount.attr("readonly", "true");
                    document.getElementById('maxDiscountAmount').setAttribute("title", "{{translate('Max discount amount field not editable for discount amount type Fixed amount')}}");
                    maxDiscountAmount.val(0);

                    $("#discountAmountLabel").text("{{translate('Discount Amount')}} ({{session()->get('currency_symbol') ?? '$'}})");
                    $("#discountAmount").attr("placeholder", "Ex: 500")
                } else {
                    maxDiscountAmount.removeAttr("readonly");
                    maxDiscountAmount.removeAttr("title");
                    $("#discountAmountLabel").text("{{translate('Discount Percent ')}}(%)")
                    $("#discountAmount").attr("placeholder", "Ex: 50%")
                }
            });

        });


        $('#customerLevelDiscountType').on('change', function () {
            let selectedValues = $(this).val();
            $.ajax({
                url: '{{route('admin.customer.get-level-wise-customer')}}',
                type: 'GET',
                data: {
                    levels: selectedValues
                },
                success: function (response) {
                    $('#customerDiscountType').empty();
                    $('#customerDiscountType').append('<option value="{{ALL}}">{{translate('ALL')}}</option>');
                    $.each(response, function (index, value) {
                        $('#customerDiscountType').append('<option value="' + value.id + '">' + value.first_name + ' ' + value.last_name + '</option>');
                    });
                }
            });
        });
    </script>
@endpush
