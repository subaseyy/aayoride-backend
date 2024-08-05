@extends('adminmodule::layouts.master')

@section('title', translate('Landing_Page'))

@push('css_or_js')
@endpush

@section('content')
    @php($env = env('APP_MODE') == 'live' ? 'live' : 'test')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('landing_page')}}</h2>
            @include('businessmanagement::admin.pages.partials._landing_page_inline_menu')


            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h5 class="text-primary text-uppercase">{{ translate('Testimonial') }}</h5>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.testimonial.update') }}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="reviewer_name"
                                                   class="mb-2">{{ translate('Reviewer_Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="reviewer_name"
                                                   name="reviewer_name"
                                                   value="{{ $data?->value['reviewer_name']  ?? "" }}"
                                                   placeholder="{{ translate('Ex: Ahmed') }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="designation" class="mb-2">{{ translate('Designation') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="designation" name="designation"
                                                   value="{{  $data?->value['designation']  ?? "" }}"
                                                   placeholder="{{ translate('Ex: Engineer') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="rating" class="mb-2">{{ translate('Rating') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="0" max="5" step=".1" class="form-control"
                                                   id="rating" name="rating"
                                                   value="{{ $data?->value['rating'] ?? "" }}"
                                                   placeholder="{{ translate('Ex: 5 Star') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="review" class="mb-2">{{ translate('Review') }} <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="review" id="review" rows="4" class="form-control"
                                                      placeholder="{{ translate('Ex: review ...') }}"
                                                      required>{{ $data?->value['review'] ?? "" }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center">
                                    <div class="d-flex flex-column gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-capitalize">{{ translate('Reviewer Image') }} <span
                                                    class="text-danger">*</span></h6>
                                            <span class="badge badge-primary">{{ translate('1:1') }}</span>
                                        </div>

                                        <div class="d-flex">
                                            <div class="upload-file">
                                                <input type="file" class="upload-file__input" name="reviewer_image"
                                                       accept="image/png, image/jpeg, image/jpg">
                                                <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                                <div class="upload-file__img">
                                                    <img
                                                        src="{{ $data?->value['reviewer_image'] ? asset('storage/app/public/business/landing-pages/testimonial/'.$data?->value['reviewer_image'])  :  asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                        alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-primary text-uppercase"
                                            type="submit">{{ translate('update') }}</button>
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


