@extends('installation.layouts.master')
@section('title', 'Congratulations!')
@section('content')
    @include('installation.layouts.title')

    <!-- Card -->
    <div class="card mt-4">
        <div class="p-4 mb-md-3 mx-xl-4 px-md-5">
            <div class="p-4 rounded mb-4 text-center">
                <h5 class="fw-normal pb-5">{{translate('Configure the following setting to run the system properly')}}</h5>

                <ul class="list-group mar-no mar-top bord-no">
                    <li class="list-group-item">{{translate('Business Setting')}}</li>
                    <li class="list-group-item">{{translate('MAIL Setting')}}</li>
                    <li class="list-group-item">{{translate('Payment Gateway Configuration')}}</li>
                    <li class="list-group-item">{{translate('SMS Gateway Configuration')}}</li>
                    <li class="list-group-item">{{translate('3rd Party APIs')}}</li>
                </ul>
            </div>

            <div class="d-flex justify-content-center">
                <a href="{{ env('APP_URL') }}" target="_blank" class="btn btn-secondary px-sm-5 me-2">{{translate('Landing Page')}}</a>
                <a href="{{ env('APP_URL') }}/admin/auth/login" target="_blank" class="btn btn-dark px-sm-5">{{translate('Admin Panel')}}</a>
            </div>
        </div>
    </div>
@endsection

