<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/css/style.css')}}" />
    <style>
    </style>
</head>

<body>
<div class="container">
<div class="" id="printableTable">
    <div class="row mb-4">
        <h4 class="col-12 fw-medium text-primary mb-2">{{ translate('trip_list') }}</h4>
    </div>
    <table class="table table-borderless table-striped">
        <thead>
        <tr>
            <th class="text-uppercase text-primary text-center">{{ translate('SL')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('trip_ID')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('date')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('customer')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('driver')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('trip_cost')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('coupon_discount')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('additional_fee')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('cost')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('commission')}}</th>
            <th class="text-uppercase text-primary text-center">{{ translate('trip_status')}}</th>
        </tr>
        </thead>
        <tbody>
            @php($time_format = getSession('time_format'))
        @foreach($data as $key => $d)
        <tr>
            <td class="text-center">{{++$key}}</td>
            <td class="text-center">{{$d['trip_ID']}}</td>
            <td class="text-center">{{$d['date']}}</td>
            <td class="text-center">{{$d['customer']}}</td>
            <td class="text-center">{{$d['driver']}}</td>
            <td class="text-center">{{$d['trip_cost']}}</td>
            <td class="text-center">{{$d['coupon_discount']}}</td>
            <td class="text-center">{{$d['additional_fee']}}</td>
            <td class="text-center">{{$d['total_trip_cost']}}</td>
            <td class="text-center">{{$d['admin_commission']}}</td>
            <td class="text-center">{{$d['trip_status']}}</td>
        </tr>
        @endforeach

        </tbody>
    </table>
    <p>{{ translate('note:_this_is_software_generated_copy')}}</p>
</div>
</div>
<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>
</body>
</html>

<script>
        window.frames["print_frame"].document.body.innerHTML = document.getElementById("printableTable").innerHTML;
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
</script>
