<footer>
    @php($logo = getSession('header_logo'))
    @php($email = getSession('business_contact_email'))
    @php($contactNumber = getSession('business_contact_phone'))
    @php($businessAddress = getSession('business_address'))
    @php($businessName = getSession('business_name'))
    @php($cta = getSession('cta'))
    @php($links = \Modules\BusinessManagement\Entities\SocialLink::where(['is_active'=>1])->orderBy('name','asc')->get())
    <div class="footer-top">
        <div class="container">
            <div class="footer__wrapper">
                <div class="footer__wrapper-widget">
                    <div class="cont">
                        <a href="{{ route('index') }}" class="logo">
                            <img src="{{ $logo ? asset("storage/app/public/business/".$logo) : asset('public/landing-page/assets/img/logo.png') }}" alt="logo">
                        </a>
                        <p>
                            {{translate('Connect with our social media and other sites to keep up to date')}}
                        </p>
                        <ul class="social-icons">
                            @foreach($links as $link)
                                @if($link->name == "facebook")
                                <li>
                                    <a href="{{$link->link}}" target="_blank">
                                        <img src="{{ asset('public/landing-page/assets/img/footer/facebook.png') }}" alt="img">
                                    </a>
                                </li>
                                @elseif($link->name == "instagram")
                                <li>
                                    <a href="{{$link->link}}"  target="_blank">
                                        <img src="{{ asset('public/landing-page/assets/img/footer/instagram.png') }}" alt="img">
                                    </a>
                                </li>
                                @elseif($link->name == "twitter")
                                <li>
                                    <a href="{{$link->link}}"  target="_blank">
                                        <img src="{{ asset('public/landing-page/assets/img/footer/twitter.png') }}" alt="img">
                                    </a>
                                </li>
                                @elseif($link->name == "linkedin")
                                <li>
                                    <a href="{{$link->link}}"  target="_blank">
                                        <img src="{{ asset('public/landing-page/assets/img/footer/linkedin.png') }}" alt="img">
                                    </a>
                                </li>
                                @endif
                            @endforeach

                        </ul>
                        <div class="app-btns">
                            <div class="me-xl-4">
                                <h6 class="text-white mb-3 font-regular">User App</h6>
                                <div class="d-flex gap-3 flex-column">
                                    <a target="_blank"  type="button" href="{{ $cta && $cta['app_store']['user_download_link'] ? $cta['app_store']['user_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/app-store.png"
                                             class="w-115px" alt="">
                                    </a>
                                    <a target="_blank" type="button" href="{{ $cta && $cta['play_store']['user_download_link'] ? $cta['play_store']['user_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/play-store.png"
                                             class="w-115px" alt="">
                                    </a>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-white mb-3 font-regular">Driver App</h6>
                                <div class="d-flex gap-3 flex-column">
                                    <a target="_blank" type="button" href="{{ $cta && $cta['app_store']['driver_download_link'] ? $cta['app_store']['driver_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/app-store.png"
                                             class="w-115px" alt="">
                                    </a>
                                    <a target="_blank" type="button" href="{{ $cta && $cta['play_store']['driver_download_link'] ? $cta['play_store']['driver_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/play-store.png"
                                             class="w-115px" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer__wrapper-widget">
                    <ul class="footer__wrapper-link">
                        <li>
                            <a href="{{ route('index') }}">{{ translate('Home') }}</a>
                        </li>
                        <li>
                            <a href="{{route('about-us')}}">{{ translate('About Us') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('contact-us') }}">{{ translate('Contact Us') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}">{{ translate('Privacy Policy') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('terms') }}">{{ translate('Terms & Condition') }}</a>
                        </li>
                    </ul>
                </div>
                <div class="footer__wrapper-widget">
                    <div class="footer__wrapper-contact">
                        <img class="icon" src="{{ asset('public/landing-page') }}/assets/img/footer/mail.png" alt="footer">
                        <h6>
                            {{ translate('Send us Mail') }}
                        </h6>
                        <a href="Mailto:{{  $email ? $email : "contact@example.com" }}">{{  $email ? $email : "contact@example.com" }}</a>
                    </div>
                </div>
                <div class="footer__wrapper-widget">
                    <div class="footer__wrapper-contact">
                        <img class="icon" src="{{ asset('public/landing-page') }}/assets/img/footer/tel.png" alt="footer">
                        <h6>
                            {{ translate('Contact Us') }}
                        </h6>
                        <div>
                            <a href="Tel:{{ $contactNumber ? $contactNumber : "+90-327-539" }}">{{ $contactNumber ? $contactNumber : "+90-327-539" }}</a>
                        </div>
                        <a href="Mailto:support@example.com">{{ $email ? $email : "support@6amtech.com"}}</a>
                    </div>
                </div>
                <div class="footer__wrapper-widget">
                    <div class="footer__wrapper-contact">
                        <img class="icon" src="{{ asset('public/landing-page') }}/assets/img/footer/pin.png" alt="footer">
                        <h6>
                            {{ translate('Send us Mail') }}
                        </h6>
                        <div>
                            {{ $businessAddress ? $businessAddress : "510 Kampong Bahru Rd Singapore 099446" }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom text-center py-3">
        {{getSession('copyright_text')}}
    </div>
</footer>
