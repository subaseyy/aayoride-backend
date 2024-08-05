<header>
    @php($logo = getSession('header_logo'))
    <!-- Header Bottom -->
    <div class="navbar-bottom">
        <div class="container">
            <div class="navbar-bottom-wrapper">
                <a href="{{route('index')}}" class="logo">
                    <img src="{{ $logo ? asset("storage/app/public/business/".$logo) : asset('public/landing-page/assets/img/logo.png') }}" alt="">
                </a>
                <ul class="menu me-lg-4">
                    <li>
                        <a href="{{route('index')}}" class="{{Request::is('/')? 'active' :''}}"><span>Home</span></a>
                    </li>
                    <li>
                        <a href="{{route('privacy')}}" class="{{Request::is('privacy') ? 'active' :''}}"><span>{{ translate('Privacy Policy') }}</span></a>
                    </li>
                    <li>
                        <a href="{{route('terms')}}" class="{{Request::is('terms')? 'active' :''}}"><span>{{ translate('Terms & Condition') }}</span></a>
                    </li>
                    <li>
                        <a href="{{route('about-us')}}" class="{{Request::is('about-us')? 'active' :''}}"><span>{{ translate('About Us') }}</span></a>
                    </li>
                    <li class="d-sm-none">
                        <a href="{{route('contact-us')}}" class="cmn--btn px-4 w-unset text-white d-inline-flex {{Request::is('contact-us')? 'active' :''}}"><span>Contact
                                Us</span></a>
                    </li>
                </ul>
                <div class="nav-toggle d-lg-none ms-auto me-2 me-sm-4">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <a href="{{route('contact-us')}}" class="cmn--btn d-none d-sm-block {{Request::is('contact-us')? 'active' :''}}">{{ translate('Contact Us') }}</a>
            </div>
        </div>
    </div>
    <!-- Header Bottom -->
</header>
