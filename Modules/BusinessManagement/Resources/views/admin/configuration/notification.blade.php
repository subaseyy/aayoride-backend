@extends('adminmodule::layouts.master')

@section('title', translate('notification'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('notification_setup')}}</h2>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table class="table table-borderless align-middle">
                            <thead class="table-light align-middle">
                            <tr>
                                <th>{{translate('notifications')}}</th>
                                <th>{{translate('push_notification')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notificationSettings as $notification)
                                <tr>
                                    <td class="text-capitalize">{{translate($notification->name)}}</td>
                                    <td>
                                        <label class="switcher">
                                            <input class="switcher_input"
                                                   data-type="push" data-id="{{$notification->id}}"
                                                   type="checkbox" {{$notification->push == 1? 'checked' : ''}}>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="text-primary mb-4 text-uppercase">{{translate('firebase_push_notification_setup')}}</h5>
                    <form action="{{route('admin.business.configuration.notification.store')}}"
                          method="post" id="server_key_form">
                        @csrf
                        <div class="mb-4">
                            <label for="server_key"
                                   class="mb-2">{{translate('service_account_content')}}<span
                                    class="text-danger">*</span></label>
                            <textarea name="server_key" id="server_key" placeholder="Type Here..." class="form-control"
                                      cols="30"
                                      rows="10" required>{{($settings?->value)}}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-3">
                                <button class="btn btn-primary text-uppercase"
                                        type="submit">{{ translate('submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary mb-4 text-uppercase">{{translate('firebase_push_notification_setup')}}</h5>
                    <form action="{{route('admin.business.configuration.notification.push-store')}}"
                          method="post" id="notification_setup_form">
                        @csrf
                        <div class="row">
                            @forelse($notifications as $notification)
                                <div class="col-lg-6">
                                    <div class="mb-30">
                                        <div class="d-flex justify-content-between gap-3 align-items-center mb-3">
                                            <label
                                                for="trip_req_message">{{translate($notification['name'])}}</label>
                                        </div>
                                        <textarea name="notification[{{$notification->name}}][value]"
                                                  id="trip_req_message" rows="4" class="form-control"
                                                  placeholder="Type Here ...">{{$notification?->value}}</textarea>
                                    </div>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary" type="submit">{{translate('submit')}}</button>
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


        $('#notification_setup_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });

        $('#server_key_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });

        $('.switcher_input').on('click', function () {
            updateSettings(this)
        })

        function updateSettings(obj) {
            $.ajax({
                url: '{{route('admin.business.configuration.notification.notification-settings')}}',
                _method: 'PUT',
                data: {
                    id: $(obj).data('id'),
                    type: $(obj).data('type'),
                    status: ($(obj).prop("checked")) === true ? 1 : 0
                },
                beforeSend: function () {
                    $('.preloader').removeClass('d-none');
                },
                success: function (d) {
                    $('.preloader').addClass('d-none');
                    toastr.success("{{translate('status_successfully_changed')}}");
                },
                error: function () {
                    $('.preloader').addClass('d-none');
                    toastr.error("{{translate('status_change_failed')}}");

                }
            });
        }
    </script>
@endpush
