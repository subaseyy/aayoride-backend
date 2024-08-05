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
                        <h5 class="text-primary text-uppercase">{{ translate('CTA') }}</h5>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.cta.update') }}" id="banner_form"
                          enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{CTA}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="title" class="mb-2">{{ translate('title') }}</label>
                                    <input type="text" class="form-control" id="title"
                                           value="{{ $data?->value['title'] ?? "" }}" name="title"
                                           placeholder="{{ translate('Ex: Title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="subTitle" class="mb-2">{{ translate('sub_Title') }}</label>
                                    <input type="text" class="form-control" id="subTitle"
                                           value="{{ $data?->value['sub_title'] ?? "" }}" name="sub_title"
                                           placeholder="{{ translate('Ex: Sub_Title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h6 class="d-flex align-items-center gap-2 mb-3">
                                        <img width="22"
                                             src="{{ asset('public/assets/admin-module/img/media/play-store.png') }}"
                                             alt="">
                                        {{ translate('Playstore_Button') }}
                                    </h6>
                                    <div class="rounded bg-light p-3 p-lg-4">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between gap-2">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <label for="playStoreUserDownloadLink"
                                                           class="mb-0">{{ translate('User Download Link') }}</label>
                                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                       title="{{translate('User Download Link')}}"></i>
                                                </div>
                                            </div>
                                            <input type="url" class="form-control" id="playStoreUserDownloadLink"
                                                   name="play_store_user_download_link"
                                                   value="{{ $data?->value['play_store']['user_download_link'] ?? "" }}"
                                                   placeholder="{{ translate('Ex: https://play.google.com/store/apps') }}"
                                                   required>
                                        </div>
                                        <div class="">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <label for="playStoreDriverDownloadLink"
                                                       class="mb-0">{{ translate('Driver Download Link') }}</label>
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{translate('Driver Download Link')}}"></i>
                                            </div>
                                            <input type="url" class="form-control" id="playStoreDriverDownloadLink"
                                                   name="play_store_driver_download_link"
                                                   value="{{ $data?->value['play_store']['driver_download_link'] ?? "" }}"
                                                   placeholder="{{ translate('Ex: https://play.google.com/store/apps') }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <h6 class="d-flex align-items-center gap-2 mb-3">
                                        <img width="22"
                                             src="{{ asset('public/assets/admin-module/img/media/app-store.png') }}"
                                             alt="">
                                        {{ translate('app_Store_Button') }}
                                    </h6>
                                    <div class="rounded bg-light p-3 p-lg-4">
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-between gap-2">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <label for="appStoreUserDownloadLink"
                                                           class="mb-0">{{ translate('User Download Link') }}</label>
                                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                       title="{{translate('User Download Link')}}"></i>
                                                </div>
                                            </div>
                                            <input type="url" class="form-control" id="appStoreUserDownloadLink"
                                                   name="app_store_user_download_link"
                                                   value="{{ $data?->value['app_store']['user_download_link'] ?? "" }}"
                                                   placeholder="{{ translate('Ex: https://play.google.com/store/apps') }}"
                                                   required>
                                        </div>
                                        <div class="">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <label for="appStoreDriverDownloadLink"
                                                       class="mb-0">{{ translate('Driver Download Link') }}</label>
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   title="{{translate('Driver Download Link')}}"></i>
                                            </div>
                                            <input type="url" class="form-control" id="appStoreDriverDownloadLink"
                                                   name="app_store_driver_download_link"
                                                   value="{{ $data?->value['app_store']['driver_download_link'] ?? "" }}"
                                                   placeholder="{{ translate('Ex: https://play.google.com/store/apps') }}"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-secondary text-uppercase"
                                            type="reset">{{ translate('reset') }}</button>
                                    <button class="btn btn-primary text-uppercase"
                                            type="submit">{{ translate('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-30">
                        <h5 class="text-primary text-uppercase">{{ translate('Image_Section') }}</h5>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.cta.update') }}" id="banner_form"
                          enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{CTA_IMAGE}}">
                        <div class="row">
                            <div class="col-md-4 col-xl-3">
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="text-capitalize">{{ translate('image') }}</h5>
                                        <span class="badge badge-primary">{{ translate('1:1') }}</span>
                                    </div>

                                    <div class="d-flex">
                                        <div class="upload-file">
                                            <input type="file" class="upload-file__input" name="image"
                                                   accept="image/png, image/jpeg">
                                            <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                            <div class="upload-file__img">
                                                <img
                                                    src="{{ $data1?->value['image'] ? asset('storage/app/public/business/landing-pages/cta/'.$data1?->value['image']) : asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column gap-1">
                                        <p class="mb-0 title-color">{{ translate('Min Size for Better Resolution 408x408 px') }}</p>
                                        <p class="fs-12">{{ translate('Image format : jpg, png, jpeg | Maximum size : 5MB') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-xl-9">
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="text-capitalize">{{ translate('background_Image') }}</h5>
                                        <span class="badge badge-primary">{{ translate('3:1') }}</span>
                                    </div>

                                    <div class="d-flex">
                                        <div class="upload-file">
                                            <input type="file" class="upload-file__input" name="background_image"
                                                   accept="image/png, image/jpeg">
                                            <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                            <div class="upload-file__img upload-file__img_banner">
                                                <img
                                                    src="{{ $data1?->value['background_image'] ? asset('storage/app/public/business/landing-pages/cta/'.$data1?->value['background_image']) : asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-secondary text-uppercase"
                                            type="reset">{{ translate('reset') }}</button>
                                    <button class="btn btn-primary text-uppercase"
                                            type="submit">{{ translate('save') }}</button>
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


