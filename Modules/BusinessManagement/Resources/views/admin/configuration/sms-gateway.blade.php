@extends('adminmodule::layouts.master')

@section('title', translate('SMS_Gateways'))

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{ translate('3rd_party') }}</h2>
            @include('businessmanagement::admin.configuration.partials._third_party_inline_menu')
            <style>
                .card {
                    display: flex;
                    flex-direction: column;
                    height: 100%;
                }

                .card-body {
                    flex: 1;
                    /* This makes the card body fill the available space */
                }
            </style>
            <div class="main-content">
                <!-- Tab Content -->
                <div class="row">
                    <div class="col-md-6 mb-30 ">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="page-title">SparrowSMS</h4>
                            </div>
                            <div class="card-body p-30">
                                <form action="#" method="POST" id="twilio-form" enctype="multipart/form-data">
                                    <div class="discount-type">
                                        <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                            <div class="custom-radio">
                                                <input type="radio" id="twilio-active" name="status" value="1"
                                                    checked>
                                                <label for="twilio-active">Active</label>
                                            </div>
                                        </div>

                                        <input name="gateway" value="twilio" class="d-none">
                                        <input name="mode" value="live" class="d-none">

                                        <div class="   mb-30 mt-30">
                                            <label for="exampleFormControlInput1" class="form-label">Sparrow Token
                                                *</label>
                                            <input type="text" class="form-control" name="sid" placeholder="Sparrow Token *"
                                                value="{{ env('SPARROW_TOKEN') }}" disabled>
                                        </div>
                                        <div class="   mb-30 mt-30">
                                            <label for="exampleFormControlInput1" class="form-label">Sparrow Sender Name
                                                *</label>
                                            <input type="text" class="form-control" name="messaging_service_sid"
                                                placeholder="Sparrow Sender Name *" value="{{ env('SPARROW_SENDER') }}" disabled>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- @foreach ($dataValues as $gateway)
                        <div class="col-md-6 mb-30 ">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="page-title">{{ucfirst(str_replace('_',' ',$gateway->key_name))}}</h4>
                                </div>
                                <div class="card-body p-30">
                                    <form
                                        action="{{route('admin.business.configuration.third-party.sms-gateway.update')}}"
                                        method="POST"
                                        id="{{$gateway->key_name}}-form" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="discount-type">
                                            <div class="d-flex align-items-center gap-4 gap-xl-5 mb-30">
                                                <div class="custom-radio">
                                                    <input type="radio" id="{{$gateway->key_name}}-active" name="status"
                                                           value="1" {{$dataValues->where('key_name',$gateway->key_name)->first()->live_values['status']?'checked':''}}>
                                                    <label
                                                        for="{{$gateway->key_name}}-active">{{translate('active')}}</label>
                                                </div>
                                                <div class="custom-radio">
                                                    <input type="radio" id="{{$gateway->key_name}}-inactive"
                                                           name="status"
                                                           value="0" {{$dataValues->where('key_name',$gateway->key_name)->first()->live_values['status']?'':'checked'}}>
                                                    <label
                                                        for="{{$gateway->key_name}}-inactive">{{translate('inactive')}}</label>
                                                </div>
                                            </div>

                                            <input name="gateway" value="{{$gateway->key_name}}" class="d-none">
                                            <input name="mode" value="live" class="d-none">

                                            @php($skip=['gateway','mode','status'])
                                            @foreach ($dataValues->where('key_name', $gateway->key_name)->first()->live_values as $key => $value)
                                                @if (!in_array($key, $skip))
                                                    <div class="   mb-30 mt-30">
                                                        <label for="exampleFormControlInput1"
                                                               class="form-label">{{ucfirst(str_replace('_',' ',$key))}}
                                                            *</label>
                                                        <input type="text" class="form-control"
                                                               name="{{$key}}"
                                                               placeholder="{{ucfirst(str_replace('_',' ',$key))}} *"
                                                               value="{{env('APP_MODE')=='demo'?'':$value}}">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary demo_check">
                                                {{translate('update')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach --}}
                </div>
                <!-- End Tab Content -->
            </div>

        </div>
    </div>
    <!-- End Main Content -->
@endsection
