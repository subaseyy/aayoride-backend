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


            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h5 class="text-primary text-uppercase">{{ translate('Header_Intro_Section') }}</h5>
                    </div>

                    <form action="{{route('admin.business.pages-media.landing-page.intro-section.update')}}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{INTRO_SECTION}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="title" class="mb-2">{{ translate('title') }}</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{$data?->value['title']??''}}"
                                           placeholder="{{ translate('Ex: Title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="subTitle" class="mb-2">{{ translate('sub_Title') }}</label>
                                    <input type="text" class="form-control" id="subTitle" name="sub_title"
                                           value="{{$data?->value['sub_title']??''}}"
                                           placeholder="{{ translate('Ex: Sub_Title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <div class="border rounded border-primary-light bg-primary-light px-3 py-2">
                                        <p class="d-flex text-primary text-capitalize">{{ translate('* Generate the link for the Intro section button from the CTA tab.') }}</p>
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

                    <form action="{{route('admin.business.pages-media.landing-page.intro-section.update')}}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{INTRO_SECTION_IMAGE}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex flex-column gap-3 mb-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="text-capitalize">{{ translate('background_Image') }}</h5>
                                        <span class="badge badge-primary">{{ translate('3:1') }}</span>
                                    </div>

                                    <div class="d-flex">
                                        <div class="upload-file">
                                            <input type="file" class="upload-file__input" name="background_image" accept="image/png, image/jpeg, image/jpg"
                                            {{ $data1?->value['background_image'] ? '' : 'required' }}>
                                            <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                            <div class="upload-file__img upload-file__img_banner aspect-ratio-3-1 overflow-hidden d-flex justify-content-center">
                                                @if($data1?->value['background_image'] && file_exists('storage/app/public/business/landing-pages/intro-section/'.$data1?->value['background_image']))
                                                    <img class="aspect-ratio-auto h-100"
                                                        src="{{ $data1?->value['background_image'] ? asset('storage/app/public/business/landing-pages/intro-section/'.$data1?->value['background_image']):asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                        alt="">
                                                @else
                                                    <img class="aspect-ratio-auto h-100" src="{{ asset('public/assets/admin-module/img/media/upload-file.png') }}" alt="">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column gap-1">
                                        <p class="mb-0 title-color">{{ translate('Min Size for Better Resolution 488x244 px') }}</p>
                                        <p class="fs-12">{{ translate('Image format : jpg, png, jpeg | Maximum size : 5MB') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
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


