<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/css/style.css')}}"/>
    <style>
        .invoice-container {
            background-color: var(--bs-white);
            padding: 2.5rem 1.875rem;
            margin: 0.5rem auto;
            max-inline-size: 43.75rem;
        }
    </style>
</head>

<body>
<div class="container" id="printableTable">


    <div class="row mb-4">
        <h4 class="col-12 fw-medium text-primary mb-2">{{ translate('coupon_List') }}</h4>
    </div>

    <table class="table table-borderless table-striped">
        <thead>
        <tr>
            <th class="text-uppercase text-primary">{{translate('SL')}}</th>
            <th class="text-uppercase text-primary">{{translate('coupon_title')}}</th>
            <th class="text-uppercase text-primary">{{translate('coupon_type')}}</th>
            <th class="text-uppercase text-primary">{{translate('coupon_amount')}}</th>
            <th class="text-uppercase text-primary">{{translate('duration')}}</th>
            <th class="text-uppercase text-primary">{{translate('total_times_used')}}</th>
            <th class="text-uppercase text-primary">{{translate('total_coupon')}}</th>
            <th class="text-uppercase text-primary">{{translate('average_coupon')}}</th>
            <th class="text-uppercase text-primary">{{translate('coupon_status')}}</th>
            <th class="text-uppercase text-primary">{{translate('status')}}</th>
            <th class="text-uppercase text-primary">{{translate('created_at')}}</th>
        </tr>
        </thead>
        <tbody>
        @php($time_format = getSession('time_format'))
        @foreach($data as $key => $d)
            <tr>
                <td>{{++$key}}</td>
                <td>{{$d['name']}}</td>
                <td>{{$d['coupon_type']}}</td>
                <td>{{ $d['amount_type'] == 'percentage'? $d['coupon'].'%':'$'.$d['coupon'] }}</td>
                <td>
                    {{translate('start')}} : {{$d['start_date']}} <br>
                    {{translate('end')}} : {{$d['end_date']}} <br>
                    {{translate('duration')}}
                    : {{ Carbon\Carbon::parse($d['end_date'])->diffInDays($d['start_date'])}} Days
                </td>
                <td>{{ (int)$d['total_used'] }}</td>
                <td>{{ set_currency_symbol(round($d['total_amount'],2)) }}</td>
                <td>{{ set_currency_symbol(round($d['total_used'] > 0?($d['total_amount']/$d['total_used']):0,2)) }}</td>
                <td>
                    @php($date = Carbon\Carbon::now()->startOfDay())
                    @if($date->gt($coupon['end_date']))
                        <span
                            class="badge badge-danger">{{ translate(EXPIRED) }}</span>
                    @elseif (!$coupon['is_active'])
                        <span
                            class="badge badge-warning">{{ translate(CURRENTLY_OFF) }}</span>
                    @elseif ($date->lt($d['start_date']))
                        <span class="badge badge-info">{{ translate(UPCOMING) }}</span>

                    @elseif ($date->lte($d['end_date']))
                        <span class="badge badge-success">{{ translate(RUNNING) }}</span>
                    @endif
                </td>
                <td>{{$d['is_active'] ? 'active' : 'inactive'}}</td>
                <td>{{date(DATE_FORMAT, strtotime($d['created_at']))}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    <p>{{translate('note:_this_is_software_generated_copy')}}</p>
</div>

<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>

</body>

</html>

<script>
    "use strict";
    window.frames["print_frame"].document.body.innerHTML = document.getElementById("printableTable").innerHTML;
    window.frames["print_frame"].window.focus();
    window.frames["print_frame"].window.print();

</script>
