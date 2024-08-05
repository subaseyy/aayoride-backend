@section('title', 'Vehicle Attribute')

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3 justify-content-between mb-4">
                <h2 class="fs-22 text-capitalize">{{ translate('vehicle_attribute') }}</h2>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <form action="{{ route('admin.vehicle.attribute-setup.model.update', ['id' => $model->id]) }}"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('model_edit') }}</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label for="model_name"
                                                        class="mb-2">{{ translate('model_name') }}</label>
                                                    <input required type="text" required id="model_name"
                                                        name="name" value="{{ $model->name }}" class="form-control"
                                                        placeholder="Ex: Model">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label for="brand_select"
                                                        class="mb-2">{{ translate('brand_name') }}</label>
                                                    <select name="brand_id" id="brand_select"
                                                        class="js-select-ajax text-capitalize">
                                                        @if (isset($model->brand))
                                                            <option value="{{ $model->brand->id }}" selected="selected">
                                                                {{ $model->brand->name }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label for="seat_capacity"
                                                        class="mb-2">{{ translate('seat_capacity') }}</label>
                                                    <input required type="number" id="seat_capacity" min="0"
                                                        name="seat_capacity" class="form-control"
                                                        value="{{ $model->seat_capacity }}" placeholder="Ex: Seat Capacity">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label for="maximum_weight"
                                                        class="mb-2">{{ translate('maximum_weight') }}
                                                        (KG)</label>
                                                    <input required type="number" id="maximum_weight" step=".01"
                                                        min="0" name="maximum_weight" class="form-control"
                                                        value="{{ $model->maximum_weight }}"
                                                        placeholder="Ex: Maximum Weight">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label for="hatch_bag_capacity"
                                                        class="mb-2">{{ translate('hatch_bag_capacity') }}</label>
                                                    <input required type="number" id="hatch_bag_capacity" min="0"
                                                        name="hatch_bag_capacity" class="form-control"
                                                        value="{{ $model->hatch_bag_capacity }}"
                                                        placeholder="Ex: Hatch Bag Capacity">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-4">
                                                    <label for="engine"
                                                        class="mb-2">{{ translate('engine') }}</label>
                                                    <input required type="text" id="engine" name="engine"
                                                        class="form-control" value="{{ $model->engine }}"
                                                        placeholder="Ex: Engine">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="short_desc"
                                                class="mb-2">{{ translate('short_description') }}</label>
                                            <textarea id="short_desc" rows="5" name="short_desc" class="form-control" placeholder="Ex: Description" required>{{ $model->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h5 class="text-center text-capitalize">{{ translate('model_image') }}
                                            </h5>

                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" accept=".png" name="model_image">
                                                    <span class="edit-btn">
                                                        <i class="bi bi-pencil-square text-primary"></i>
                                                    </span>
                                                    <div class="upload-file__img w-auto h-auto">
                                                        <img width="150" id="image_id"
                                                            src="{{ onErrorImage(
                                                                $model?->image,
                                                                asset('storage/app/public/vehicle/model') . '/' . $model?->image,
                                                                asset('public/assets/admin-module/img/media/upload-file.png'),
                                                                'vehicle/model/',
                                                            ) }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="opacity-75 mx-auto text-center max-w220">
                                                {{ translate('5MB_image_note') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="reset" id="reset_btn"
                                        class="btn btn-secondary">{{ translate('reset') }}</button>
                                    <button type="submit"
                                        class="btn btn-primary">{{ translate('update') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script>
        "use strict";
        $('.js-select-ajax').select2({
            ajax: {
                url: '{{ route('admin.vehicle.attribute-setup.brand.all-brands') }}',
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data) {
                    //
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

        // Assuming you have a reset button with ID 'reset-button'
        let resetButton = $('#reset_btn');
        let defaultImageSrc = '{{ asset('public/assets/admin-module/img/media/upload-file.png') }}';
        let imageElement = $('#image_id');
        let fileInput = $('.upload-file__input');

        resetButton.on('click', function() {
            imageElement.attr('src', defaultImageSrc);
            fileInput.val('');
        });
    </script>
@endpush
