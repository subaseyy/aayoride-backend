@extends('adminmodule::layouts.master')

@section('title', translate('Google_Map_API'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('3rd_party')}}</h2>
            @include('businessmanagement::admin.configuration.partials._third_party_inline_menu')

            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary text-uppercase mb-4">{{translate('google_map_api_setup')}}</h5>

                    <div
                        class="media align-items-center gap-3 px-3 py-2 rounded border border-primary-light border-start-5 mb-30">
                        <i class="bi bi-info-circle-fill fs-20 text-primary"></i>
                        <p class="media-body"><strong>{{translate('NB')}}
                                :</strong> {{translate('Client key should have enable map javascript api and you can restrict it with http refer')}}
                            .
                            {{translate('Server key should have enable place api key and you can restrict it with ip')}}
                            . {{translate('You can use same api for both field without any restrictions')}}.</p>
                    </div>

                    <form action="{{route('admin.business.configuration.third-party.google-map.update')}}" method="post"
                          id="map_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="map_api_key" class="mb-2">{{translate('map_API_key')}}
                                        ({{translate('client')}})</label>
                                    <input required type="text" name="map_api_key"
                                           value="{{$setting['map_api_key']??''}}" class="form-control" id="map_api_key"
                                           placeholder="Map API Key">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="map_api_key_server" class="mb-2">{{translate('map_API_key')}}
                                        ({{translate('server')}})</label>
                                    <input required type="text" name="map_api_key_server"
                                           value="{{$setting['map_api_key_server']??''}}" class="form-control"
                                           id="map_api_key_server" placeholder="Map API Key">
                                </div>
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
    <!-- End Main Content -->
@endsection


@push('script')

    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan


        $('#map_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

@endpush
