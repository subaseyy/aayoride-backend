<!DOCTYPE html>
<html lang="en">
@php($businessName = businessConfig('business_name', 'business_information')?->value)
@php($businessLogo = businessConfig('header_logo', 'business_information')?->value)
@php($businessContactEmail = businessConfig('business_contact_email', 'business_information')?->value)
@php($businessContactPhone = businessConfig('business_contact_phone', 'business_information')?->value)

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$businessName}} {{translate('invoice')}}</title>
    <style>

        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa2JL7SUc.woff2')}}) format('woff2');
            unicode-range: U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;
        }
        /* cyrillic */
        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa0ZL7SUc.woff2')}}) format('woff2');
            unicode-range: U+0301, U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;
        }
        /* greek-ext */
        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa2ZL7SUc.woff2')}}) format('woff2');
            unicode-range: U+1F00-1FFF;
        }
        /* greek */
        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa1pL7SUc.woff2')}}) format('woff2');
            unicode-range: U+0370-0377, U+037A-037F, U+0384-038A, U+038C, U+038E-03A1, U+03A3-03FF;
        }
        /* vietnamese */
        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa2pL7SUc.woff2')}}) format('woff2');
            unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
        }
        /* latin-ext */
        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa25L7SUc.woff2')}}) format('woff2');
            unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20C0, U+2113, U+2C60-2C7F, U+A720-A7FF;
        }
        /* latin */
        @font-face {
            font-family: "Inter", sans-serif;
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url({{asset('public/assets/admin-module/fonts/Inter/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa1ZL7.woff2')}}) format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
        body , html {
            font-size: 12px;
        }
        *, ::after, ::before {
            box-sizing: border-box;
        }
        * {

            font-weight: 400;
            font-family: "Inter", sans-serif;
        }
        .media-wrap > .media:not(:last-child) {
            position: relative;
        }
        .media-wrap > .media:not(:last-child)::after {
            position: absolute;
            left: 16px;
            top: 35px;
            width: 1px;
            height: 50%;
            border-left: 1px dashed var(--bs-primary);
            content: "";
        }
        .table-striped tbody tr:nth-of-type(odd) td {
            /* background: #e0fffb;
            background-color: #e0fffb; */
            color: #293231;
        }
        table {
            caption-side: bottom;
            border-collapse: collapse;
            font-size: 12px;
        }
        .bg-white {
            background-color: #fff !important;
        }
        .invoice-main-title {
            letter-spacing: 1ch;
        }
        .text-uppercase {
            text-transform: uppercase !important;
        }
        .text-center {
            text-align: center !important;
        }
        .text-right {
            text-align: right !important;
        }
        .text-left {
            text-align: left !important;
        }
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        .text-dark {
            color: #293231 !important;
        }
        .gap-3 {
            gap: 1rem !important;
        }
        .pb-2 {
            padding-bottom: 0.5rem !important;
        }
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        .pb-4 {
            padding-bottom: 1.5rem !important;
        }
        .gap-1 {
            gap: 0.25rem !important;
        }
        .align-items-center {
            align-items: center !important;
        }
        .justify-content-between {
            justify-content: space-between !important;
        }
        .justify-content-center {
            justify-content: center !important;
        }
        /*.border {*/
        /*    border: 1px solid #ebebeb !important;*/
        /*}*/
        .border-primary {
            border-color: #14b19e !important;
        }
        .border-bottom {
            border-bottom: 1px solid #ebebeb !important;
        }
        .d-flex {
            display: flex !important;
        }
        .p-3 {
            padding: 1rem !important;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #293231;
            vertical-align: top;
            border-color: #f4f4f4;
        }
        a,
        .text-primary {
            color: #14b19e !important;
        }
        a {
            text-decoration: none;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-top: 0;
            margin-right: 12px;
            margin-left: 12px;
        }
        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: 12px;
            padding-left: 12px;
            margin-top: 0;
        }
        .fw-medium {
            font-weight: 500;
        }
        .col-12 {
            flex: 0 0 auto;
            width: 100%;
        }
        .col-6 {
            flex: 0 0 auto;
            width: 50%;
        }
        .flex-column {
            flex-direction: column !important;
        }
        .media {
            display: flex;
            align-items: flex-start;
        }
        .media-body {
            flex: 1;
        }
        .align-items-end {
            align-items: flex-end !important;
        }
        .fw-semibold {
            font-weight: 600 !important;
        }
        .mb-0 {
            margin-bottom: 0 !important;
        }
        p {
            margin-bottom: 1.25rem;
            margin-top: 0px;
        }
        .col-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }
        .gap-2 {
            gap: 0.5rem !important;
        }
        .mb-1 {
            margin-bottom: 0.25rem !important;
        }
        .table-borderless>:not(caption)>*>* {
            border-bottom-width: 0;
        }
        .table>:not(caption)>*>* {
            padding: 0.5rem 0.5rem;
            /* background: transparent; */
            border-bottom-width: 1px;
        }
        .text-capitalize {
            text-transform: capitalize !important;
        }
        .mb-5 {
            margin-bottom: 3rem !important;
        }
        .mt-4 {
            margin-top: 1.5rem !important;
        }
        .justify-content-end {
            justify-content: flex-end !important;
        }
        .bg-primary {
            background: #14b19e !important;
        }
        .py-3 {
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        .px-5 {
            padding-right: 3rem !important;
            padding-left: 3rem !important;
        }
        .text-end {
            text-align: right;
        }
        .text-start {
            text-align: left;
        }
        .w-100 {
            width: 100% !important;
        }
        .d-inline-block {
            display: inline-block !important;
        }
        .ml-3 {
            margin-left: 16px !important;
        }
        .text-white {
            color: #fff;
        }
        .py-2 {
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .p-2 {
            padding: 8px !important;
        }
    </style>
</head>

<body class="bg-white" id="printableTable">
    <div class="">
        <table style="width:100%">
            <tbody>
                <tr>
                    <td class="text-uppercase text-center invoice-main-title mb-4">Invoice</td>
                </tr>
            </tbody>
        </table>
        <div class="border-bottom border-primary pb-2 mb-3 text-dark">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td>{{ 'Trip ID' }} #{{$data->ref_id}}</td>
                        <td class="text-end">
                            @php($time_format = getSession('time_format'))
                            <div class="text-end">{{translate('date')}}: {{date(DATE_FORMAT,strtotime(now()))}}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center mb-3">
            <img width="160" src="{{$businessLogo? asset("storage/app/public/business/".$businessLogo):asset('public/assets/admin-module/img/invoice-logo.png')}}" alt="{{$businessName}}">
        </div>

        <div class="pb-4 mb-4">
            <div class="">
                <h4 class="fw-medium text-primary mb-4 text-uppercase">{{translate('trip_distance')}}</h4>

                <table class="w-100">
                    <tbody>
                        <tr>
                            <td class="text-start">
                                <table style="vertical-align:middle">
                                    <tbody>
                                        <tr>
                                            <td style="width:50px">
                                                <img width="33" src="{{asset('public/assets/admin-module/img/media/from.png')}}" alt="">
                                            </td>
                                            <td>
                                                <div class="d-inline-block ml-3 media-body">
                                                    <div class="text-dark" style="font-size: 14px;">
                                                        <strong>{{ translate('pickup_Address') }}</strong>
                                                        ({{ date(DATE_FORMAT,strtotime($data->created_at)).' '. date('h:i A', strtotime($data?->tripStatus?->ongoing)) ?? 'N/a' }})
                                                    </div>
                                                    <div class="fw-medium text-dark" style="max-width: 250px">
                                                        {{ $data?->coordinate?->pickup_address }}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table style="vertical-align:middle; margin-top: 10px">
                                    <tbody>
                                        <tr>
                                            <td style="width:50px">
                                                <img width="33" src="{{asset('public/assets/admin-module/img/media/to.png')}}" alt="">
                                            </td>
                                            <td>
                                                <div class="d-inline-block ml-3 media-body">
                                                    <div class="text-dark" style="font-size: 14px;">
                                                        <strong>{{ translate('destination_Address') }}</strong>
                                                        ({{date(DATE_FORMAT,strtotime($data->created_at)). ' '. date('h:i A', strtotime($data?->tripStatus?->completed ?? $data?->tripStatus?->cancelled)) ?? 'N/a' }})
                                                    </div>
                                                    <div class="fw-medium text-dark" style="max-width: 250px">
                                                        {{ $data?->coordinate?->destination_address }}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td class="text-end">
                                <div class="">
                                    @if($data->current_status == 'completed')
                                        <p class="fz-12 mb-0 fw-semibold">{{$data->actual_distance}} {{translate('kilometers')}}</p>
                                    @else
                                        <p class="fz-12 mb-0 fw-semibold">{{$data->estimated_distance}} {{translate('kilometers')}}</p>
                                    @endif
                                    <p class="fz-12 mb-0">
                                        <?php
                                        use Carbon\Carbon;
                                        $startTime = Carbon::parse($data?->tripStatus?->ongoing);
                                        $endTime = Carbon::parse($data?->tripStatus?->completed ?? $data?->tripStatus?->cancelled);
                                        $timeDifference = $endTime->diff($startTime);
                                        $timeDifferenceString = $timeDifference->format('<strong>%H</strong> hour <strong>%I</strong> Min <strong>%s</strong> sec');
                                        ?>
                                        {!! $timeDifferenceString !!}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border mb-4 p-3" style="background-color: #EDF9F859; border-color: #14b19e29 !important;">
            <div class="text-dark">
                <h4 class="fw-medium text-primary" style="margin:0 0 10px">
                    {{ translate('Payment_Info') }}:
                </h4>
                <table class="w-100">
                    <tbody>
                        <tr>
                            <td>
                                <table>
                                    <tr>
                                        <td style="padding:2px 4px">
                                            <strong style="width: 100px">{{ translate('A.C_Name') }}</strong>
                                        </td>
                                        <td style="padding:2px 4px">
                                            <span>:</span> <span class="fw-medium text-dark">{{ $data?->customer?->first_name }} {{ $data?->customer?->last_name }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:2px 4px">
                                            <strong style="width: 100px">{{ translate('Phone') }}</strong>
                                        </td>
                                        <td style="padding:2px 4px">
                                            <span>:</span> <span class="fw-medium text-dark">{{ $data?->customer?->phone }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table>
                                    <tr>
                                        <td style="padding:2px 4px">
                                            <strong style="width: 100px">{{ translate('Payment_Type') }}</strong>
                                        </td>
                                        <td style="padding:2px 4px">
                                            <span>:</span> <span class="fw-medium text-dark">{{ translate($data->payment_method) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding:2px 4px">
                                            <strong style="width: 100px">{{ translate('Payment_Status') }}</strong>
                                        </td>
                                        <td style="padding:2px 4px">
                                            <span>:</span> <span class="fw-medium text-dark">{{ translate($data->payment_status) }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <table class="table table-borderless table-striped text-dark">
            <thead>
                <tr>
                    <th class="text-uppercase text-primary text-start p-3">{{translate('SL')}}</th>
                    <th class="text-uppercase text-primary text-start p-3">{{translate('cost_description')}}</th>
                    <th class="text-uppercase text-primary text-end p-3">{{translate('price')}}</th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <td class="p-3" >1</td>
                <td class="text-capitalize p-3" >{{translate('trip_cost')}}</td>
                <td class="text-end p-3" >{{set_currency_symbol($data->actual_fare)}}</td>
            </tr>
            <tr>
                <td class="p-3" style="background-color: #e0fffb">2</td>
                <td class="p-3" style="background-color: #e0fffb">{{translate('discount_amount')}}</td>
                <td class="text-end p-3" style="background-color: #e0fffb">- {{set_currency_symbol($data->discount_amount + 0 )}}</td>
            </tr>
            <tr>
                <td class="p-3">3</td>
                <td class="p-3">{{translate('coupon_discount')}}</td>
                <td class="text-end p-3">- {{set_currency_symbol($data->coupon_amount + 0 )}}</td>
            </tr>
            @if($data->type != 'parcel')
                <tr>
                    <td class="p-3" style="vertical-align:top;background-color: #e0fffb">4</td>
                    <td class="p-3" style="line-height:1.8;background-color: #e0fffb">
                        <div>{{translate('Additional_Fee')}}</div>
                        <div>{{translate('delay_fee')}}</div>
                        <div>{{translate('idle_fee')}}</div>
                        <div>{{translate('cancellation_fee')}}</div>
                    </td>
                    <td class="text-end p-3" style="line-height:1.8;background-color: #e0fffb">
                        <div>+ {{set_currency_symbol($data?->fee?->delay_fee+$data?->fee?->idle_fee+$data?->fee?->cancellation_fee)}}</div>
                        <div>{{set_currency_symbol($data?->fee?->delay_fee)}}</div>
                        <div>{{set_currency_symbol($data?->fee?->idle_fee)}}</div>
                        <div>{{set_currency_symbol($data?->fee?->cancellation_fee)}}</div>
                    </td>
                </tr>
            @endif
            <?php
            $totalAmount = $data->actual_fare + ($data?->fee?->delay_fee ?? 0) + ($data?->fee?->idle_fee ?? 0) + ($data?->fee?->cancellation_fee ?? 0) - ($data->coupon_amount ?? 0)-($data->discount_amount ?? 0);
            ?>
            <tr>
                <td class="p-3" style="background-color: @if($data->type == 'parcel') #e0fffb @endif">{{ $data->type == 'parcel' ? 4:5 }}</td>
                <td class="p-3" style="background-color: @if($data->type == 'parcel') #e0fffb @endif">{{translate('VAT/Tax')}} <small class="font-semi-bold"><strong>({{ round((($data?->fee?->vat_tax ?? 0) * 100) / $totalAmount ?? 0) }}%)</strong></small></td>
                <td class="text-end p-3" style="background-color: @if($data->type == 'parcel') #e0fffb @endif">+ {{set_currency_symbol($data?->fee?->vat_tax + 0)}}</td>
            </tr>
            @if($data->tips>0)
                <tr>
                    <td class="p-3" style="background-color: @if($data->type != 'parcel') #e0fffb @endif">{{ $data->type == 'parcel' ? 5:8 }}</td>
                    <td class="p-3" style="background-color: @if($data->type != 'parcel') #e0fffb @endif">{{translate('tips')}}</td>
                    <td class="text-end p-3" style="background-color: @if($data->type != 'parcel') #e0fffb @endif">+ {{set_currency_symbol($data->tips + 0)}}</td>
                </tr>
            @endif
            </tbody>
        </table>

        <table style="width:100%">
            <tr>
                <td></td>
                <td></td>
                <td class="text-right">
                    <span class="bg-primary" style="display:inline-block;padding:8px 25px">
                        <span class="fw-semibold text-white">{{translate('total')}}:</span>
                        <span class="fw-semibold text-white">{{set_currency_symbol($data->paid_fare)}}</span>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="height:10px"></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        {{-- <p><span class="text-primary">{{translate('Visit the trip')}}</span>
            <strong>{{translate('page for more information')}}, {{translate('including invoices')}}
                ({{translate('where available')}})</strong></p> --}}
        <p class="text-dark">
            {{ translate('Thank_you_for_choosing') }} {{ $businessName }}.
            {{ translate('please') }} <a href="{{ url('/') }}" class="text-primary">
                {{ translate('contact_us') }}</a> {{ translate('for any queries') }}.
        </p>
        {{-- <p>{{translate('Fare does not include fees that may be charged by your bank')}}
            . {{translate('Please contact your bank directly for inquiries')}}.</p> --}}

        <div class="p-3" style="background-color: #e0fffb">
            <table class="w-100">
                <tbody>
                    <tr>
                        <td class="text-start">
                            <a href="{{ url('/') }}">{{ url('/') }}</a>
                        </td>
                        <td class="text-center">
                            <a href="tel:{{ $businessContactPhone }}">{{ $businessContactPhone }}</a>
                        </td>
                        <td class="text-end">
                            <a href="mailto:{{ $businessContactEmail }}">
                                {{ $businessContactEmail }}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

<script>
    window.print();
</script>
</html>

