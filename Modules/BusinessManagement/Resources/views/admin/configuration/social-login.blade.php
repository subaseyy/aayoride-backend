@extends('adminmodule::layouts.master')

@section('title', translate('Social_Media_Logins'))

@section('content')
    @php($facebook = $settings->where('key_name', 'facebook_login')->first()?->value)
    @php($google = $settings->where('key_name', 'google_login')->first()?->value)
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('3rd_party')}}</h2>
            @include('businessmanagement::admin.configuration.partials._third_party_inline_menu')

            <div class="row gy-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                                <h5 class="d-flex align-items-center gap-2 text-capitalize">
                                    <img width="20" src="{{asset('public/assets/admin-module/img/icons/google.png')}}"
                                         alt="" class="dark-support">
                                    {{translate('google_login')}}
                                </h5>
                                <button type="button" class="btn btn-outline-primary text-capitalize"
                                        data-bs-toggle="modal" data-bs-target="#googleInstructionModal">
                                    {{translate('see_credential_setup_instructions')}}
                                </button>
                            </div>

                            <form action="{{route('admin.business.configuration.third-party.social-login.update')}}"
                                  method="post">
                                @csrf
                                <input type="hidden" name="name" value="google_login">
                                <h6 class="mb-3">{{translate('status')}}</h6>
                                <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                    <div class="custom-radio">
                                        <input type="radio" id="google-active" name="status"
                                               value="1" {{($google['status']??0) == 1? 'checked' : ''}}>
                                        <label for="google-active">{{translate('active')}}</label>
                                    </div>
                                    <div class="custom-radio">
                                        <input type="radio" id="google-inactive" name="status"
                                               value="0" {{($google['status']??0) == 0? 'checked' : ''}}>
                                        <label for="google-inactive">{{translate('inactive')}}</label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                                <h5 class="d-flex align-items-center gap-2 text-capitalize">
                                    <img width="20" src="{{asset('public/assets/admin-module/img/icons/facebook.png')}}"
                                         alt="" class="dark-support">
                                    {{translate('facebook_login')}}
                                </h5>
                                <button type="button" class="text-capitalize btn btn-outline-primary"
                                        data-bs-toggle="modal" data-bs-target="#facebookInstructionModal">
                                    {{translate('see_credential_setup_instructions')}}
                                </button>
                            </div>

                            <form action="{{route('admin.business.configuration.third-party.social-login.update')}}"
                                  method="post">
                                @csrf
                                <input type="hidden" name="name" value="facebook_login">
                                <h6 class="mb-3">{{translate('status')}}</h6>
                                <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                    <div class="custom-radio">
                                        <input type="radio" value="1" id="senang_pay-active"
                                               name="status" {{($facebook['status']??0) == 1? 'checked' : ''}}>
                                        <label for="senang_pay-active">{{translate('active')}}</label>
                                    </div>
                                    <div class="custom-radio">
                                        <input type="radio" value="0" id="senang_pay-inactive"
                                               name="status"{{($facebook['status']??0) == 0? 'checked' : ''}}>
                                        <label for="senang_pay-inactive">{{translate('inactive')}}</label>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->

    <!-- Modal -->
    <div class="modal fade" id="googleInstructionModal" tabindex="-1" aria-labelledby="instructionModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-3">{{translate('instructions')}}</h5>
                    <ol class="d-flex text-dark flex-column gap-1">
                        <li>{{translate('Go_to_the_Credentials_page')}} (<a
                                href="https://console.cloud.google.com/apis/credentials"
                                target="_blank">{{translate('Click Here')}}</a>)
                        </li>
                        <li>{{translate('Click')}} <b>{{translate('Create Credentials')}}</b> >
                            <b>{{translate('OAuth Client ID')}}</b></li>
                        <li>{{translate('Select')}}
                            <b>{{translate('Web application')}}</b> {{translate('Type')}}</li>
                        <li>{{translate('Name your OAuth 2.0 client')}}</li>
                        <li>{{translate('Click')}} <b>{{translate('ADD URI')}}</b> {{translate('From')}}
                            <b>{{translate('Authorized redirect URIs')}}</b></li>
                        <li>{{translate('Provide the')}}
                            <code>{{translate('callback URI')}}</code> {{translate('from below and click create')}}
                        </li>
                        <li>{{translate('Press Submit')}}</li>
                        <li>{{translate('Copy')}} <b>{{translate('Client ID')}}</b>
                            & {{translate('Client Secret')}} {{translate('and  Paste in the input filed beside and')}}
                            <b>{{translate('Save')}}</b>.
                        </li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                            data-bs-dismiss="modal">{{translate('close')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="facebookInstructionModal" tabindex="-1" aria-labelledby="instructionModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="mb-3">{{translate('instructions')}}</h5>
                    <ol class="d-flex text-dark flex-column gap-1">
                        <li>{{translate('Go to the facebook developer page')}} (<a
                                href="https://developers.facebook.com/apps/"
                                target="_blank">{{translate('Click Here')}}</a>)
                        </li>
                        <li>{{translate('Go to')}}
                            <b>{{translate('Get Started')}}</b> {{translate('from Navbar')}}</li>
                        <li>{{translate('From Register tab press')}} <b>{{translate('Continue')}}</b>
                            <small>{{translate('(If needed)')}}</small></li>
                        <li>{{translate('Provide Primary Email and press')}}
                            <b>{{translate('Confirm Email')}}</b> <small>{{translate('(If needed)')}}</li>
                        <li>{{translate('In about section select')}}
                            <b>{{translate('Other')}}</b> {{translate('and press')}}
                            <b>{{translate('Complete Registration')}}</b></li>
                        <li><b>{{translate('Create App')}}</b> > {{translate('Select an app type and press')}}
                            <b>{{translate('Next')}}</b></li>
                        <li>{{translate('Complete the add details form and press')}}
                            <b>{{translate('Create App')}}</b></li>
                        <li>{{translate('Form')}}
                            <b>{{translate('Facebook Login')}}</b> {{translate('press')}}
                            <b>{{translate('Set Up')}}</b></li>
                        <li>{{translate('Select')}} <b>{{translate('Web')}}</b></li>
                        <li>{{translate('provide')}} <b>{{translate('Site URL')}}</b>
                            <small>{{translate('(Base URL of the site ex: https://example.com)')}}</small> >
                            <b>{{translate('save')}}</b></li>
                        <li>{{translate('Now go to')}}
                            <b>{{translate('Setting')}}</b> {{translate('form')}}
                            <b>{{translate('Facebook Login')}}</b> {{translate('(left sidebar)')}}</li>
                        <li>{{translate('Make sure to check')}}
                            <b>{{translate('Client OAuth Login')}}</b> {{translate('(must on)')}}</li>
                        <li>{{translate('Provide')}}
                            <code>{{translate('Valid OAuth Redirect URIs')}}</code> {{translate('from below and click')}}
                            <b>{{translate('Save Changes')}}</b></li>
                        <li>{{translate('Now go to')}} <b>{{translate('Setting')}}</b>
                            <small>{{translate('(from left sidebar)')}}</small> >
                            <b>{{translate('Basic')}}</b></li>
                        <li>{{translate('Fill the form and press')}} <b>{{translate('Save Changes')}}</b></li>
                        <li>{{translate('Now, copy')}} <b>{{translate('Client ID')}}</b> &
                            <b>{{translate('Client Secret')}}</b>, {{translate('paste in the input field below and')}}
                            <b>{{translate('Save')}}</b></li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"
                            data-bs-dismiss="modal">{{translate('close')}}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
