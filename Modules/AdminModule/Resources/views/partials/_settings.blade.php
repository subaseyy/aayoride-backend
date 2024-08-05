<div class="settings-sidebar">
    <div class="settings-toggle-icon">
        <i class="bi bi-gear-fill"></i>
    </div>
    <div class="settings-content">
        <h4>Settings</h4>
        <div class="switchers-wrap">
            <div class="switch-items">
                <div class="setting-box-wrap">
                    <div class="setting-box active light-mode">
                        <img src="{{asset("public/assets/admin-module/img/light-mode.png")}}" width="36px" alt="">
                    </div>
                    <h5>{{ translate('Light Mode') }}</h5>
                </div>
                <div class="setting-box-wrap">
                    <div class="setting-box dark-mode">
                        <img src="{{asset("public/assets/admin-module/img/dark-mode.png")}}" width="36px" alt="">
                    </div>
                    <h5>{{ translate('Dark Mode') }}</h5>
                </div>
                <div class="setting-box-wrap">
                    <div class="setting-box ltr-mode">
                        <img src="{{asset("public/assets/admin-module/img/ltr-icon.png")}}" alt="">
                    </div>
                    <h5>{{ translate('LTR') }}</h5>
                </div>
                <div class="setting-box-wrap">
                    <div class="setting-box rtl-mode">
                        <img src="{{asset("public/assets/admin-module/img/rtl-icon.png")}}" alt="">
                    </div>
                    <h5>{{ translate('RTL') }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
