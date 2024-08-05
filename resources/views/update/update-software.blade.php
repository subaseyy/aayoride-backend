@extends('installation.layouts.master')

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-body">
                    <div class="mar-ver pad-btm text-center mb-4">
                        <h1 class="h3">
                            Software Update
                        </h1>
                        @if(env('SOFTWARE_VERSION') >= 1.4)
                            <div class="alert alert-danger px-1 mt-2" role="alert">
                                {{ translate("Important Notice: We've upgraded the Firebase push notification system to a new and improved version as the old one will be phased out by June 2024.
                                    Make sure your system is up-to-date to keep getting all the notification seamlessly please do check the Notification settings in the Admin panel.
                                    Thanks for staying connected!.")  }}
                                <a class="alert-link" target="_blank" href="https://docs.6amtech.com/docs-grofresh/admin-panel/mandatory-setup#firebase-configuration-for-notification">{{ translate('kindly follow the documentation') }}</a>
                            </div>
                        @endif
                    </div>


                    <form method="POST" action="{{route('update-system')}}">
                        @csrf
                        <div class="bg-light p-4 rounded mb-4">
                            <div class="px-xl-2 pb-sm-3">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <div class="from-group">
                                            <label for="username" class="d-flex align-items-center gap-2 mb-2">
                                                <span class="fw-medium">Username</span>
                                                <span class="cursor-pointer" data-bs-toggle="tooltip"
                                                      data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                      data-bs-html="true"
                                                      data-bs-title="The username of your account">
                                                      <img src="{{asset('public/assets/installation')}}/assets/img/svg-icons/info2.svg" class="svg" alt="">
                                                </span>
                                            </label>
                                            <input type="text" id="username" class="form-control" name="username"
                                                   value="{{env('BUYER_USERNAME')}}"
                                                   placeholder="Ex: John Doe" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="from-group">
                                            <label for="purchase_key" class="mb-2">Purchase Code</label>
                                            <input type="text" id="purchase_key" class="form-control" name="purchase_key"
                                                   value="{{env('PURCHASE_CODE')}}"
                                                   placeholder="Ex: 19xxxxxx-ca5c-49c2-83f6-696a738b0000" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-dark px-sm-5">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
