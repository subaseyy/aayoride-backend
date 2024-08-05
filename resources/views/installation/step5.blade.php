@extends('installation.layouts.master')
@section('title', 'Third Step')
@section('content')
    @include('installation.layouts.title')


    <!-- Progress -->
    <div class="pb-2">
        <div class="progress cursor-pointer" role="progressbar" aria-label="DriveMond Software Installation"
             aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" data-bs-toggle="tooltip"
             data-bs-placement="top" data-bs-custom-class="custom-progress-tooltip" data-bs-title="Final Step!"
             data-bs-delay='{"hide":1000}'>
            <div class="progress-bar w-d" style="--width: 90%"></div>
        </div>
    </div>

    <!-- Card -->
    <div class="card mt-4 position-relative">
        <div class="d-flex justify-content-end mb-2 position-absolute top-end">
            <a href="#" class="d-flex align-items-center gap-1">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                              data-bs-title="Admin setup">

                            <img src="{{asset('public/assets/installation')}}/assets/img/svg-icons/info.svg" alt=""
                                 class="svg">
                        </span>
            </a>
        </div>
        <div class="p-4 mb-md-3 mx-xl-4 px-md-5">
            <div class="d-flex align-items-center column-gap-3 flex-wrap">
                <h5 class="fw-bold fs text-uppercase">{{translate('Step 5.')}} </h5>
                <h5 class="fw-normal">{{translate('Admin Account Settings')}}</h5>
            </div>
            <p class="mb-4">{{translate('These information will be used to create')}} <strong>{{translate('super admin credential')}}</strong>
                {{translate('for your admin panel')}}.
            </p>

            <form method="POST" action="{{ route('system_settings',['token'=>bcrypt('step_6')]) }}">
                @csrf
                <div class="bg-light p-4 rounded mb-4">
                    <div class="px-xl-2 pb-sm-3">
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <div class="from-group">
                                    <label for="first-name" class="d-flex align-items-center gap-2 mb-2">{{translate('Business Name')}}</label>
                                    <input type="text" id="first-name" class="form-control" name="web_name"
                                           required placeholder="Ex: DriveMond">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="first-name" class="d-flex align-items-center gap-2 mb-2">
                                        {{translate('First Name')}}</label>
                                    <input type="text" id="first-name" class="form-control" name="first_name"
                                           required placeholder="Ex: John">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="last-name" class="d-flex align-items-center gap-2 mb-2">
                                        {{translate('Last Name')}}</label>
                                    <input type="text" id="last-name" class="form-control" name="last_name"
                                           required placeholder="Ex: Doe">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="phone" class="d-flex align-items-center gap-2 mb-2">
                                        <span class="fw-medium">{{translate('Phone')}}</span>
                                        <span class="cursor-pointer" data-bs-toggle="tooltip"
                                              data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                              data-bs-html="true"
                                              data-bs-title="Provide an valid number. This number will be use to send verification code and other attachments in future">
                                                    <img
                                                        src="{{asset('public/assets/installation')}}/assets/img/svg-icons/info2.svg"
                                                        class="svg" alt="">
                                                </span>
                                    </label>

                                    <div class="number-input-wrap">
                                        <div class="row">
                                            <div class="col-md-3 pe-0">
                                                <select name="phone-number" id="phone-number" class="form-control">
                                                    @foreach(TELEPHONE_CODES as $item)
                                                        <option value="{{$item['code']}}">{{$item['name']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-9 ps-0">
                                                <input type="tel" pattern="[0-9]{1,14}" id="phone" class="form-control" name="phone" required
                                                       placeholder="Ex: 9837530836">
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="email" class="d-flex align-items-center gap-2 mb-2">
                                        <span class="fw-medium">{{translate('Email')}}</span>
                                        <span class="cursor-pointer" data-bs-toggle="tooltip"
                                              data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                              data-bs-html="true"
                                              data-bs-title="Provide an valid email. This email will be use to send verification code and other attachments in future">
                                                    <img
                                                        src="{{asset('public/assets/installation')}}/assets/img/svg-icons/info2.svg"
                                                        class="svg" alt="">
                                                </span>
                                    </label>

                                    <input type="email" id="email" class="form-control" name="email" required
                                           placeholder="Ex: jhone@doe.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="password"
                                           class="d-flex align-items-center gap-2 mb-2">{{translate('Password')}}</label>
                                    <div class="input-inner-end-ele position-relative input-group_tooltip">
                                        <input type="password" autocomplete="new-password" id="password"
                                               name="password" required class="form-control"
                                               placeholder="Ex: 8+ character" minlength="8">
                                        <i id="password-eye" class="bi bi-eye-slash-fill text-dark tooltip-icon" data-bs-toggle="tooltip" data-bs-title=""></i>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="from-group">
                                    <label for="confirm-password" class="d-flex align-items-center gap-2 mb-2">{{translate('Confirm Password')}}</label>
                                    <div class="input-inner-end-ele position-relative input-group_tooltip">
                                        <input type="password" autocomplete="new-password" id="confirm_password"
                                               name="confirm_password" class="form-control" placeholder="Ex: 8+ character" required>
                                        <i id="conf-password-eye" class="bi bi-eye-slash-fill text-dark tooltip-icon" data-bs-toggle="tooltip" data-bs-title=""></i>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-dark px-sm-5">{{translate('Complete Installation')}}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')

    <script>
        "use strict";
        let confPassword = document.getElementById('confirm_password');
        let confPasswordIcon = document.getElementById('conf-password-eye');
        let password = document.getElementById('password');
        let passwordIcon = document.getElementById('password-eye');
        confPasswordIcon.onclick = function () {
            if (confPassword.getAttribute('type') === 'text') {
                confPassword.setAttribute('type', 'password');
                confPasswordIcon.removeAttribute('class');
                confPasswordIcon.setAttribute('class', 'bi bi-eye-slash-fill text-dark tooltip-icon');
            } else {
                confPassword.setAttribute('type', 'text');
                confPasswordIcon.removeAttribute('class');
                confPasswordIcon.setAttribute('class', 'bi bi-eye-fill text-dark tooltip-icon');
            }
        }

        passwordIcon.onclick = function () {
            if (password.getAttribute('type') === 'text') {
                password.setAttribute('type', 'password');
                passwordIcon.removeAttribute('class');
                passwordIcon.setAttribute('class', 'bi bi-eye-slash-fill text-dark tooltip-icon');
            } else {
                password.setAttribute('type', 'text');
                passwordIcon.removeAttribute('class');
                passwordIcon.setAttribute('class', 'bi bi-eye-fill text-dark tooltip-icon');
            }
        }
    </script>
@endpush

