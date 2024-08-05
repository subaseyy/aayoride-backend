@extends('adminmodule::layouts.master')

@section('title', translate($type ?? 'about_us'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/summernote/summernote-lite.min.css') }}" />
@endpush

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4">{{ translate('Business_Pages') }}</h2>

            <form action="{{ route('admin.business.pages-media.business-page.update') }}" id="business_pages_form"
                method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
                        <li class="nav-item">
                            <a href="{{ url()->current() }}?type=about_us"
                                class="nav-link text-capitalize {{ $type == 'about_us' ? 'active' : '' }}">{{ translate('about_us') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url()->current() }}?type=privacy_policy"
                                class="nav-link text-capitalize {{ $type == 'privacy_policy' ? 'active' : '' }}">{{ translate('privacy_policy') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url()->current() }}?type=terms_and_conditions"
                                class="nav-link text-capitalize {{ $type == 'terms_and_conditions' ? 'active' : '' }}">{{ translate('terms_and_conditions') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url()->current() }}?type=legal"
                                class="nav-link text-capitalize {{ $type == 'legal' ? 'active' : '' }}">{{ translate('legal') }}</a>
                        </li>
                    </ul>
                </div>

                @php($icons = ['about_us' => 'bi-file-person-fill', 'privacy_policy' => 'bi-shield-fill', 'terms_and_conditions' => 'bi-clipboard-minus-fill', 'legal' => 'bi-sign-stop-fill'])
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="fw-medium d-flex align-items-center gap-2 text-capitalize">
                            <i
                                class="bi {{ array_key_exists($type, $icons) ? $icons[$type] : 'bi-file-person-fill' }}"></i>
                            <strong>{{ translate($type) }}
                                {{ translate('page') }}</strong>
                        </h5>
                    </div>
                    <input type="hidden" name="type" value="{{ $type ?? 'about_us' }}">
                    <div class="card-body">
                        <div class="row mb-5 mb-lg-4">
                            <div class="col-lg-6">

                                <div class="mb-4">
                                    <label for="business_address"
                                        class="mb-2">{{ translate('short_description') }}</label>
                                    <textarea name="short_description" id="business_address" cols="30" rows="5" class="form-control"
                                        placeholder="{{ translate('Type Here ...') }}">{{ $data?->value['short_description'] }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex flex-column align-items-center gap-3">
                                    <h5 class="text-capitalize">{{ translate('page_banner') }}</h5>

                                    <div class="d-flex flex-column align-items-center gap-3">
                                        <div class="upload-file">
                                            <input type="file" name="image" class="upload-file__input" accept=".png">
                                            <span class="edit-btn">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                            </span>
                                            <div class="upload-file__img upload-file__img_banner">
                                                <img src="{{ onErrorImage(
                                                    $data?->value['image'],
                                                    asset('storage/app/public/business/pages') . '/' . $data?->value['image'],
                                                    asset('public/assets/admin-module/img/media/banner-upload-file.png'),
                                                    'business/pages/',
                                                ) }}"
                                                    alt="">
                                            </div>
                                        </div>
                                        <p class="opacity-75 max-w220 mx-auto">
                                            {{ translate('Image format - png | Image
                                                                                        Size - maximum size 2 MB Image Ratio - 3:1') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-2 text-capitalize">{{ translate('long_description') }}</h5>
                        <textarea id="summernote" name="long_description">{{ $data?->value['long_description'] }}</textarea>
                        <div class="col-12 d-flex justify-content-end mt-5">
                            <button type="submit"
                                class="btn btn-primary text-capitalize">{{ translate('submit') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->

@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/plugins/summernote/summernote-lite.min.js') }}"></script>

    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#business_pages_form').on('submit', function(e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: '{{ translate('describe_about_this_page') }}',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
@endpush
