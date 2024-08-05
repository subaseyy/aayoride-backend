@extends('adminmodule::layouts.master')

@section('title', translate('Landing_Page'))

@push('css_or_js')
@endpush

@section('content')
    @php($env = env('APP_MODE') == 'live' ? 'live' : 'test')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('landing_page_setup')}}</h2>
            @include('businessmanagement::admin.pages.partials._landing_page_inline_menu')

            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h6 class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-calendar"></i>
                            {{ translate('section_Content') }}
                        </h6>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.our-solutions.update') }}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="id" value="{{ $data->id }}">
                                        <div class="mb-4">
                                            <label for="solution_title" class="mb-2">
                                                {{ translate('Title') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="solution_title" name="title"
                                                   placeholder="{{ translate('ex') }}: {{ translate('Ride_Sharing') }}"
                                                   value="{{ $data?->value['title']  ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="solution_description" class="mb-2">
                                                {{ translate('Description') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="description" id="solution_description" rows="4" class="form-control"
                                                      placeholder="{{ translate('ex') }}: {{ translate('Section_Description') }}"
                                                      required>{{ $data?->value['description']  ?? "" }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="d-flex flex-column gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-capitalize">
                                                {{ translate('Icon / Image') }}
                                                <span class="text-danger">*</span>
                                            </h6>
                                            <span class="badge badge-primary">{{ '290x290 px' }}</span>
                                        </div>

                                        <div class="d-flex">
                                            <div class="upload-file">
                                                <input type="file" class="upload-file__input" name="image"
                                                       accept="image/png, image/jpeg, image/jpg">
                                                <div class="upload-file__img" style="--size: 11rem;">
                                                    <img
                                                        src="{{ $data?->value['image'] ? asset('storage/app/public/business/landing-pages/our-solutions/'.$data?->value['image'])  :  asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                        alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-secondary text-uppercase" type="reset">
                                        {{ translate('reset') }}
                                    </button>
                                    <button class="btn btn-primary text-uppercase" type="submit">
                                        {{ translate('save') }}
                                    </button>
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
