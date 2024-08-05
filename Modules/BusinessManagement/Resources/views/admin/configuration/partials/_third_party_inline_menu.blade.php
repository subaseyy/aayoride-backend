<div class="mb-3">
    <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
        <li class="nav-item">
            <a href="{{route('admin.business.configuration.third-party.payment-method.index')}}" class="text-capitalize nav-link
                {{Request::is('admin/business/configuration/third-party/payment-method') ? 'active' : ''}}
            ">{{translate('payment_methods')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.business.configuration.third-party.sms-gateway.index')}}" class="text-capitalize nav-link
                {{Request::is('admin/business/configuration/third-party/sms-gateway') ? 'active' : ''}}
            ">{{translate('SMS_gateways')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.business.configuration.third-party.email-config.index')}}" class="text-capitalize nav-link
                {{Request::is('admin/business/configuration/third-party/email-config') ? 'active' : ''}}
            ">{{translate('email_config')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.business.configuration.third-party.google-map.index')}}" class="text-capitalize nav-link
                {{Request::is('admin/business/configuration/third-party/google-map') ? 'active' : ''}}
            ">{{translate('google_map_API')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.business.configuration.third-party.recaptcha.index')}}" class="text-capitalize nav-link
                {{Request::is('admin/business/configuration/third-party/recaptcha') ? 'active' : ''}}
            ">{{translate('reCaptcha')}}</a>
        </li>
    </ul>
</div>
