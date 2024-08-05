<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Configuration</title>
        @stack('css')
    </head>
    <body>
        @yield('payment')
        <script src="{{asset('public/assets/modules/select2/select2.min.js')}}"></script>
        @stack('script_2')
    </body>
</html>
