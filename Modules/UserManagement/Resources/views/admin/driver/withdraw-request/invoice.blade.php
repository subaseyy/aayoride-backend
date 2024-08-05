<!DOCTYPE html>
<html lang="en">
@php($businessName = businessConfig('business_name', 'business_information')?->value)
@php($businessLogo = businessConfig('header_logo', 'business_information')?->value)
@php($businessContactEmail = businessConfig('business_contact_email', 'business_information')?->value)
@php($businessContactPhone = businessConfig('business_contact_phone', 'business_information')?->value)
@php($businessAddress = businessConfig('business_address', 'business_information')?->value)

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
        *, ::after, ::before {
            box-sizing: border-box;
        }
        * {
            font-weight: 500;
            font-family: "Inter", sans-serif;
            margin: 0;
            padding: 0;
        }
        body , html {
            font-size: 10px;
        }
        .text-center {
            text-align: center
        }
        .text-end {
            text-align: right;
        }
        .fs-18 {
            font-size: 18px !important;
        }
        .fw-bold {
            font-weight: bold !important;
        }
        .mb-1 {
            margin-bottom: 4px !important;
        }
        .mb-2 {
            margin-bottom: 8px !important;
        }
        .mb-3 {
            margin-bottom: 16px !important;
        }

        .border {
            border: 1px solid #D7DAE0;
        }

        .border-primary {
            border-color: rgba(3, 157, 85, 0.20) !important;
        }

        .rounded {
            border-radius: 4px !important;
        }

        .p-10 {
            padding: 10px !important;
        }
        .w120px {
            width: 120px !important;
        }
        .fw-semibold {
            font-weight: 600;
        }
        .d-inline-block {
            display: inline-block;
        }


        table {
            width: 100% !important;
            border-collapse: collapse;
        }
        table th {
            text-align: start;
            text-transform: uppercase;
            font-size: 9px;
            background: rgba(3, 157, 85, 0.05);
            font-weight: 500;
        }
        table th,
        table td {
            padding: 15px;
        }
        .td-border-bottom tbody td {
            border-bottom: 1px solid #D7DAE0;
        }
        footer {
            border-top: 0.5px solid #EBEDF2;
            background-color: #F2F4F7;
        }
        .min-h80vh {
            min-height: 80vh !important;
        }
        .calc-table {
            max-width: 500px;
            margin-left: auto;
        }
        .mt-3 {
            margin-top: 16px !important;
        }
    </style>
</head>

<body class="bg-white" id="printableTable">
    <div class="mb-3">
        <table>
            <tbody>
                <tr>
                    <td>
                        <h3 class="fs-18 fw-bold mb-2">{{ translate("Withdraw Request") }}</h3>
                        <p>{{ Carbon\Carbon::now()->format('d F, Y g:i a')}}</p>
                    </td>
                    <td class="text-end">
                        <div>
                            <img width="160" src="{{$businessLogo? asset("storage/app/public/business/".$businessLogo):asset('public/assets/admin-module/img/invoice-logo.png')}}" alt="{{$businessName}}">
                            <p>{{ $businessAddress ?? "Business address City, State, IN - 000 000" }}</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>


    </div>

    <div class="border bg-white rounded p-10 mb-2 min-h80vh">
        <table class="td-border-bottom">
            <thead class="border border-primary rounded">
                <tr>
                    <th>{{translate('SL')}}</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Payment Info')}}</th>
                    <th>{{translate('Request Date')}}</th>
                    <th>{{translate('Status')}}</th>
                    <th>{{translate('Amount')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($data as $key => $singleData)
                <tr>
                    <td>{{$key+1}}</td>
                    <td><div class="fw-semibold">{{ $singleData->user ? $singleData?->user?->full_name : translate("Driver not found") }}</div></td>
                    <td>
                        <div class="mb-1">
                            <span class="w120px d-inline-block">{{translate("Payment Method")}}:</span>
                            <span class="fw-semibold">{{ $singleData->method ? $singleData->method->method_name : translate("Method not available")}}</span>
                        </div>
                        @foreach($singleData->method_fields as $keyData => $value)
                            <div class="mb-1">
                                <span class="w120px d-inline-block">{{ucfirst(str_replace('_',' ',$keyData))}}:</span>
                                <span class="fw-semibold">{{$value}}</span>
                            </div>
                        @endforeach

                    </td>
                    <td>
                        <div class="mb-1">{{ Carbon\Carbon::parse($singleData->created_at)->format('d F, Y') }}</div>
                        <div>{{ Carbon\Carbon::parse($singleData->created_at)->format('g:i a') }}</div>
                    </td>
                    <td><div class="fw-semibold">{{ ucfirst(str_replace('_',' ',$singleData->status)) }}</div></td>
                    <td>{{ set_currency_symbol($singleData->amount) }}</td>
                </tr>
            @endforeach
            </tbody>
            @if(count($data)>0)
                <tfoot>
                <tr>
                    <td colspan="4"></td>
                    <td><div class="fs-18">{{ translate("Total") }}</div></td>
                    <td><div class="fs-18">{{set_currency_symbol($data->sum('amount'))}}</div></td>
                </tr>
                </tfoot>
            @endif
        </table>
    </div>

    <footer class="mt-3">
        <table>
            <tbody>
                <tr>
                    <td>{{env('APP_URL')}}</td>
                    <td><div class="text-center">{{$businessContactPhone?? "+91 00000 00000"}}</div></td>
                    <td class="text-end"><div>{{$businessContactEmail??"hello@email.com"}}</div></td>
                </tr>
            </tbody>
        </table>
    </footer>
</body>

<script>
    window.print();
</script>
</html>

