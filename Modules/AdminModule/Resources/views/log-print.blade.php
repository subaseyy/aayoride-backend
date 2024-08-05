<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Banner_Activity_Log') }}</title>
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/css/style.css') }}"/>
    <style>
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
        <h4 class="col-12 fw-medium text-primary mb-2 text-capitalize">{{ translate('print_activity_log') }}</h4>
    </div>

    <table class="table table-borderless table-striped custom-table">
        <thead>
        <tr>
            <th class="text-capitalize">{{ translate('edited_date') }}</th>
            <th class="text-capitalize">{{ translate('edited_time') }}</th>
            <th class="text-capitalize">{{ translate('edited_by') }}</th>
            <th class="text-capitalize">{{ translate('edited_object') }}</th>
            <th class="text-capitalize">{{ translate('before_edit_status') }}</th>
            <th class="text-capitalize">{{ translate('after_edit_status') }}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($data as $d)
            <tr>
                <td>{{ $d['edited_date'] }}</td>
                <td>{{ $d['edited_time'] }}</td>
                <td>{{ $d['email'] }}</td>
                <td>{{ $d['edited_object'] }}</td>
                <td class="word-break">{{ $d['before'] }}</td>
                <td class="word-break">{{ $d['after'] }}</td>
            </tr>
        @empty
        @endforelse
        </tbody>
    </table>

    <p>{{ translate('note:_this_is_software_generated_copy') }}</p>
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
