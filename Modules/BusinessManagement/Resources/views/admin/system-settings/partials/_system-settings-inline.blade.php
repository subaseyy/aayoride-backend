<ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
    <li class="nav-item text-capitalize">
        <a href="{{route('admin.business.environment-setup.index')}}" class="nav-link {{Request::is('admin/business/environment-setup') ? 'active' : ''}}">{{translate('environment_setup')}}</a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.business.clean-database.index')}}" class="nav-link {{Request::is('admin/business/clean-database') ? 'active' : ''}}">{{translate('clean_database')}}</a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.business.languages.index')}}" class="nav-link {{Request::is('admin/business/languages') ? 'active' : ''}}">{{translate('languages')}}</a>
    </li>

</ul>
