@extends('landing-page.layouts.master')
@section('title', 'Home')


@section('content')
    <!-- Banner Section Start -->
    <section class="banner-section">
        <div class="container">
            <div class="banner-wrapper justify-content-between bg__img wow animate__fadeInDown"
                 data-img="{{ $introSectionImage?->value && $introSectionImage?->value['background_image'] ? asset('storage/app/public/business/landing-pages/intro-section/'.$introSectionImage?->value['background_image']) : asset('public/landing-page/assets/img/banner/banner-bg.png') }}">
                <div class="banner-content">
                    <h1 class="title">{{ $introSection?->value && $introSection?->value['title'] ? translate($introSection?->value['title']) : translate("It’s Time to Change The Riding Experience") }}</h1>
                    <p class="txt">{{ $introSection?->value && $introSection?->value['sub_title'] ? translate($introSection?->value['sub_title']) : translate("Embrace the future today and explore the amazing features that make "). (($business_name && $business_name['value']) ? $business_name['value'] : "DriveMond") .translate("the smart, sustainable, and efficient ride sharing & delivery solution.") }}
                    </p>
                    <div class="app--btns d-flex flex-wrap">
                        <div class="dropdown">
                            <a href="#" class="cmn--btn"
                               data-bs-toggle="dropdown">{{translate('Download User App')}}</a>
                            <div class="dropdown-menu dropdown-button-menu">
                                <ul>
                                    <li>
                                        <a href="{{ $cta?->value && $cta?->value['play_store']['user_download_link'] ? $cta?->value['play_store']['user_download_link'] : "" }}">
                                            <img src="{{ asset('public/landing-page') }}/assets/img/play-fav.png"
                                                 alt="">
                                            <span>{{translate('Play Store')}}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ $cta?->value && $cta?->value['app_store']['user_download_link'] ? $cta?->value['app_store']['user_download_link'] : "" }}">
                                            <img src="{{ asset('public/landing-page') }}/assets/img/apple-fav.png"
                                                 alt="">
                                            <span>{{translate('App Store')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <a href="#about" class="cmn--btn btn-white text-nowrap overflow-hidden text-truncate">
                            {{translate('Earn_From')}} {{ (($business_name && $business_name['value']) ? $business_name['value'] : "DriveMond") }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Banner Section End -->
    <!-- Basic Info Section Start -->
    <section class="basic-info-section">
        <div class="container position-relative">
            <div class="basic-info-wrapper wow animate__fadeInUp">
                <div class="basic-info-item">
                    <img
                        src="{{ $businessStatistics?->value && $businessStatistics?->value['total_download']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/total-download/'.$businessStatistics?->value['total_download']['image']) : asset('public/landing-page/assets/img/icons/1.png') }}"
                        alt="">
                    <div class="content">
                        <h5>{{ $businessStatistics?->value && $businessStatistics?->value['total_download']['count'] ? $businessStatistics?->value['total_download']['count'] : "1M+" }}</h5>
                        <div>{{ $businessStatistics?->value && $businessStatistics?->value['total_download']['content'] ? translate($businessStatistics?->value['total_download']['content']) : translate("download") }}</div>
                    </div>
                </div>
                <div class="basic-info-item">
                    <img
                        src="{{ $businessStatistics?->value && $businessStatistics?->value['complete_ride']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/complete-ride/'.$businessStatistics?->value['complete_ride']['image']) : asset('public/landing-page/assets/img/icons/2.png') }}"
                        alt="">
                    <div class="content">
                        <h5>{{ $businessStatistics?->value && $businessStatistics?->value['complete_ride']['count'] ? $businessStatistics?->value['complete_ride']['count'] : "1M+" }}</h5>
                        <div>{{ $businessStatistics?->value && $businessStatistics?->value['complete_ride']['content'] ? translate($businessStatistics?->value['complete_ride']['content']) : translate("Complete Ride") }}</div>
                    </div>
                </div>
                <div class="basic-info-item">
                    <img
                        src="{{ $businessStatistics?->value && $businessStatistics?->value['happy_customer']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/happy-customer/'.$businessStatistics?->value['happy_customer']['image']) : asset('public/landing-page/assets/img/icons/3.png') }}"
                        alt="">
                    <div class="content">
                        <h5>{{ $businessStatistics?->value && $businessStatistics?->value['happy_customer']['count'] ? $businessStatistics?->value['happy_customer']['count'] : "1M+" }}</h5>
                        <div>{{ $businessStatistics?->value && $businessStatistics?->value['happy_customer']['content'] ? translate($businessStatistics?->value['happy_customer']['content']) : translate("Happy Customer") }}</div>
                    </div>
                </div>
                <div class="basic-info-item">
                    <img
                        src="{{ $businessStatistics?->value && $businessStatistics?->value['support']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/support/'.$businessStatistics?->value['support']['image']) : asset('public/landing-page/assets/img/icons/4.png')}}"
                        alt="">
                    <div class="content">
                        <h5>{{ $businessStatistics?->value && $businessStatistics?->value['support']['title'] ? $businessStatistics?->value['support']['title'] : "24/7 hr" }}</h5>
                        <div>{{ $businessStatistics?->value && $businessStatistics?->value['support']['content'] ? translate($businessStatistics?->value['support']['content']) : translate("Support") }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Info Section End -->
    <!-- Platform Section Start -->
    <section class="platform-section pt-60 pb-60">
        <img src="{{ asset('public/landing-page/assets/img/platform/platform-bg.png') }}"
             class="shape d-none d-lg-block" alt="">
        <div class="container position-relative">
            <div class="text-center wow animate__fadeInUp pb-4">
                <h3 class="section-title mb-0">
                    {{ $ourSolutionSection?->value && $ourSolutionSection?->value['title'] ? translate($ourSolutionSection?->value['title']) : translate("Our_Solutions") }}
                    {{--                    {{ translate("Our")}} <span class="text--base">{{ translate("Solutions")}}</span>--}}
                </h3>
                <p class="section-text mt-0 pt-2">
                    {{ $ourSolutionSection?->value && $ourSolutionSection?->value['sub_title'] ? translate($ourSolutionSection?->value['sub_title']) : translate("Explore our dynamic day-to-day solution for everyday life") }}
                </p>

            </div>

            <div class="row justify-content-center gap-4 mt-3">
                @if($ourSolutionSectionListCount > 0)
                    @foreach($ourSolutionSectionList as $ourSolutionSingle)
                        @if($ourSolutionSingle?->value['status'] == 1)
                            <div class="col-sm-6 col-md-5 mb-3">
                                <div class="platform-item wow animate__fadeInUp">
                                    <img src="{{ onErrorImage(
                                            $ourSolutionSingle?->value['image'],
                                            asset('storage/app/public/business/landing-pages/our-solutions/'.$ourSolutionSingle?->value['image']),
                                            asset('public/landing-page/assets/img/platform/'.rand(1,2).'.png'),
                                            'business/landing-pages/our-solutions/',
                                        ) }}" alt="" class="img-fluid square-uploaded-img">
                                    <h6 class="title mt-3">
                                        {{ $ourSolutionSingle?->value['title'] ?? '' }}
                                    </h6>
                                    <p class="txt ">
                                        {{ $ourSolutionSingle?->value['description'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="col-sm-6 col-md-5 mb-3">
                        <div class="platform-item wow animate__fadeInUp">
                            <img src="{{ asset('public/landing-page/assets/img/platform/1.png') }}" alt="">
                            <h6 class="title">{{ translate('Ride Sharing') }}</h6>
                            <p class="txt">{{ translate('Book a ride to your desired destination and set a custom fare from the app') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-5 mb-3">
                        <div class="platform-item wow animate__fadeInUp">
                            <img src="{{ asset('public/landing-page/assets/img/platform/2.png') }}" alt="">
                            <h6 class="title">{{ translate('Parcel Delivery') }}</h6>
                            <p class="txt">{{ translate('Send important parcels to the right place with custom fare setup option') }}</p>
                        </div>
                    </div>
                @endif

            </div>

        </div>
    </section>
    <!-- Platform Section End -->
    <!-- earn money Section Start -->
    <section class="about-section bg-2 py-25">
        <div class="scroll-elem" id="about"></div>
        <div class="container">
            <div class="about__wrapper">
                <div class="about__wrapper-thumb wow animate__fadeInUp">
                    <img class="main-img"
                         src="{{ $earnMoneyImage?->value['image'] ? asset('storage/app/public/business/landing-pages/earn-money/'.$earnMoneyImage?->value['image']): asset('public/landing-page/assets/img/about1.png') }}"
                         alt="img">
                </div>

                <div class="about__wrapper-content bg-transparent wow animate__fadeInDown">
                    <h3 class="section-title text-start ms-0">{{ $earnMoney?->value && $earnMoney?->value['title'] ? translate($earnMoney?->value['title']) : translate("Earn Money with") }}
                        <span class="text--base">{{ (($business_name && $business_name['value']) ? $business_name['value'] : "DriveMond")}}</span></h3>
                    <p>
                        {{ $earnMoney?->value && $earnMoney?->value['sub_title'] ? translate($earnMoney?->value['sub_title']) : translate("With flexible schedules and a user-friendly platform, you can earn money with every ride. Become a ").(($business_name && $business_name['value']) ? $business_name['value'] : "DriveMond"). translate("today!") }}
                    </p>
                    <br>
                    <div class="dropdown d-inline-block">
                        <a class="cmn--btn btn-black px-4 h-50" href="#" data-bs-toggle="dropdown">
                            {{translate('Be a Delivery man / Driver')}}
                        </a>
                        <div class="dropdown-menu dropdown-button-menu">
                            <ul>
                                <li>
                                    <a href="">
                                        <img src="{{ asset('public/landing-page/assets/img/play-fav.png') }}" alt="">
                                        <span>{{translate('Play Store')}}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="">
                                        <img src="{{ asset('public/landing-page/assets/img/apple-fav.png') }}" alt="">
                                        <span>{{translate('App Store')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- About Section End -->
    <!-- Testimonial Section Start -->
    <section class="testimonial-section pt-50 pb-50">
        <div class="container-fluid">
            <h3 class="section-title mb-0 wow animate__fadeInUp"><span
                    class="text--base">2000+</span> {{ translate('People Share Their Love') }}</h3>
            <div class="wow animate__fadeInDown">
                <div class="testimonial-slider owl-theme owl-carousel">
                    @if($testimonialListCount>0)
                        @foreach($testimonials as $testimonial)
                            @if($testimonial?->value['status'] == 1)
                                <!-- Testimonial Slider Single Slide -->
                                <div class="testimonial__item">
                                    <div class="testimonial__item-img">
                                        <img src="{{ onErrorImage(
                                        $testimonial?->value && $testimonial?->value['reviewer_image'] ? $testimonial?->value['reviewer_image'] : '',
                                        $testimonial?->value && $testimonial?->value['reviewer_image'] ? asset('storage/app/public/business/landing-pages/testimonial/'.$testimonial?->value['reviewer_image']) : '',
                                        asset('public/landing-page/assets/img/client/user.png'),
                                        'business/landing-pages/testimonial/',
                                    ) }}" alt="client">

                                    </div>
                                    <div class="testimonial__item-cont">
                                        <h6 class="name">{{ $testimonial?->value && $testimonial?->value['reviewer_name'] ? $testimonial?->value['reviewer_name']: "" }}</h6>
                                        <span
                                            class="text--base">{{ $testimonial?->value && $testimonial?->value['designation'] ? $testimonial?->value['designation']: "" }}</span>
                                        <div class="rating">
                                            @php($count = $testimonial?->value && $testimonial?->value['rating'] ? $testimonial?->value['rating'] : 0)

                                            @for($inc=1;$inc<=5;$inc++)
                                                @if ($inc <= (int)$count)
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                @elseif ($count != 0 && $inc <= (int)$count + 1.1 && $count > ((int)$count))
                                                    <i class="bi bi-star-half text-warning"></i>
                                                @else
                                                    <i class="bi bi-star text-warning"></i>
                                                @endif
                                            @endfor

                                        </div>
                                        <blockquote>
                                            {{ $testimonial?->value && $testimonial?->value['review'] ? $testimonial?->value['review'] : "" }}
                                        </blockquote>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="testimonial__item">
                            <div class="testimonial__item-img">
                                <img src="{{asset('public/landing-page/assets/img/client/user.png')}}" alt="client">
                            </div>
                            <div class="testimonial__item-cont">
                                <h6 class="name">{{ "Roofus K." }}</h6>
                                <span
                                    class="text--base">{{ "Customer" }}</span>
                                <div class="rating">
                                    @php($count = 5)

                                    @for($inc=1;$inc<=5;$inc++)
                                        @if ($inc <= (int)$count)
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @elseif ($count != 0 && $inc <= (int)$count + 1.1 && $count > ((int)$count))
                                            <i class="bi bi-star-half text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-warning"></i>
                                        @endif
                                    @endfor

                                </div>
                                <blockquote>
                                    {{ "Exceeded my expectations! Customer support is responsive and helpful. Fantastic experience!" }}
                                </blockquote>
                            </div>
                        </div>
                    @endif
                </div>
                <!-- Testimonial Slider Bottom Counter and Nav Icons -->
                <div class="slider-bottom d-flex justify-content-center">
                    <div class="owl-btn testimonial-owl-prev">
                        <i class="las la-long-arrow-alt-left"></i>
                    </div>
                    <div class="slider-counter mx-3"></div>
                    <div class="owl-btn testimonial-owl-next">
                        <i class="las la-long-arrow-alt-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Testimonial Section End -->
    <!-- CTA Section Start -->
    <section class="cta-section py-60px-90px">
        <div class="container">
            <div class="cta--wrapper bg__img"
                 data-img="{{ $ctaImage?->value['background_image'] ? asset('storage/app/public/business/landing-pages/cta/'.$ctaImage?->value['background_image']) : asset('public/landing-page/assets/img/cta-bg.png') }}">
                <div class="cta-inner">
                    <div class="content wow animate__fadeInDown">
                        <h2 class="title text-capitalize">{{ $cta?->value && $cta?->value['title'] ? translate($cta?->value['title']) : translate("Download Our App") }}</h2>
                        <p class="mb-3 pb-1">
                            {{ $cta?->value && $cta?->value['sub_title'] ? translate($cta?->value['sub_title']) : translate("For both Android and IOS") }}
                        </p>
                        <div class="d-flex gap-4 gap-md-5">
                            <div class="me-xl-4">
                                <h6 class="text-white mb-3 font-regular">{{translate('User App')}}</h6>
                                <div class="d-flex gap-3 flex-column">
                                    <a target="_blank" class="no-gutter" type="button"
                                       href="{{ $cta?->value && $cta?->value['app_store']['user_download_link'] ? $cta?->value['app_store']['user_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/app-store.png"
                                             class="w-125px" alt="">
                                    </a>
                                    <a target="_blank" class="no-gutter" type="button"
                                       href="{{ $cta?->value && $cta?->value['play_store']['user_download_link'] ? $cta?->value['play_store']['user_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/play-store.png"
                                             class="w-125px" alt="">
                                    </a>
                                </div>
                            </div>
                            <div>
                                <h6 class="text-white mb-3 font-regular">{{translate('Driver App')}}</h6>
                                <div class="d-flex gap-3 flex-column">
                                    <a target="_blank" class="no-gutter" type="button"
                                       href="{{ $cta?->value && $cta?->value['app_store']['driver_download_link'] ? $cta?->value['app_store']['driver_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/app-store.png"
                                             class="w-125px" alt="">
                                    </a>
                                    <a target="_blank" class="no-gutter" type="button"
                                       href="{{ $cta?->value && $cta?->value['play_store']['driver_download_link'] ? $cta?->value['play_store']['driver_download_link'] : "" }}">
                                        <img src="{{ asset('public/landing-page') }}/assets/img/play-store.png"
                                             class="w-125px" alt="">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative w-100 max-355 wow animate__fadeInUp">
                        <img class="mw-100"
                             src="{{ $ctaImage?->value['image'] ? asset('storage/app/public/business/landing-pages/cta/'.$ctaImage?->value['image']) : asset('public/landing-page/assets/img/cta.png') }}"
                             alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- CTA Section End -->
@endsection
