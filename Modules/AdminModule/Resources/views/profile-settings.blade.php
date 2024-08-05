@extends('adminmodule::layouts.master')

@section('title', translate('Update_Profile'))

@push('css_or_js')
@endpush

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">{{ translate('update_profile') }}</h2>
            </div>

            <form action="{{ route('admin.update-profile', ['id' => auth()->id()]) }}" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="card overflow-visible">
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-lg-8">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('general_information') }}
                                </h5>

                                <div class="row align-items-end">
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label for="f_name"
                                                class="mb-2 text-capitalize">{{ translate('first_name') }}</label>
                                            <input type="text" value="{{ auth()->user()?->first_name }}" name="first_name"
                                                id="f_name" class="form-control"
                                                placeholder="{{ translate('ex: Maximilian') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label for="l_name" class="mb-2">{{ translate('last_name') }}</label>
                                            <input type="text" value="{{ auth()->user()?->last_name }}" name="last_name"
                                                id="l_name" class="form-control"
                                                placeholder="{{ translate('ex: Schwarzmüller') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-4">
                                            <label for="p_email" class="mb-2">{{ translate('email') }}</label>
                                            <input type="email" value="{{ auth()->user()->email }}" name="email"
                                                id="p_email" class="form-control"
                                                placeholder="{{ translate('ex') }}: {{ translate('company@company.com') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="mb-4">
                                            <label for="phone_number" class="mb-2">{{ translate('phone') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="tel" pattern="[0-9]{1,14}" required value="{{ auth()->user()->phone }}"
                                                id="phone_number" class="form-control w-100 text-dir-start"
                                                placeholder="{{ translate('ex: xxxxx xxxxxx') }}">
                                            <input type="hidden" id="phone_number-hidden-element" name="phone">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-4 input-group_tooltip">
                                            <label for="password" class="mb-2">{{ translate('password') }}</label>
                                            <input type="password" name="password" id="password"
                                                class="form-control" placeholder="{{ translate('ex') }}: ********">
                                            <i id="password-eye" class="mt-3 bi bi-eye-slash-fill text-primary tooltip-icon"
                                                data-bs-toggle="tooltip" data-bs-title=""></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4 input-group_tooltip">
                                            <label for="confirm_password"
                                                class="mb-2">{{ translate('confirm_password') }}</label>
                                            <input type="password" name="confirm_password"  id="confirm_password"
                                                class="form-control" placeholder="{{ translate('ex') }}: ********">
                                            <i id="conf-password-eye"
                                                class="mt-3 bi bi-eye-slash-fill text-primary tooltip-icon"
                                                data-bs-toggle="tooltip" data-bs-title=""></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="d-flex flex-column justify-content-around gap-3">
                                    <h5 class="text-center text-capitalize">{{ translate('profile_image') }}</h5>

                                    <div class="d-flex justify-content-center">
                                        <div class="upload-file">
                                            <input type="file" name="profile_image" class="upload-file__input" accept=".jpg, .jpeg, .png">
                                            <div class="upload-file__img w-auto h-auto">
                                                <img width="150"
                                                    src="{{ onErrorImage(
                                                        auth()->user()?->profile_image,
                                                        asset('storage/app/public/employee/profile') . '/' . auth()->user()->profile_image,
                                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                        'employee/profile/',
                                                    ) }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <p class="opacity-75 mx-auto max-w220">
                                        {{ translate('File Format - jpg, png, jpeg') }}
                                        {{ translate('Image Size - Maximum Size 5 MB.') }}</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-3">
                                <button class="btn btn-primary" type="submit">{{ translate('save') }}</button>
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
    <link href="{{ asset('public/assets/admin-module/css/intlTelInput.min.css') }}" rel="stylesheet"/>
    <script src="{{ asset('public/assets/admin-module/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin-module/js/password.js') }}"></script>

    <script>
        "use strict";
        initializePhoneInput("#phone_number", "#phone_number-hidden-element");
    </script>
@endpush
