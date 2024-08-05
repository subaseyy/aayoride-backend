<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ translate('Employee_Role_List') }}</title>
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
        #printableTable {
            background-color: var(--bs-white);
            padding: 1.875rem; /* Adjusted padding */
            margin: 1rem auto;
            max-width: 595px; /* Set to A4 width in pixels (210mm converted to pixels) */
            overflow-x: auto; /* Add this property to enable horizontal scrolling if needed */
        }

        .custom-table {
            width: 100%; /* Set the table width to 100% */
        }

        .custom-table th,
        .custom-table td {
            word-wrap: break-word; /* Allow word wrapping within table cells */
            max-width: 120px; /* Adjusted max-width for better fit */
        }
    </style>
</head>

<body>
<div class="container" id="printableTable">


<div class="row mb-4">
    <h4 class="col-12 fw-medium text-primary mb-2">Employee List</h4>
</div>

<table class="table table-borderless table-striped custom-table">
    <thead>
    <tr>
        <th class="text-uppercase text-primary">SL</th>
        <th class="text-uppercase text-primary">Role Name</th>
        <th class="text-uppercase text-primary">Modules</th>
        <th class="text-uppercase text-primary">Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $key => $d)
        <tr>
            <td>{{++$key}}</td>
            <td>{{$d['name']}}</td>
            <td class="word-break">{{$d['modules']}}</td>
            <td>{{$d['status']}}</td>
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
