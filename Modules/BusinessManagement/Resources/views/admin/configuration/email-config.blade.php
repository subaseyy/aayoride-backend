@extends('adminmodule::layouts.master')

@section('title', translate('Email_Config'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('3rd_party')}}</h2>
            @include('businessmanagement::admin.configuration.partials._third_party_inline_menu')

            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary text-uppercase mb-4">{{translate('mail_configuration_information')}}</h5>

                    <form action="{{route('admin.business.configuration.third-party.email-config.update')}}"
                          method="post" id="email_config_form">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="mailer_name" class="mb-2">{{translate('mailer_name')}}</label>
                                    <input required type="text" name="mailer_name"
                                           value="{{$setting['mailer_name'] ?? ''}}"
                                           class="form-control" id="mailer_name"
                                           placeholder="{{translate('Ex: John Doe')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="host" class="mb-2">{{translate('host')}}</label>
                                    <input required type="text" name="host"
                                           value="{{$setting['host'] ?? ''}}"
                                           class="form-control" id="host"
                                           placeholder="{{translate('email.example.com')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="driver" class="mb-2">{{translate('driver')}}</label>
                                    <input required type="text" name="driver"
                                           value="{{$setting['driver'] ?? ''}}"
                                           class="form-control" id="driver"
                                           placeholder="{{translate('Ex: SMTP')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="port" class="mb-2">{{translate('port')}}</label>
                                    <input required type="text" name="port"
                                           value="{{$setting['port'] ?? ''}}"
                                           class="form-control" id="port" placeholder="{{translate('Ex: Port')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="username" class="mb-2">{{translate('username')}}</label>
                                    <input required type="text" name="username"
                                           value="{{$setting['username'] ?? ''}}"
                                           class="form-control" id="username"
                                           placeholder="{{translate('demo@example.com')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="email_id" class="mb-2">{{translate('email_ID')}}</label>
                                    <input required type="text" name="email_id"
                                           value="{{$setting['email_id'] ?? ''}}"
                                           class="form-control" id="email_id"
                                           placeholder="{{translate('demo@example.com')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="encryption" class="mb-2">{{translate('encryption')}}</label>
                                    <input required type="text" name="encryption"
                                           value="{{$setting['encryption']  ?? ''}}"
                                           class="form-control" id="encryption" placeholder="{{translate('tls')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="password" class="mb-2">{{translate('password')}}</label>
                                    <input required type="text" name="password"
                                           value="{{$setting['password'] ?? ''}}"
                                           class="form-control" id="password" placeholder="Ex: 12345678">
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
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


        $('#email_config_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

@endpush

