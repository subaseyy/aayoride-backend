<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{asset('public/assets/installation/assets/img/favicon.png')}}">

    <!-- Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/css/fonts/google.css')}}"/>

    <link rel="stylesheet" href="{{asset('public/assets/installation/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/installation/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/css/toastr.css')}}"/>

</head>

<body>
<section style="background-image: url('{{asset('public/assets/installation')}}/assets/img/page-bg.png')"
         class="w-100 min-vh-100 bg-img position-relative py-5">

    <!-- Logo -->
    <div class="logo">
        <img src="{{asset('public/assets/installation')}}/assets/img/favicon.svg" alt="">
    </div>

    <div class="custom-container">
        @yield('content')

        <!-- Footer -->
        <footer class="footer py-3 mt-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 align-items-center">
                <div class="footer-logo">
                    <img src="{{asset('public/assets/installation')}}/assets/img/logo.svg" width="150" alt="">
                </div>
                <p class="copyright-text mb-0">© {{date("Y")}} | {{translate('All Rights Reserved')}}</p>
            </div>
        </footer>
    </div>
</section>


<!-- Script Goes Here -->
<script src="{{asset('public/assets/installation/assets/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('public/assets/installation/assets/js/script.js')}}"></script>
<script src="{{asset('public/assets/admin-module/js/jquery-3.6.0.min.js')}}"></script>
<script src="{{asset('public/assets/admin-module/js/toastr.js')}}"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        "use strict";
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif
@stack('script')
</body>
</html>
