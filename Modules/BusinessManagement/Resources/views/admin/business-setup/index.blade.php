@extends('adminmodule::layouts.master')

@section('title', translate('Business_Info'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{ translate('business_management') }}</h2>

            <form action="{{ route('admin.business.setup.info.store') }}" id="business_form" method="post"
                enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <div class="">
                            @include('businessmanagement::admin.business-setup.partials._business-setup-inline')
                        </div>
                    </div>
                    <div class="col-12">
                        @can('business_edit')
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div
                                        class="border rounded border-primary-light px-4 py-3 d-flex justify-content-between mb-1">
                                        <h6 class="d-flex text-primary text-capitalize">{{ translate('maintenance_mode') }}
                                        </h6>
                                        <div class="position-relative">
                                            <label class="switcher">
                                                <input class="switcher_input status-change"
                                                    data-url="{{ route('admin.business.setup.info.maintenance') }}"
                                                    id="maintenance" type="checkbox"
                                                    {{ $settings->where('key_name', 'maintenance_mode')->first()?->value == 1 ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <p class="text-warning fs-13">
                                        *{{ translate('By turning the ‘Maintenance Mode’ ON, all your apps and user websites will be disabled temporarily') . '. ' . translate('Only the Admin Panel will be functional') }}
                                    </p>
                                </div>
                            </div>
                        @endcan
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="fw-medium d-flex align-items-center gap-2 text-capitalize">
                                    <i class="bi bi-briefcase-fill"></i>
                                    {{ translate('company_information') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="business_name"
                                                class="mb-2">{{ translate('business_name') }}</label>
                                            <input type="text" name="business_name"
                                                value="{{ $settings->firstWhere('key_name', 'business_name')?->value ?? old('business_name') }}"
                                                id="business_name" class="form-control"
                                                placeholder="{{ translate('Ex: ABC Company') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="business_contact_num"
                                                class="mb-2">{{ translate('business_contact_number') }}</label>
                                            <input type="text" name="business_contact_phone"
                                                value="{{ $settings->firstWhere('key_name', 'business_contact_phone')?->value ?? old('business_contact_phone') }}"
                                                id="business_contact_num" class="form-control"
                                                placeholder="{{ translate('Ex: +9XXX-XXX-XXXX') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="business_email"
                                                class="mb-2">{{ translate('business_contact_email') }}</label>
                                            <input type="email" name="business_contact_email"
                                                value="{{ $settings->firstWhere('key_name', 'business_contact_email')->value ?? old('business_contact_email') }}"
                                                id="business_email" class="form-control"
                                                placeholder="{{ translate('Ex: company@email.com') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="business_address"
                                                class="mb-2">{{ translate('business_address') }}</label>
                                            <textarea name="business_address" id="business_address" cols="30" rows="6" class="form-control"
                                                placeholder="{{ translate('Type Here ...') }}">{{ $settings->firstWhere('key_name', 'business_address')?->value ?? old('business_address') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label for="business_support_number"
                                                        class="mb-2">{{ translate('business_support_number') }}</label>
                                                    <input type="text" name="business_support_phone"
                                                        value="{{ $settings->firstWhere('key_name', 'business_support_phone')?->value ?? old('business_support_phone') }}"
                                                        id="business_support_number" class="form-control"
                                                        placeholder="{{ translate('Ex: 9XXX-XXX-XXXX') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label for="business_support_email"
                                                        class="mb-2">{{ translate('business_support_email') }}</label>
                                                    <input type="text" name="business_support_email"
                                                        value="{{ $settings->firstWhere('key_name', 'business_support_email')?->value ?? old('business_support_email') }}"
                                                        id="business_support_email" class="form-control"
                                                        placeholder="{{ translate('Ex: support@email.com') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label for="trade_licence_number"
                                                        class="mb-2">{{ translate('trade_licence_number') }}</label>
                                                    <input type="text" name="trade_licence_number"
                                                        value="{{ $settings->firstWhere('key_name', 'trade_licence_number')?->value ?? old('trade_licence_number') }}"
                                                        id="trade_licence_number" class="form-control"
                                                        placeholder="{{ translate('Ex: 9.43896534') }}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-4">
                                                    <label for="copyright_text"
                                                        class="mb-2">{{ translate('company_copyright_text') }}</label>
                                                    <input type="text" name="copyright_text"
                                                        value="{{ $settings->firstWhere('key_name', 'copyright_text')?->value ?? old('copyright_text') }}"
                                                        id="copyright_text" class="form-control"
                                                        placeholder="{{ translate('Copyright@email.com') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="fw-medium d-flex align-items-center gap-2 text-capitalize">
                                    <i class="bi bi-briefcase-fill"></i>
                                    {{ translate('business_information') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-end">
                                    <div class="col-sm-12 col-lg-12">
                                        <div class="mb-4">
                                            <label for="language" class="mb-2">{{ translate('language') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('choose_all_languages_you_want_to_add') }}"></i>
                                            </label>
                                            <select name="language[]" id="language" class="js-select-lan"
                                                multiple="multiple">
                                                @php($d_languages = $settings->where('key_name', 'language')->first()->value ?? [])
                                                @foreach (LANGUAGES as $language)
                                                    <option value="{{ $language['code'] }}"
                                                        {{ in_array($language['code'], $d_languages) ? 'selected' : '' }}>
                                                        {{ $language['name'] }} - {{ $language['nativeName'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="country" class="mb-2">{{ translate('country') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('choose_your_business_location') }}"></i>
                                            </label>
                                            <select name="country_code" id="country" class="js-select" required>
                                                <option value="" disabled selected>
                                                    {{ translate('select_country') }}</option>
                                                @foreach (COUNTRIES as $country)
                                                    <option value="{{ $country['code'] }}"
                                                        {{ ($settings->where('key_name', 'country_code')->first()->value ?? '') == $country['code'] ? 'selected' : '' }}>
                                                        {{ $country['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            @php($cc = $settings->where('key_name', 'currency_code')->first()?->value)
                                            <label for="currency" class="mb-2">{{ translate('currency') }}
                                                <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('choose_the_currency_of_your_business') }}"></i>
                                            </label>
                                            <select name="currency_code" id="currency" class="js-select">
                                                <option disabled selected>{{ translate('select_currency') }}</option>
                                                @foreach (CURRENCIES as $currency)
                                                    <option value="{{ $currency['code'] }}"
                                                        {{ $cc == $currency['code'] ? 'selected' : '' }}>
                                                        {{ $currency['name'] }}
                                                        ({{ $currency['symbol'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <label class="mb-2">{{ translate('currency_position') }}
                                            <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                               data-bs-toggle="tooltip"
                                               data-bs-title="{{ translate('Left: $99; Right: 99$') }}"></i>
                                        </label>
                                        <div class="d-flex align-items-center form-control mb-4">
                                            <div class="flex-grow-1">
                                                <input type="radio" name="currency_symbol_position" value="left"
                                                    id="currency_position_left"
                                                    {{ ($settings->firstWhere('key_name', 'currency_symbol_position')?->value ?? '') == 'left' ? 'checked' : '' }}>
                                                <label for="currency_position_left"
                                                    class="media gap-2 align-items-center">
                                                    <i class="tio-agenda-view-outlined text-muted"></i>
                                                    <span class="media-body">
                                                        ($) {{ translate('left') }}
                                                    </span>
                                                </label>
                                            </div>

                                            <div class="flex-grow-1">
                                                <input type="radio" name="currency_symbol_position" value="right"
                                                    id="currency_position_right"
                                                    {{ ($settings->where('key_name', 'currency_symbol_position')->first()->value ?? '') == 'right' ? 'checked' : '' }}>
                                                <label for="currency_position_right"
                                                    class="media gap-2 align-items-center">
                                                    <i class="tio-table text-muted"></i>
                                                    <span class="media-body">{{ translate('right') }} ($)</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="time_zone"
                                                class="mb-2">{{ translate('time_zone') }}</label>
                                            <select name="time_zone" id="time_zone" class="js-select">
                                                <option value="" disabled selected>
                                                    {{ translate('select_time_zone') }}</option>
                                                @foreach (TIME_ZONES as $zone)
                                                    <option value="{{ $zone['tzCode'] }}"
                                                        {{ ($settings->where('key_name', 'time_zone')->first()->value ?? '') == $zone['tzCode'] ? 'selected' : '' }}>
                                                        (GMT{{ $zone['utc'] }})
                                                        {{ $zone['tzCode'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="time_format" class="mb-2">Time Format</label>
                                            <select name="time_format" id="time_format" class="js-select">
                                                <option value="" disabled selected>Select Time Format</option>
                                                <option value="h:i:s A"
                                                    {{ ($settings->where('key_name', 'time_format')->first()->value ?? '') == 'h:i:s A' ? 'selected' : '' }}>
                                                    {{ translate('12_hour') }}</option>
                                                <option value="H:i:s"
                                                    {{ ($settings->where('key_name', 'time_format')->first()->value ?? '') == 'H:i:s' ? 'selected' : '' }}>
                                                    {{ translate('24_hour') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <label for="currency_decimal"
                                                class="mb-2">{{ translate('decimal_after_point') }}</label>
                                            <input type="number" name="currency_decimal_point"
                                                value="{{ $settings->firstWhere('key_name', 'currency_decimal_point')?->value ?? old('currency_decimal_point') }}"
                                                id="currency_decimal" class="form-control"
                                                placeholder="{{ translate('Ex: 2') }}">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <div
                                                class="form-control gap-2 align-items-center d-flex justify-content-between">
                                                <div class="d-flex align-items-center fw-medium gap-2 text-capitalize">
                                                    {{ translate('driver_self_registration') }}
                                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-title="{{ translate('if_enabled,_drivers_can_register_themselves_from_the_driver_app') }}">
                                                    </i>
                                                </div>
                                                <div class="position-relative">
                                                    <label class="switcher">
                                                        <input type="checkbox" name="driver_self_registration"
                                                            class="switcher_input"
                                                            {{ $settings->where('key_name', 'driver_self_registration')->first()->value ?? 0 == 1 ? 'checked' : '' }}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <div
                                                class="form-control gap-2 align-items-center d-flex justify-content-between">
                                                <div class="d-flex align-items-center fw-medium gap-2 text-capitalize">
                                                    {{ translate('customer_verification') }}
                                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-title="{{ translate('if_enabled,_customers_need_to_verify_their_registration') }}">
                                                    </i>
                                                </div>
                                                <div class="position-relative">
                                                    <label class="switcher">
                                                        <input type="checkbox" name="customer_verification"
                                                            class="switcher_input"
                                                            {{ $settings->firstWhere('key_name', 'customer_verification')->value ?? 0 == 1 ? 'checked' : '' }}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4">
                                            <div
                                                class="form-control gap-2 align-items-center d-flex justify-content-between">
                                                <div class="d-flex align-items-center fw-medium gap-2 text-capitalize">
                                                    {{ translate('driver_verification') }}
                                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                                       data-bs-toggle="tooltip"
                                                       data-bs-title="{{ translate('if_enabled,_drivers_need_to_verify_their_registration') }}">
                                                    </i>
                                                </div>
                                                <div class="position-relative">
                                                    <label class="switcher">
                                                        <input type="checkbox" name="driver_verification"
                                                            class="switcher_input"
                                                            {{ $settings->where('key_name', 'driver_verification')->first()->value ?? 0 == 1 ? 'checked' : '' }}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="parcel_weight_unit"
                                                class="mb-2">{{ translate('parcel_weight_unit') }}</label>
                                            <select name="parcel_weight_unit" id="parcel_weight_unit" class="js-select"
                                                required>
                                                <option value="" selected disabled>
                                                    {{ translate('select_parcel_weight_unit') }}</option>
                                                <option value="kg"
                                                    {{ ($settings->firstWhere('key_name', 'parcel_weight_unit')->value ?? '') == 'kg' ? 'selected' : '' }}>
                                                    {{ translate('kilogram') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="fw-medium d-flex align-items-center gap-2 text-capitalize">
                                    <i class="bi bi-palette-fill"></i>
                                    {{ translate('website_color') }}
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-wrap gap-4">
                                <div class="form-group">
                                    <input type="color" name="website_color[primary]"
                                        class="form-control form-control_color"
                                        value="{{ $settings->firstWhere('key_name', 'website_color')->value['primary'] ?? null }}">
                                    <div class="text-center">
                                        <div class="fs-12 fw-semibold text-dark color_code mt-2 mb-1 text-uppercase">
                                            {{ $settings->firstWhere('key_name', 'website_color')->value['primary'] ?? null }}
                                        </div>
                                        <label
                                            class="title-color text-capitalize">{{ translate('primary_color') }}</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="color" name="website_color[secondary]"
                                        class="form-control form-control_color"
                                        value="{{ $settings->firstWhere('key_name', 'website_color')->value['secondary'] ?? null }}">
                                    <div class="text-center">
                                        <div class="fs-12 fw-semibold text-dark color_code mt-2 mb-1 text-uppercase">
                                            {{ $settings->firstWhere('key_name', 'website_color')->value['secondary'] ?? null }}
                                        </div>
                                        <label class="title-color text-capitalize">
                                            {{ translate('secondary_color') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="color" name="website_color[background]"
                                        class="form-control form-control_color"
                                        value="{{ $settings->firstWhere('key_name', 'website_color')->value['background'] ?? null }}">
                                    <div class="text-center">
                                        <div class="fs-12 fw-semibold text-dark color_code mt-2 mb-1 text-uppercase">
                                            {{ $settings->firstWhere('key_name', 'website_color')->value['background'] ?? null }}
                                        </div>
                                        <label class="title-color text-capitalize">
                                            {{ translate('background') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="color" name="text_color[secondary]"
                                        class="form-control form-control_color"
                                        value="{{ $settings->firstWhere('key_name', 'text_color')->value['secondary'] ?? null }}">
                                    <div class="text-center">
                                        <div class="fs-12 fw-semibold text-dark color_code mt-2 mb-1 text-uppercase">
                                            {{ $settings->firstWhere('key_name', 'text_color')->value['secondary'] ?? null }}
                                        </div>
                                        <label class="title-color text-capitalize">
                                            {{ translate('secondary_text') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="color" name="text_color[light]"
                                        value="{{ $settings->firstWhere('key_name', 'text_color')->value['light'] ?? null }}"
                                        class="form-control form-control_color">
                                    <div class="text-center">
                                        <div class="fs-12 fw-semibold text-dark color_code mt-2 mb-1 text-uppercase">
                                            {{ $settings->firstWhere('key_name', 'text_color')->value['light'] ?? null }}
                                        </div>
                                        <label class="title-color text-capitalize">
                                            {{ translate('light_text') }}
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input type="color" name="text_color[primary]"
                                        value="{{ $settings->firstWhere('key_name', 'text_color')->value['primary'] ?? null }}"
                                        class="form-control form-control_color">
                                    <div class="text-center">
                                        <div class="fs-12 fw-semibold text-dark color_code mt-2 mb-1 text-uppercase">
                                            {{ $settings->firstWhere('key_name', 'text_color')->value['primary'] ?? null }}
                                        </div>
                                        <label class="title-color text-capitalize">
                                            {{ translate('primary_text') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-6">
                        <div class="card h-100">
                            <div class="card-header d-flex flex-wrap justify-content-between gap-2">
                                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2">
                                    <i class="bi bi-image"></i>
                                    {{ translate('website_header_logo') }}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="{{ translate('website_header_logo') }}"></i>
                                </h5>
                                <span class="badge badge-primary">{{ translate('3:1') }}</span>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="d-flex flex-column justify-content-around gap-4">
                                    <div class="d-flex justify-content-center">
                                        <div class="upload-file auto">
                                            <input type="file" name="header_logo" class="upload-file__input" accept=".png">
                                            <span class="edit-btn">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                            </span>
                                            <div class="upload-file__img">
                                                <img width="250" height="60" loading="lazy"
                                                    src="{{ onErrorImage(
                                                        $settings?->firstWhere('key_name', 'header_logo')?->value,
                                                        asset('storage/app/public/business') . '/' . $settings?->firstWhere('key_name', 'header_logo')?->value,
                                                        asset('public/assets/admin-module/img/media/banner-upload-file.png'),
                                                        'business/',
                                                    ) }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <p class="opacity-75 mx-auto max-w220">
                                        {{ translate('File Format - png Image Size - Maximum Size 5 MB.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-6">
                        <div class="card h-100">
                            <div class="card-header flex-wrap d-flex justify-content-between gap-2">
                                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2">
                                    <i class="bi bi-image"></i>
                                    {{ translate('website_favicon') }}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="{{ translate('website_favicon') }}"></i>
                                </h5>
                                <span class="badge badge-primary">1:1</span>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="d-flex flex-column justify-content-around gap-4">
                                    <div class="d-flex justify-content-center">
                                        <div class="upload-file auto">
                                            <input type="file" name="favicon" class="upload-file__input" accept=".png">
                                            <span class="edit-btn">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                            </span>
                                            <div class="upload-file__img">
                                                <img width="64" height="64" loading="lazy"
                                                    src="{{ onErrorImage(
                                                        $settings?->firstWhere('key_name', 'favicon')?->value,
                                                        asset('storage/app/public/business') . '/' . $settings?->firstWhere('key_name', 'favicon')?->value,
                                                        asset('public/assets/admin-module/img/media/upload-file.png'),
                                                        'business/',
                                                    ) }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <p class="opacity-75 mx-auto max-w220">
                                        {{ translate('File Format - png Image Size - Maximum Size 5 MB.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-6">
                        <div class="card h-100">
                            <div class="card-header flex-wrap d-flex justify-content-between gap-2">
                                <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2">
                                    <i class="bi bi-image"></i>
                                    {{ translate('loading_gif') }}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip"
                                       data-bs-title="{{ translate('loading_gif') }}"></i>
                                </h5>
                                <span class="badge badge-primary">1:1</span>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-around">
                                <div class="d-flex flex-column justify-content-around gap-4">
                                    <div class="d-flex justify-content-center">
                                        <div class="upload-file auto">
                                            <input type="file" name="preloader" class="upload-file__input" accept=".gif">
                                            <span class="edit-btn">
                                                <i class="bi bi-pencil-square text-primary"></i>
                                            </span>
                                            <div class="upload-file__img">
                                                <img width="180" height="180" loading="lazy"
                                                    src="{{ onErrorImage(
                                                        $settings?->firstWhere('key_name', 'preloader')?->value,
                                                        asset('storage/app/public/business') . '/' . $settings?->firstWhere('key_name', 'preloader')?->value,
                                                        asset('public/assets/admin-module/img/media/upload-file.png'),
                                                        'business/',
                                                    ) }}"
                                                    alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <p class="opacity-75 mx-auto max-w220">
                                        {{ translate('File Format - gif Image Size - Maximum Size 5 MB.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end mb-5">
                        <button type="submit"
                            class="btn btn-primary text-capitalize">{{ translate('save_information') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/business-management/business-setup/index.js') }}"></script>

    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#business_form').on('submit', function(e) {
            if (!permission) {
                toastr.error('{{ translate('you_donot_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>
@endpush
