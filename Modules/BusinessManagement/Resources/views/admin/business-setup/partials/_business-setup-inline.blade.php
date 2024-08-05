<ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
    <li class="nav-item text-capitalize">
        <a href="{{route('admin.business.setup.info.index')}}"
           class="nav-link {{Request::is('admin/business/setup/info') ? 'active' : ''}}">{{translate('business_info')}}</a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.business.setup.driver.index')}}"
           class="nav-link {{Request::is('admin/business/setup/driver') ? 'active' : ''}}">{{translate('driver')}}</a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.business.setup.customer.index')}}"
           class="nav-link {{Request::is('admin/business/setup/customer') ? 'active' : ''}}">{{translate('customer')}}</a>
    </li>
    <li class="nav-item text-capitalize">
        <a href="{{route('admin.business.setup.trip-fare.penalty')}}"
           class="nav-link {{Request::is('admin/business/setup/trip-fare/penalty') ? 'active' : ''}}">{{translate('fare_&_penalty_settings')}}</a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.business.setup.trip-fare.trips')}}"
           class="nav-link {{Request::is('admin/business/setup/trip-fare/trips') ? 'active' : ''}}">{{translate('trips')}}</a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.business.setup.info.settings')}}"
           class="nav-link {{Request::is('admin/business/setup/info/settings') ? 'active' : ''}}">{{translate('settings')}}</a>
    </li>
</ul>
