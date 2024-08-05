@section('title', translate('add_New_Vehicle'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3 justify-content-between mb-4">
                <h2 class="fs-22 text-capitalize">{{ translate('add_New_Vehicle') }}</h2>
            </div>
            <form id="vehicle_form" action="{{ route('admin.vehicle.store') }}" enctype="multipart/form-data" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('vehicle_information') }}
                                </h5>

                                <div class="row align-items-end">
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="brand_id" class="mb-2">{{ translate('vehicle_brand') }}
                                                <span class="text-danger">*</span></label>
                                            <select class="js-select-ajax" name="brand_id" id="brand_id"
                                                data-placeholder="{{ translate('select_brand') }}"
                                                onchange="ajax_models('{{ url('/') }}/admin/vehicle/attribute-setup/model/ajax-models/'+this.value)"
                                                required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4" id="model-selector">
                                            <label for="model_id" class="mb-2">{{ translate('vehicle_model') }}
                                                <span class="text-danger">*</span></label>
                                            <select class="js-select-ajax theme-input-style w-100 form-control" name="model_id"
                                                id="model_id"
                                                data-placeholder="{{ translate('please_select_vehicle_model') }}"
                                                required>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="vehicle_category"
                                                class="mb-2">{{ translate('vehicle_category') }} <span
                                                    class="text-danger">*</span></label>
                                            <select id="vehicle_category" class="js-select-ajax" name="category_id"
                                                data-placeholder="{{ translate('select_vehicle_category') }}" required>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="licence_plate_num"
                                                class="mb-2">{{ translate('licence_plate_number') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" value="{{ old('licence_plate_number') }}"
                                                id="licence_plate_number" class="form-control" name="licence_plate_number"
                                                placeholder="Ex: DB-3212 " required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="licence_expire_date"
                                                class="mb-2">{{ translate('licence_expire_date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" id="licence_expire_date" name="licence_expire_date"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="vin_number" class="mb-2">{{ strtoupper(translate('vin')) }}
                                                {{ translate('number') }}
                                                </label>
                                            <input type="text" value="{{ old('vin_number') }}" id="vin_number"
                                                class="form-control" name="vin_number" placeholder="Ex: 1HGBH41JXMN109186"
                                                >
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4">
                                            <label for="transmission" class="mb-2">{{ translate('transmission') }}
                                            </label>
                                            <input type="text" value="{{ old('transmission') }}" id="transmission"
                                                class="form-control" name="transmission" placeholder="Ex: AMT">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="fuel_type" class="mb-2">{{ translate('fuel_type') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="js-select" id="fuel_type" name="fuel_type" required>
                                                <option value="" selected disabled>
                                                    {{ translate('select_fuel_type') }}</option>
                                                <option value="petrol">{{ translate('petrol') }}</option>
                                                <option value="diesel">{{ translate('diesel') }}</option>
                                                <option value="cng">{{ translate('cng') }}</option>
                                                <option value="lpg">{{ translate('lpg') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="ownership" class="mb-2">{{ translate('ownership') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="js-select required" id="ownership" name="ownership" required>
                                                <option value="" selected disabled>
                                                    {{ translate('select_owner') }}</option>
                                                <option value="admin">{{ translate('admin') }}</option>
                                                <option value="driver">{{ translate('driver') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="driver" class="mb-2">{{ translate('driver') }} <span
                                                    class="text-danger">*</span></label>
                                            <select required class="js-select-driver required" id="driver"
                                                name="driver_id">
                                                <option value="" selected disabled>
                                                    {{ translate('select_driver') }}</option>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="mb-4 text-capitalize">{{ translate('upload_documents') }}</h5>

                                <div class="d-flex flex-wrap gap-3">
                                    <div class="upload-file file__input" id="file__input">
                                        <input type="file" class="upload-file__input2" multiple="multiple"
                                            name="upload_documents[]" required>
                                        <div class="upload-file__img2">
                                            <div class="upload-box rounded media gap-4 align-items-center p-4 px-lg-5">
                                                <i class="bi bi-cloud-arrow-up-fill fs-20"></i>
                                                <div class="media-body">
                                                    <p class="text-muted mb-2 fs-12">{{ translate('upload') }}</p>
                                                    <h6 class="fs-12 text-capitalize">
                                                        {{ translate('file_or_image') }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-3">
                    <button class="btn btn-primary" type="submit">{{ translate('submit') }}</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/vehicle-management/vehicle/create.js') }}"></script>
    <script>
        "use strict";

        function ajax_models(route) {
            $.get({
                url: route,
                dataType: 'json',
                data: {},
                beforeSend: function() {},
                success: function(response) {
                    $('#model-selector').html(response.template);
                },
                complete: function() {

                },
            });
        }

        $('#brand_id').select2({
            ajax: {
                url: '{{ route('admin.vehicle.attribute-setup.brand.all-brands', parameters: ['status' => 'active']) }}',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                    };
                },
                processResults: function(data) {
                    //
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });

        $('#vehicle_category').select2({
            ajax: {
                url: '{{ route('admin.vehicle.attribute-setup.category.all-categories', parameters: ['status' => 'active']) }}',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    let $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });

        let all_driver = 0;

        $('.js-select-driver').select2({
            ajax: {
                url: '{{ route('admin.driver.get-all-ajax-vehicle') }}',
                data: function(params) {
                    return {
                        search: params.term, // search term
                        all_driver: all_driver,
                        page: params.page
                    };
                },
                processResults: function(data) {

                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });

        $('#vehicle_form').on('submit', function(event) {
            if ($('#model_id').val() === null) {
                toastr.error('{{ translate('fill_up_vehicle_model') }}')
                event.preventDefault()
            }
            if ($('#fuel_type').val() === null) {
                toastr.error('{{ translate('fill_up_fuel_type') }}')
                event.preventDefault()
            }
            if ($('#ownership').val() === null) {
                toastr.error('{{ translate('fill_up_ownership') }}')
                event.preventDefault()
            }
            if ($('#driver').val() === null) {
                toastr.error('{{ translate('fill_up_driver') }}')
                event.preventDefault()
            }
        })
    </script>
@endpush
