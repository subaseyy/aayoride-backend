@extends('adminmodule::layouts.master')

@section('title', translate('Business_Info'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('business_management')}}</h2>

            <div class="mb-3">
                @include('businessmanagement::admin.business-setup.partials._business-setup-inline')
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card">
                        <form action="{{route('admin.business.setup.driver.store')}}?type=loyalty_point"
                              id="loyalty_point_form" method="post">
                            @csrf
                            <div class="card-header">
                                <h5 class="d-flex align-items-center gap-2 text-capitalize">
                                    <i class="bi bi-person-fill-gear"></i>
                                    {{translate('loyalty_point')}}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       title="{{translate('configure_loyalty_point')}}"></i>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <h6 class="fw-medium text-capitalize text-capitalize">{{translate('driver_can_earn_loyalty_point')}}</h6>
                                        <label class="switcher">
                                            <input class="switcher_input"
                                                   type="checkbox"
                                                   name="loyalty_points[status]"
                                                   id="loyalty_point_switch"
                                                {{ ($settings->firstWhere('key_name', 'loyalty_points')?->value['status']) == 1 ? 'checked' : ''}}
                                            >
                                            <span class="switcher_control"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="equivalent_points"
                                           class="mb-2">{{getCurrencyFormat(1). ' ' . translate('equivalent_to_points')}}</label>
                                    <input type="tel" name="loyalty_points[value]" id="equivalent_points"
                                           class="form-control" required pattern="[1-9][0-9]{0,200}"
                                           title="Please input integer value. Ex:1,2,22,10"
                                           placeholder="{{translate('Ex: 2')}}"
                                           value="{{$settings->where('key_name', 'loyalty_points')->first()?->value['points']}}">
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit"
                                            class="btn btn-primary text-uppercase">{{translate('submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="d-flex align-items-center gap-2 text-capitalize">
                                <i class="bi bi-person-fill-gear"></i>
                                {{translate('driver_review')}}
                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                   title="{{translate('configure_driver_review')}}"></i>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h6 class="fw-medium d-flex align-items-center fw-medium gap-2 text-capitalize">
                                    {{translate('driver_can_review_customer')}}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       title="{{translate('configure_that_driver_can_give_review_to_customer_or_not')}}">
                                    </i>
                                </h6>
                                <label class="switcher">
                                    <input class="switcher_input" name="{{DRIVER_REVIEW}}" type="checkbox"
                                           data-type="{{DRIVER_SETTINGS}}" id="driverReview"
                                        {{$settings->firstWhere('key_name', DRIVER_REVIEW)?->value == 1? 'checked' : ''}}
                                    >
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header">
                            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-between">
                                <h5 class="d-flex align-items-center gap-2 text-capitalize">
                                    <i class="bi bi-person-fill-gear"></i>
                                    {{translate('Driver Level')}}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip"
                                       title="{{translate('Enable this, customers will gain access to level-specific features and benefits')}}"></i>
                                </h5>
                                <a href="{{ route("admin.driver.level.index") }}" class="text-primary fw-semibold d-flex gap-2 align-items-center">
                                    {{translate('Go to settings')}}
                                    <i class="bi bi-arrow-right"></i>
                                </a>
{{--                                <a href="{{ route("admin.driver.level.create") }}" class="text-primary fw-semibold d-flex gap-2 align-items-center">--}}
{{--                                    {{translate('Go to settings')}}--}}
{{--                                    <i class="bi bi-arrow-right"></i>--}}
{{--                                </a>--}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h6 class="fw-medium d-flex align-items-center fw-medium gap-2 text-capitalize">
                                    {{translate('you Can ON/OFF level feature')}}
                                </h6>
                                <label class="switcher">
                                    <input class="switcher_input update-business-setting" id="customerLevel"
                                           name="{{DRIVER_LEVEL}}"
                                           type="checkbox"
                                           data-name="{{DRIVER_LEVEL}}"
                                           data-type="{{DRIVER_SETTINGS}}"
                                           data-url="{{route('admin.business.setup.update-business-setting')}}"
                                           data-icon="{{($settings->firstWhere('key_name', DRIVER_LEVEL)->value?? 0) == 0 ? asset('public/assets/admin-module/img/level-up-on.png') : asset('public/assets/admin-module/img/level-up-off.png')}}"
                                           data-title="{{($settings->firstWhere('key_name', DRIVER_LEVEL)->value?? 0) == 0?translate('By Turning ON Level Feature') .'?' : translate('By Turning OFF Level Feature').'?'}}"
                                           data-sub-title="{{($settings->firstWhere('key_name', DRIVER_LEVEL)->value?? 0) == 0?translate('If you turn ON level feature, customer will see this feature on app.') : translate('If you turning off customer level feature, please do it at the beginning stage of business. Because once driver use this feature & you will off this feature they will be confused or worried about it.')}}"
                                        {{($settings->firstWhere('key_name', DRIVER_LEVEL)->value?? 0) == 1? 'checked' : ''}}
                                    >
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/business-management/business-setup/driver.js') }}"></script>

    <script>
        "use strict";
        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#driverReview').on('change', function () {
            let url = '{{route('admin.business.setup.update-business-setting')}}';
            updateBusinessSetting(this, url)
        })

        function updateBusinessSetting(obj, url) {
            if (!permission) {
                toastr.error('{{ translate('you_donot_have_enough_permission_to_update_this_settings') }}');

                let checked = $(obj).prop("checked");
                let status = checked === true ? 1 : 0;
                if (status === 1) {
                    $('#' + obj.id).prop('checked', false)

                } else if (status === 0) {
                    $('#' + obj.id).prop('checked', true)
                }
                return;
            }

            let value = $(obj).prop('checked') === true ? 1 : 0;
            let name = $(obj).attr('name');
            let type = $(obj).data('type');
            let checked = $(obj).prop("checked");
            let status = checked === true ? 1 : 0;


            Swal.fire({
                title: '{{translate('are_you_sure')}}?',
                text: '{{translate('want_to_change_status')}}',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--bs-primary)',
                cancelButtonColor: 'default',
                cancelButtonText: '{{ translate("no")}}',
                confirmButtonText: '{{ translate("yes")}}',
                reverseButtons: true
            }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: url,
                            data: {value: value, name: name, type: type},
                            success: function () {
                                toastr.success("{{translate('status_changed_successfully')}}");
                            },
                            error: function () {
                                if (status === 1) {
                                    $('#' + obj.id).prop('checked', false)
                                } else if (status === 0) {
                                    $('#' + obj.id).prop('checked', true)
                                }
                                toastr.error("{{translate('status_change_failed')}}");
                            }
                        });
                    } else if (result.dismiss === 'cancel') {

                        if (status === 1) {
                            $('#' + obj.id).prop('checked', false)
                        } else if (status === 0) {
                            $('#' + obj.id).prop('checked', true)
                        }
                        toastr.info("{{ translate("status_is_not_changed")}}");
                    }
                }
            )
        }
    </script>

    <script>
        $('#loyalty_point_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_donot_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>
@endpush
