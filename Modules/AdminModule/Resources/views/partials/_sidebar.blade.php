<aside class="aside">
    <!-- Aside Header -->
    <div class="aside-header">
        <!-- Logo -->
        <a href="{{route('admin.dashboard')}}" class="logo d-flex gap-2">
            <img width="115"
                 src="{{$logo ? asset("storage/app/public/business/".$logo) : asset('public/assets/admin-module/img/logo.png')}}"
                 alt="" class="main-logo">
        </a>
        <!-- End Logo -->

        <!-- Aside Toggle Menu Button -->
        <button class="toggle-menu-button aside-toggle bg-white p-0 dark-color">
            <i class="bi bi-chevron-left"></i>
        </button>
        <!-- End Aside Toggle Menu Button -->
    </div>
    <!-- End Aside Header -->

    <!-- Aside Body -->
    <div class="position-relative flex-grow-1">
        <div class="aside-body" data-trigger="scrollbar">
            <!-- User Profile -->
            <div class="user-profile media gap-3 align-items-center my-3">
                <div class="avatar rounded-circle">
                    <img class="fit-object dark-support rounded-circle"
                         src="{{ onErrorImage(
                        auth()->user()?->profile_image,
                        asset('storage/app/public/employee/profile') . '/' . auth()->user()->profile_image,
                        asset('public/assets/admin-module/img/user.png'),
                        'employee/profile/',
                    ) }}" alt="">
                </div>
                <div class="media-body ">
                    <div class="card-title fw-bold">{{auth()->user()?->email}}</div>
                    <span class="card-text">{{auth()->user()?->user_type}}</span>
                </div>
            </div>
            <!-- End User Profile -->

            <div class="aside-search mb-3">
                <form action="#" class="search-form">
                    <div class="input-group search-form__input_group">
                            <span class="search-form__icon">
                                <i class="bi bi-search"></i>
                            </span>
                        <input type="search" id="search-bar-input" class="theme-input-style search-form__input"
                               placeholder="{{ translate('Search_Here') }}"/>
                    </div>
                </form>
            </div>

            <!-- Nav -->
            <ul class="main-nav nav">
                @can('dashboard')
                    <li class="nav-category" title="{{ translate('dashboard') }}">
                        {{ translate('dashboard') }}
                    </li>
                    <li class="{{Request::is('admin') ?'active open':''}}">
                        <a href="{{route('admin.dashboard')}}">
                            <i class="bi bi-grid-fill"></i>
                            <span class="link-title">{{ translate('dashboard') }}</span>
                        </a>
                    </li>
                @endcan

                <!---------- Start Zone Management --------------->
                <li class="nav-category"
                    title="{{ translate('zone_management') }}">{{ translate('zone_management') }}</li>
                <li class="{{Request::is('admin/zone')||Request::is('admin/zone/*')? 'active open' :''}}">
                    <a href="{{route('admin.zone.index')}}">
                        <i class="bi bi-map"></i>
                        <span class="link-title text-capitalize">{{ translate('zone_setup') }}</span>
                    </a>
                </li>
                <!---------- End Zone Management --------------->

                <!----------------- Start Trip Management ------------------------>
                <li class="nav-category"
                    title="{{ translate('trip_management')}}">{{ translate('trip_management')}}</li>
                <li class="{{ Request::is('admin/trip/log') || Request::is('admin/trip/list/*') || Request::is('admin/trip/details/*')?'active sub-menu-opened':'' }} text-capitalize">
                    <a href="#">
                        <i class="bi bi-car-front"></i>
                        <span class="link-title">{{ translate('trips') }}</span>
                    </a>
                    <ul class="nav flex-column sub-menu text-capitalize">

                        <li class="{{ Request::is('admin/trip/list/all') || (Request::is('admin/trip/details/*')  && Request::get('type') == 'all') ?'active open':'' }}">
                            <a href="{{route('admin.trip.index', ['all'])}}">
                                <i class="bi bi-car-front-fill"></i>
                                <span class="link-title">{{ translate('all_trip')}}  </span> <span
                                    class="badge badge-primary float-end">{{$tripCount['all']}}</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('admin/trip/list/pending') || (Request::is('admin/trip/details/*')  && Request::get('type') == PENDING) ?'active open':'' }}">
                            <a href="{{route('admin.trip.index', [PENDING])}}">
                                <i class="bi bi-cloud-haze2-fill"></i>
                                <span class="link-title">{{ translate(PENDING)}}</span>
                                <span class="badge badge-primary float-end">{{$tripCount[PENDING]}}</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('admin/trip/list/accepted') || (Request::is('admin/trip/details/*')  && Request::get('type') == ACCEPTED) ?'active open':'' }}">
                            <a href="{{route('admin.trip.index', [ACCEPTED])}}">
                                <i class="bi bi-cloud-check-fill"></i>
                                <span class="link-title">{{ translate(ACCEPTED)}}</span>
                                <span class="badge badge-primary float-end">{{$tripCount[ACCEPTED]}}</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('admin/trip/list/ongoing') || (Request::is('admin/trip/details/*')  && Request::get('type') == ONGOING) ?'active open':'' }}">
                            <a href="{{route('admin.trip.index', [ONGOING])}}">
                                <i class="bi bi-clipboard-fill"></i>
                                <span class="link-title">{{ translate(ONGOING)}}</span>
                                <span class="badge badge-primary float-end">{{$tripCount[ONGOING]}}</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('admin/trip/list/completed') || (Request::is('admin/trip/details/*')  && Request::get('type') == 'completed') ?'active open':'' }}">
                            <a href="{{route('admin.trip.index', ['completed'])}}">
                                <i class="bi bi-clipboard-check-fill"></i>
                                <span class="link-title">{{ translate('completed')}}</span>
                                <span class="badge badge-primary float-end">{{$tripCount['completed']}}</span>
                            </a>
                        </li>
                        <li class="{{ Request::is('admin/trip/list/cancelled') || (Request::is('admin/trip/details/*')  && Request::get('type') == 'cancelled') ?'active open':'' }}">
                            <a href="{{route('admin.trip.index', ['cancelled'])}}">
                                <i class="bi bi-cloud-minus-fill"></i>
                                <span class="link-title">{{ translate('cancelled')}}</span>
                                <span class="badge badge-primary float-end">{{$tripCount['cancelled']}}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!----------------- End Trip Management ------------------------>

                <!---------- Start Promotion Management --------------->

                <li class="nav-category"
                    title="{{ translate('promotion_management') }}">{{ translate('promotion_management') }}</li>
                <li class="{{ Request::is('admin/promotion/banner-setup*')?'active open':'' }} text-capitalize">
                    <a href="{{route('admin.promotion.banner-setup.index')}}">
                        <i class="bi bi-flag-fill"></i>
                        {{ translate('banner_setup') }}
                    </a>
                </li>
                <!-- Coupon Setup -->
                <li class="{{Request::is('admin/promotion/coupon-setup*')?'active sub-menu-opened':''}} text-capitalize">
                    <a href="#">
                        <i class="bi bi-ticket-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('coupon_setup') }}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav flex-column sub-menu text-capitalize">
                        <li class="{{Request::is('admin/promotion/coupon-setup') | Request::is('admin/promotion/coupon-setup/edit*')? 'active open' : ''}}">
                            <a href="{{ route('admin.promotion.coupon-setup.index')  }}">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('coupon_list') }}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/promotion/coupon-setup/create') ? 'active open' : ''}}">
                            <a href="{{route('admin.promotion.coupon-setup.create')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('add_new_coupon') }}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <!-- Coupon Setup End-->
                <!-- Discount Setup -->
                <li class="{{Request::is('admin/promotion/discount-setup*')?'active sub-menu-opened':''}} text-capitalize">
                    <a href="#">
                        <i class="bi bi-ticket-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('discount_setup') }}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav flex-column sub-menu text-capitalize">
                        <li class="{{Request::is('admin/promotion/discount-setup') | Request::is('admin/promotion/discount-setup/edit*')? 'active open' : ''}}">
                            <a href="{{ route('admin.promotion.discount-setup.index')  }}">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('discount_list') }}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/promotion/discount-setup/create') ? 'active open' : ''}}">
                            <a href="{{route('admin.promotion.discount-setup.create')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('add_new_discount') }}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <!-- Coupon Setup End-->
                <!---------- End Promotion Management --------------->

                <!---------- Start User Management --------------->
                <li class="nav-category"
                    title="{{ translate('user_management')}}">{{ translate('user_management')}}</li>
                <li class="{{Request::is('admin/driver/level*')? 'active sub-menu-opened' : ''}}">
                    <a href="">
                        <i class="bi bi-people-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('driver_level_setup')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/driver/level') || Request::is('admin/driver/level/edit/*') || Request::is('admin/driver/level/log*') ? 'active open' : ''}}">
                            <a href="{{route('admin.driver.level.index')}}" class="text-capitalize">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('driver_levels')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/driver/level/create') ? 'active open' : ''}}">
                            <a href="{{route('admin.driver.level.create')}}" class="text-capitalize">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('add_driver_level')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <li class="has-sub-item {{Request::is('admin/driver') || Request::is('admin/driver/create') || Request::is('admin/driver/edit/*') || Request::is('admin/driver/show*') || Request::is('admin/driver/trash')
                    || Request::is('admin/driver/edit/*') || Request::is('admin/driver/profile-update-request-list') || Request::is('admin/driver/log*') || Request::is('admin/driver/cash*')  ? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-people-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('driver_setup')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/driver') || Request::is('admin/driver/edit/*') || Request::is('admin/driver/log*') || Request::is('admin/driver/cash*') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.driver.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('driver_list')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/driver/create') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.driver.create')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('add_new_driver')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/driver/profile-update-request-list') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.driver.profile-update-request-list')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('driver_identity_request_list')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <li class="has-sub-item{{Request::is('admin/driver/withdraw*') ? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-cash-coin"></i>
                        <span class="link-title text-capitalize">{{translate('withdraw')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/driver/withdraw-method') || Request::is('admin/driver/withdraw-method/edit/*') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.driver.withdraw-method.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('method_list')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/driver/withdraw-method/create') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.driver.withdraw-method.create')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('add_method')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/driver/withdraw/request*') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.driver.withdraw.requests')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('withdraw_requests')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <li class="{{Request::is('admin/customer/level*')? 'active sub-menu-opened' : ''}}">
                    <a href="">
                        <i class="bi bi-person-fill-add"></i>
                        <span class="link-title text-capitalize">{{translate('customer_level_setup')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/customer/level') || Request::is('admin/customer/level/edit*') || Request::is('admin/customer/level/log*')? 'active open' : ''}}">
                            <a href="{{route('admin.customer.level.index')}}" class="text-capitalize">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('customer_levels')}}
                            </a>
                        </li>
                        @can('add', \Modules\UserManagement\Entities\UserLevel::class)
                            <li class="{{Request::is('admin/customer/level/create') ? 'active open' : ''}}">
                                <a href="{{route('admin.customer.level.create')}}" class="text-capitalize">
                                    <i class="bi bi-dash-lg"></i>
                                    {{translate('add_customer_level')}}
                                </a>
                            </li>
                        @endcan
                    </ul>
                    <!-- End Sub Menu -->
                </li>

                <li class="has-sub-item {{Request::is('admin/customer/show*') || Request::is('admin/customer') || Request::is('admin/customer/create') || Request::is('admin/customer/edit/*') || Request::is('admin/customer/log*')? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-person-plus-fill"></i>
                        <span class="link-title text-capitalize">{{translate('customer_setup')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/customer') || Request::is('admin/customer/edit/*') || Request::is('admin/customer/show*') || Request::is('admin/customer/log*') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.customer.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('customer_list')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/customer/create') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.customer.create')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('add_new_customer')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>

                <li class="{{Request::is('admin/customer/wallet*')? 'active open' : ''}}">
                    <a href="{{route('admin.customer.wallet.index')}}">
                        <i class="bi bi-wallet-fill"></i>
                        <span class="link-title text-capitalize">{{translate('customer_wallet')}}</span>
                    </a>
                </li>

                <li class="has-sub-item {{Request::is('admin/employee*')? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-person-square"></i>
                        <span class="link-title text-capitalize">{{translate('employee_setup')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/employee/role*') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.employee.role.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('attribute_setup')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/employee') || Request::is('admin/employee/edit*') || Request::is('admin/employee/log*') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.employee.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('employee_list')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/employee/create') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.employee.create')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('add_new_employee')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <!---------- End user Management --------------->


                <!---------- Start Parcel Management --------------->

                <li class="nav-category"
                    title="{{translate('parcel_management')}}">{{translate('parcel_management')}}</li>
                <!-- Parcel Attribute Setup -->

                <!-- Sub Menu -->
                <li class="has-sub-item {{Request::is('admin/parcel*')? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-patch-plus"></i>
                        <span class="link-title">{{ translate('parcel_attributes') }}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav flex-column sub-menu">
                        <li class="{{ Request::is('admin/parcel/attribute/category') || Request::is('admin/parcel/attribute/category/edit*')?'active open':'' }}">
                            <a href="{{ route('admin.parcel.attribute.category.index') }}">
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('parcel_categories') }}
                            </a>
                        </li>
                        <li class="{{ Request::is('admin/parcel/attribute/weight') || Request::is('admin/parcel/attribute/weight/edit*')?'active open':'' }}">
                            <a href={{ route('admin.parcel.attribute.weight.index') }}>
                                <i class="bi bi-dash-lg"></i>
                                {{ translate('parcel_weights') }}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <!-- DParcel Attribute Setup-->

                <!---------- End Parcel Management --------------->

                <!---------- Start Vehicle Management --------------->

                <li class="nav-category" title="{{ translate('vehicles_management') }}">
                    {{ translate('vehicles_management') }}
                </li>
                <li class="{{Request::is('admin/vehicle/attribute-setup/*')?'active open':''}}">
                    <a href="{{ route('admin.vehicle.attribute-setup.brand.index') }}">
                        <i class="bi bi-ev-front-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('vehicle_attribute_setup') }}</span>
                    </a>
                </li>
                <li class="{{Request::is('admin/vehicle/log') || Request::is('admin/vehicle') || Request::is('admin/vehicle/show*') || Request::is('admin/vehicle/edit*')?'active open':''}}">
                    <a href="{{ route('admin.vehicle.index') }}">
                        <i class="bi bi-car-front-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('vehicle_list') }}</span>
                    </a>
                </li>
                <li class="{{Request::is('admin/vehicle/create') ?'active open':''}}">
                    <a href="{{ route('admin.vehicle.create') }}">
                        <i class="bi bi-truck-front-fill"></i>
                        <span class="link-title text-capitalize">{{ translate('add_new_vehicle') }}</span>
                    </a>
                </li>
                <!---------- End Vehicle Management --------------->

                <!---------- Start Fare Management --------------->
                <li class="nav-category" title="{{translate('fare_management')}}">{{translate('fare_management')}}</li>
                <li class="{{Request::is('admin/fare/trip*')? 'active open' : ''}}">
                    <a href="{{route('admin.fare.trip.index')}}">
                        <i class="bi bi-sign-intersection-y-fill"></i>
                        <span class="link-title text-capitalize">{{translate('trip_fare_setup')}}</span>
                    </a>
                </li>
                <li class="{{Request::is('admin/fare/parcel*')? 'active open' : ''}}">
                    <a href="{{route('admin.fare.parcel.index')}}">
                        <i class="bi bi-box"></i>
                        <span class="link-title text-capitalize">{{translate('parcel_delivery_fare_setup')}}</span>
                    </a>
                </li>
                <!---------- End Fare Management --------------->

                <li class="nav-category"
                    title="{{translate('transaction_management')}}">{{translate('transaction_management')}}</li>

                <li class="{{Request::is('admin/transaction*')? 'active open' : ''}}">
                    <a href="{{route('admin.transaction.index')}}">
                        <i class="bi bi-cash-stack"></i>
                        <span class="link-title text-capitalize">{{translate('transactions')}}</span>
                    </a>
                </li>

                <li class="nav-category" title="Business Management">{{translate('business_management')}}</li>
                <li class="
                {{Request::is('admin/business/setup*')? 'active sub-menu-opened' : ''}}">
                    <a href="{{route('admin.business.setup.info.index')}}">
                        <i class="bi bi-briefcase-fill"></i>
                        <span class="link-title text-capitalize">{{translate('business_setup')}}</span>
                    </a>

                </li>
                <li class="has-sub-item {{Request::is('admin/business/pages-media/*')? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-file-earmark-break-fill"></i>
                        <span class="link-title text-capitalize">{{translate('pages_&_media')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/business/pages-media/business-page') ? 'active open' : ''}}">
                            <a class="text-capitalize"
                               href="{{route('admin.business.pages-media.business-page.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('business_pages')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/business/pages-media/landing-page/*') ? 'active open' : ''}}">
                            <a class="text-capitalize"
                               href="{{route('admin.business.pages-media.landing-page.intro-section.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('landing_Page_Setup')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/business/pages-media/social-media') ? 'active open' : ''}}">
                            <a class="text-capitalize" href="{{route('admin.business.pages-media.social-media')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('social_media_links')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <li class="has-sub-item  {{Request::is('admin/business/configuration*')? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-gear-wide-connected"></i>
                        <span class="link-title">{{translate('configurations')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/business/configuration/notification') ? 'active open' : ''}}">
                            <a href="{{route('admin.business.configuration.notification.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('Notification')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/business/configuration/third-party/*') ? 'active open' : ''}}">
                            <a href="{{route('admin.business.configuration.third-party.payment-method.index')}}"
                               class="text-capitalize">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('3rd_party')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
                <li class="has-sub-item
                {{Request::is('admin/business/environment-setup*') || Request::is('admin/business/clean-database*') ||
                    Request::is('admin/business/languages*')? 'active sub-menu-opened' : ''}}">
                    <a href="#">
                        <i class="bi bi-sliders2-vertical"></i>
                        <span class="link-title text-capitalize">{{translate('system_settings')}}</span>
                    </a>
                    <!-- Sub Menu -->
                    <ul class="nav sub-menu">
                        <li class="{{Request::is('admin/business/environment-setup*') ? 'active open' : ''}}">
                            <a href="{{route('admin.business.environment-setup.index')}}" class="text-capitalize">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('environment_setup')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/business/clean-database*') ? 'active open' : ''}}">
                            <a href="{{route('admin.business.clean-database.index')}}" class="text-capitalize">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('clean_database')}}
                            </a>
                        </li>
                        <li class="{{Request::is('admin/business/languages*') ? 'active open' : ''}}">
                            <a href="{{route('admin.business.languages.index')}}">
                                <i class="bi bi-dash-lg"></i>
                                {{translate('languages')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Sub Menu -->
                </li>
            </ul>
            <!-- End Nav -->
        </div>
    </div>
    <!-- End Aside Body -->
</aside>

@push('script')
    <script src="{{asset('public/assets/admin-module/js/admin-module/sidebar.js')}}"></script>
@endpush
