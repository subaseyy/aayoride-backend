@section('title', 'Vehicle Category')

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
                    <form action="{{ route('admin.vehicle.attribute-setup.category.update', ['id' => $category->id]) }}"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('category_edit') }}</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="category_name"
                                                class="mb-2">{{ translate('category_name') }}</label>
                                            <input type="text" id="category_name" required name="category_name"
                                                class="form-control" value="{{ $category->name }}"
                                                placeholder="Ex: Category">
                                        </div>
                                        <div class="mb-4">
                                            <label for="category_type"
                                                class="mb-2">{{ translate('category_type') }}</label>
                                            <select id="category_type" name="type" class="form-control js-select"
                                                data-toggle="tooltip" data-placement="top" title="Tooltip on top">
                                                <option value="car" {{ $category['type'] == 'car' ? 'selected' : '' }}>
                                                    {{ translate('car') }}</option>
                                                <option value="motor_bike"
                                                    {{ $category['type'] == 'motor_bike' ? 'selected' : '' }}>
                                                    {{ translate('Motor_Bike') }}</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="short_desc"
                                                class="mb-2">{{ translate('short_description') }}</label>
                                            <textarea id="short_desc" required rows="5" name="short_desc" class="form-control" placeholder="Ex: Description"> {{ $category->description }} </textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h5 class="text-center text-capitalize">{{ translate('category_image') }}
                                            </h5>

                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" accept=".png" name="category_image">
                                                    <span class="edit-btn">
                                                        <i class="bi bi-pencil-square text-primary"></i>
                                                    </span>
                                                    <div class="upload-file__img w-auto h-auto">
                                                        <img width="150" id="image_id"
                                                            src="{{ onErrorImage(
                                                                $category?->image,
                                                                asset('storage/app/public/vehicle/category') . '/' . $category?->image,
                                                                asset('public/assets/admin-module/img/media/upload-file.png'),
                                                                'vehicle/category/',
                                                            ) }}"
                                                            alt="" id="image_id">
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
                                    <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
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
