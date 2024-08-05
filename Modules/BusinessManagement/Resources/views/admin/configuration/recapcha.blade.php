@extends('adminmodule::layouts.master')

@section('title', translate('Recaptcha'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('3rd_party')}}</h2>
            @include('businessmanagement::admin.configuration.partials._third_party_inline_menu')

            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                        <h5 class="text-capitalize">{{translate('google_recaptcha_information')}}</h5>
                        <a target="_blank" href="https://www.google.com/recaptcha/admin/create"
                           class="btn btn-outline-primary text-capitalize">
                            {{translate('credential_setup_page')}}
                        </a>
                    </div>


                    <form action="{{route('admin.business.configuration.third-party.recaptcha.update')}}" method="post"
                          id="recaptcha_form">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">{{translate('status')}}</h6>
                                <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                    <div class="custom-radio">
                                        <input type="radio" id="senang_pay-active" name="status"
                                               value="1" {{($setting['status'] ?? 0) == 1? 'checked' : ''}}>
                                        <label for="senang_pay-active">{{translate('active')}}</label>
                                    </div>
                                    <div class="custom-radio">
                                        <input type="radio" id="senang_pay-inactive" name="status"
                                               value="0" {{($setting['status'] ?? 0) == 0? 'checked' : ''}}>
                                        <label for="senang_pay-inactive">{{translate('inactive')}}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 align-it">
                                <div class="mb-4">
                                    <label for="site_key" class="mb-2">{{translate('site_key')}}</label>
                                    <input required type="text" name="site_key" value="{{$setting['site_key']??''}}"
                                           class="form-control" id="site_key" placeholder="Site Key">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="secret_key" class="mb-2">{{translate('secret_key')}}</label>
                                    <input required type="text" name="secret_key" value="{{$setting['secret_key']??''}}"
                                           class="form-control" id="secret_key" placeholder="Secret Key">
                                </div>
                            </div>
                            <div class="col-12">
                                <h5 class="mb-3">Instructions</h5>
                                <ol class="d-flex flex-column text-dark gap-1">
                                    <li>To get OAuth Client ID Go to the Credentials page ( <a href="#" target="_blank"><b>Click
                                                Here</b></a> )
                                    </li>
                                    <li>Click Create Credentials &gt; <b>OAuth Client ID</b></li>
                                    <li>Select <b>Web application</b> Type</li>
                                    <li>Name your OAuth 2.0 client</li>
                                    <li>Click <b>ADD URI</b> From <b>Authorized redirect URIs</b></li>
                                    <li>Provide the callback URI from below and click create</li>
                                    <li>Press Submit</li>
                                    <li>Copy Clientd ID &amp; Client Secrete and Paste in the input filed beside and
                                        Save.
                                    </li>
                                </ol>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">{{translate('save')}}</button>
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

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#recaptcha_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

@endpush
