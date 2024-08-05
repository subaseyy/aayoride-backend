{{--Custom Modal Start--}}
<div class="modal fade" id="customModal">
    <div class="modal-dialog status-warning-modal">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-toggle="modal">
                </button>
            </div>
            <div class="modal-body pb-5 pt-0">
                <div class="max-349 mx-auto">
                    <div>
                        <div class="text-center">
                            <img alt="" class="mb-4" id="icon"
                                 src="{{asset('public/assets/admin-module/img/svg/blocked_customer.svg')}}">
                            <h5 class="modal-title mb-3" id="title">{{translate("Are you sure?")}}</h5>
                        </div>
                        <div class="text-center mb-4 pb-2">
                            <p id="subTitle">{{translate("Want to change status")}}</p>
                        </div>
                    </div>
                    <div class="btn--container justify-content-center">
                        <button type="button" class="btn btn-primary min-w-120"
                                id="modalConfirmBtn">{{translate('Ok')}}</button>
                        <button type="button" class="btn btn--cancel min-w-120" id="modalCancelBtn">
                            {{translate('Cancel')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--Custom Modal End--}}

{{--Customer Level Setting Warning Modal Start--}}
<div class="modal fade" id="customerLevelSettingWarningModal" >
    <div class="modal-dialog status-warning-modal">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-toggle="modal">
                </button>
            </div>
            <div class="modal-body pb-5 pt-0">
                <div class="max-349 mx-auto">
                    <div>
                        <div class="text-center">
                            <img alt="" class="mb-4" id="icon"
                                 src="{{asset('public/assets/admin-module/img/warning.png')}}">
                            <h5 class="modal-title mb-3" id="title">{{translate("This feature is turned off from settings")}}</h5>
                        </div>
                        <div class="text-center mb-4 pb-2">
                            <p id="subTitle">{{translate("Customer level feature is currently turned off from business settings. If you want to active all the level for customers in the app, turn on the feature from the settings")}}</p>
                        </div>
                    </div>
                    <div class="btn--container justify-content-center">
                        <button type="button" class="btn btn--cancel min-w-120" id="modalCancelBtn" data-bs-toggle="modal">
                            {{translate('Not Now')}}
                        </button>
                        <a href="#" class="btn btn-primary min-w-120"
                                id="customerLevelSettingWarningModalConfirmBtn">{{translate('Go to Settings')}}</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--Customer Level Setting Warning Modal End--}}
{{--Driver Level Setting Warning Modal Start--}}
<div class="modal fade" id="driverLevelSettingWarningModal" >
    <div class="modal-dialog status-warning-modal">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-toggle="modal">
                </button>
            </div>
            <div class="modal-body pb-5 pt-0">
                <div class="max-349 mx-auto">
                    <div>
                        <div class="text-center">
                            <img alt="" class="mb-4" id="icon"
                                 src="{{asset('public/assets/admin-module/img/warning.png')}}">
                            <h5 class="modal-title mb-3" id="title">{{translate("This feature is turned off from settings")}}</h5>
                        </div>
                        <div class="text-center mb-4 pb-2">
                            <p id="subTitle">{{translate("Driver level feature is currently turned off from business settings. If you want to active all the level for drivers in the app, turn on the feature from the settings")}}</p>
                        </div>
                    </div>
                    <div class="btn--container justify-content-center">
                        <button type="button" class="btn btn--cancel min-w-120" id="modalCancelBtn" data-bs-toggle="modal">
                            {{translate('Not Now')}}
                        </button>
                        <a href="#" class="btn btn-primary min-w-120"
                                id="driverLevelSettingWarningModalConfirmBtn">{{translate('Go to Settings')}}</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--Driver Level Setting Warning Modal End--}}
