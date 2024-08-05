@extends('adminmodule::layouts.master')

@section('title', translate('Environment_Setup'))

@push('css_or_js')
@endpush

@section('content')

    <!-- Main Content -->
    <div class="content container-fluid">
        <!-- Page Title -->
        <h2 class="fs-22 mb-4 text-capitalize">{{translate('system_settings')}}</h2>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        <div class="mb-3">
            @include('businessmanagement::admin.system-settings.partials._system-settings-inline')
        </div>
        <!-- End Inlile Menu -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-medium d-flex align-items-center gap-2 text-capitalize">
                            <i class="bi bi-briefcase-fill"></i>
                            {{translate('environment_information')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{route('admin.business.environment-setup.update')}}" method="post" id="env_form">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-4">
                                        <label class="title-color d-flex mb-2">{{translate('APP_NAME')}}</label>
                                        <input type="text" value="{{ env('APP_NAME') }}"
                                               name="app_name" class="form-control"
                                               placeholder="Ex : DriveMond" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-4">
                                        <label class="title-color d-flex mb-2">{{translate('APP_DEBUG')}}</label>
                                        <select name="app_debug" class="form-control js-select2-custom">
                                            <option value="true" {{env('APP_DEBUG')==true?'selected':''}}>
                                                {{translate('true')}}
                                            </option>
                                            <option value="false" {{env('APP_DEBUG')==false?'selected':''}}>
                                                {{translate('false')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="title-color d-flex mb-2">{{translate('APP_MODE')}}</label>
                                        <select name="app_mode" class="form-control js-select2-custom">
                                            <option value="live" {{env('APP_MODE')=='live'?'selected':''}}>
                                                {{translate('live')}}
                                            </option>
                                            <option value="demo" {{env('APP_MODE')=='demo'?'selected':''}}>
                                                {{translate('demo')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="title-color d-flex mb-2">{{translate('APP_URL')}}</label>
                                        <input type="text" value="{{ env('APP_URL') }}"
                                               name="app_url" class="form-control"
                                               placeholder="Ex : http://localhost" required disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-4">
                                        <label
                                            class="title-color d-flex mb-2">{{translate('DB_CONNECTION')}}</label>
                                        <input type="text"
                                               value="{{ env('APP_MODE') != 'demo' ? env('DB_CONNECTION') : '---' }}"
                                               name="db_connection" class="form-control"
                                               placeholder="Ex : mysql" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="title-color d-flex mb-2">{{translate('DB_HOST')}}</label>
                                        <input type="text"
                                               value="{{ env('APP_MODE') != 'demo' ? env('DB_HOST') : '---' }}"
                                               name="db_host" class="form-control"
                                               placeholder="Ex : http://localhost/" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="title-color d-flex mb-2">{{translate('DB_PORT')}}</label>
                                        <input type="text"
                                               value="{{ env('APP_MODE') != 'demo' ? env('DB_PORT') : '---' }}"
                                               name="db_port" class="form-control"
                                               placeholder="Ex : 3306" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group mb-4">
                                        <label class="title-color d-flex mb-2">{{translate('DB_DATABASE')}}</label>
                                        <input type="text"
                                               value="{{ env('APP_MODE') != 'demo' ? env('DB_DATABASE') : '---' }}"
                                               name="db_database" class="form-control"
                                               placeholder="Ex : demo_db" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="title-color d-flex mb-2">{{translate('DB_USERNAME')}}</label>
                                        <input type="text"
                                               value="{{ env('APP_MODE') != 'demo' ? env('DB_USERNAME') : '---' }}"
                                               name="db_username" class="form-control"
                                               placeholder="Ex : root" required disabled>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="form-group">
                                        <label class="title-color d-flex mb-2">{{translate('DB_PASSWORD')}}</label>
                                        <input type="text"
                                               value="{{ env('APP_MODE') != 'demo' ? env('DB_PASSWORD') : '---' }}"
                                               name="db_password" class="form-control"
                                               placeholder="Ex : password" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group mb-4">
                                        <label
                                            class="title-color d-flex mb-2">{{translate('BUYER_USERNAME')}}</label>

                                        <input type="text" value="{{ env('BUYER_USERNAME') }}" class="form-control"
                                               disabled>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group" id="purchase_code_div">
                                        <label
                                            class="title-color d-flex mb-2">{{translate('PURCHASE_CODE')}}</label>
                                        <div class="input-icons">
                                            <input type="password" value="{{ env('PURCHASE_CODE') }}"
                                                   class="form-control" id="purchase_code" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end flex-wrap gap-10">
                                <button type="submit" class="btn btn-primary px-4">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
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

        $('#env_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

@endpush
