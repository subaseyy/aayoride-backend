@section('title', 'Banner Setup')

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h5 class="text-primary text-uppercase">{{ translate('edit_banner') }}</h5>
                    </div>

                    <form action="{{ route('admin.promotion.banner-setup.update', ['id' => $banner->id]) }}"
                          id="banner_form"
                          enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="banner_title" class="mb-2">{{ translate('banner_title') }}</label>
                                    <input type="text" class="form-control" id="banner_title" name="banner_title"
                                           value="{{ $banner->name }}" placeholder="Ex: 50% Off" required>
                                </div>
                                <div class="mb-4">
                                    <label for="sort_description"
                                           class="mb-2">{{ translate('short_description') }}</label>
                                    <textarea name="short_desc" id="sort_description" placeholder="Type Here..."
                                              class="form-control" cols="30"
                                              rows="6" required>{{ $banner->description }}</textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="redirect_link"
                                           class="mb-2">{{ translate('redirect_link') }}</label>
                                    <input type="text" class="form-control" id="redirect_link" name="redirect_link"
                                           value="{{ $banner->redirect_link }}" placeholder="Ex: www.google.com"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column justify-content-around align-items-center gap-3 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="text-capitalize">{{ translate('banner_image') }}
                                        </h5>
                                    </div>
                                    <div class="d-flex">
                                        <div class="upload-file">
                                            <input type="file" class="upload-file__input" name="banner_image">
                                            <span class="edit-btn">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                            </span>
                                            <div class="upload-file__img upload-file__img_banner">
                                                <img src="{{ onErrorImage(
                                                    $banner?->image,
                                                    asset('storage/app/public/promotion/banner') . '/' . $banner?->image,
                                                    asset('public/assets/admin-module/img/media/banner-upload-file.png'),
                                                    'promotion/banner/',
                                                ) }}"
                                                     alt="">
                                            </div>
                                        </div>
                                    </div>
                                    <p class="opacity-75 mx-auto max-w220">
                                        {{ translate('File Format - jpg, .jpeg, .png Image Size - Maximum Size 5 MB. Image Ratio - 3:1') }}
                                    </p>
                                </div>
                                <div class="mb-4 text-capitalize">
                                    <label for="time_period" class="mb-2">{{ translate('time_period') }}</label>
                                    <select name="time_period" class="js-select" id="time_period"
                                            aria-label="{{ translate('time_period') }}">
                                        <option disabled selected>{{ translate('select_time_period') }}</option>
                                        <option
                                            value="all_time" {{ $banner->time_period == 'all_time' ? 'selected' : '' }}>
                                            {{ translate('all_time') }}</option>
                                        <option value="period" {{ $banner->time_period == 'period' ? 'selected' : '' }}>
                                            {{ translate('period') }}</option>
                                    </select>
                                </div>

                                <div
                                    class="date-pick {{ $banner->start_date && $banner->end_date != null ? 'd-block' : 'd-none' }}">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="mb-4">
                                                <label for="start_date"
                                                       class="mb-2">{{ translate('start_date') }}</label>
                                                <input type="date" name="start_date" id="start_date" min="{{date('Y-m-d',strtotime(now()))}}"
                                                       value="{{ $banner->start_date }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-4">
                                                <label for="end_date"
                                                       class="mb-2">{{ translate('end_date') }}</label>
                                                <input type="date" name="end_date" id="end_date" min="{{date('Y-m-d',strtotime(now()))}}"
                                                       value="{{ $banner->end_date }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-primary text-uppercase"
                                            type="submit">{{ translate('submit') }}</button>
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
    <script src="{{ asset('public/assets/admin-module/js/promotion-management/banner-setup/edit.js') }}"></script>
    <script>
        "use strict";
        $('#banner_form').submit(function (e) {
            let timePeriod = $('#time_period').val();

            if (timePeriod === 'period' && $('#start_date').val() === '') {
                toastr.error('{{ translate('please_select_start_date') }}');
                e.preventDefault();
            }

            if (timePeriod === 'period' && $('#end_date').val() === '') {
                toastr.error('{{ translate('please_select_end_date') }}');
                e.preventDefault();
            }

            if (!timePeriod) {
                toastr.error('{{ translate('please_select_time_period') }}');
                e.preventDefault();
            }

        });
    </script>
@endpush
