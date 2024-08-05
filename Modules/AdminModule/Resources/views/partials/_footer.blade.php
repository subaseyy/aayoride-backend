<footer class="footer mt-auto">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 d-flex justify-content-center justify-content-md-start mb-2 mb-md-0">
                {{getSession('copyright_text')}}
            </div>
            <div class="col-md-8 d-flex justify-content-center justify-content-md-end">
                <ul class="footer-menu justify-content-center">
                    <li>
                        <a href="{{route('admin.business.setup.info.index')}}" class="text-capitalize">
                            {{ translate('business_setup')}}
                            <i class="bi bi-gear-fill"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.settings')}}">
                            {{ translate('profile')}}
                            <i class="bi bi-person-fill"></i>
                        </a>
                    </li>
                    <li>
                        <a href="{{route('admin.dashboard')}}">
                            {{ translate('home')}}
                            <i class="bi bi-house-door-fill"></i>
                        </a>
                    </li>
                    <li>
                        <span
                            class="badge badge-primary fs-12">{{ translate('app_version')}} <span>{{env('SOFTWARE_VERSION')}}</span></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
