@extends('adminmodule::layouts.master')

@section('title', translate('Add_Driver_Level'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/swiper@11/swiper-bundle.min.css') }}"/>

@endpush
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">{{translate('add_driver_level')}}</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#getInformationModal">
                    How it works
                    <div class="ripple-animation" data-toggle="modal" data-target="#getInformationModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"
                             class="svg replaced-svg">
                            <path d="M9.00033 9.83268C9.23644 9.83268 9.43449 9.75268 9.59449 9.59268C9.75449 9.43268 9.83421 9.2349 9.83366 8.99935V5.64518C9.83366 5.40907 9.75366 5.21463 9.59366 5.06185C9.43366 4.90907 9.23588 4.83268 9.00033 4.83268C8.76421 4.83268 8.56616 4.91268 8.40616 5.07268C8.24616 5.23268 8.16644 5.43046 8.16699 5.66602V9.02018C8.16699 9.25629 8.24699 9.45074 8.40699 9.60352C8.56699 9.75629 8.76477 9.83268 9.00033 9.83268ZM9.00033 13.166C9.23644 13.166 9.43449 13.086 9.59449 12.926C9.75449 12.766 9.83421 12.5682 9.83366 12.3327C9.83366 12.0966 9.75366 11.8985 9.59366 11.7385C9.43366 11.5785 9.23588 11.4988 9.00033 11.4993C8.76421 11.4993 8.56616 11.5793 8.40616 11.7393C8.24616 11.8993 8.16644 12.0971 8.16699 12.3327C8.16699 12.5688 8.24699 12.7668 8.40699 12.9268C8.56699 13.0868 8.76477 13.1666 9.00033 13.166ZM9.00033 17.3327C7.84755 17.3327 6.76421 17.1138 5.75033 16.676C4.73644 16.2382 3.85449 15.6446 3.10449 14.8952C2.35449 14.1452 1.76088 13.2632 1.32366 12.2493C0.886437 11.2355 0.667548 10.1521 0.666992 8.99935C0.666992 7.84657 0.885881 6.76324 1.32366 5.74935C1.76144 4.73546 2.35505 3.85352 3.10449 3.10352C3.85449 2.35352 4.73644 1.7599 5.75033 1.32268C6.76421 0.88546 7.84755 0.666571 9.00033 0.666016C10.1531 0.666016 11.2364 0.884905 12.2503 1.32268C13.2642 1.76046 14.1462 2.35407 14.8962 3.10352C15.6462 3.85352 16.24 4.73546 16.6778 5.74935C17.1156 6.76324 17.3342 7.84657 17.3337 8.99935C17.3337 10.1521 17.1148 11.2355 16.677 12.2493C16.2392 13.2632 15.6456 14.1452 14.8962 14.8952C14.1462 15.6452 13.2642 16.2391 12.2503 16.6768C11.2364 17.1146 10.1531 17.3332 9.00033 17.3327ZM9.00033 15.666C10.8475 15.666 12.4206 15.0168 13.7195 13.7185C15.0184 12.4202 15.6675 10.8471 15.667 8.99935C15.667 7.15213 15.0178 5.57907 13.7195 4.28018C12.4212 2.98129 10.8481 2.33213 9.00033 2.33268C7.1531 2.33268 5.58005 2.98185 4.28116 4.28018C2.98227 5.57852 2.3331 7.15157 2.33366 8.99935C2.33366 10.8466 2.98283 12.4196 4.28116 13.7185C5.57949 15.0174 7.15255 15.6666 9.00033 15.666Z"
                                  fill="currentColor"></path>
                        </svg>
                    </div>
                </button>
            </div>
            <div class="alert alert--primary d-flex align-items-center alert-dismissible gap-1 justify-content-center"
                 role="alert" id="driverLevelHint">
                <img src="{{asset('public/assets/admin-module/img/hint.png')}}" alt="hint" width="18" height="18">
                <div class="w-50px flex-grow-1">
                    {{translate('At present, there is only one level available, which is the default level. When a driver logs in to the app for the first time, they will automatically be assigned the default level.')}}
                </div>
                <button type="button" class="btn-close" id="hintModal" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.driver.level.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-capitalize mb-2">{{translate('Current Level info')}}</h5>
                        <p>{{translate('The Current Level setup automatically assigns drivers the default level upon their initial app login')}}</p>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-xl-10">
                                <div class="card">
                                    <div class="card-body p-xl-5 ">
                                        <div class="row g-4 justify-content-between align-items-center">
                                            <div class="col-sm-6 col-xl-5">
                                                <div class="mb-4">
                                                    <label for="select_level_number"
                                                           class="mb-2">{{translate('select_level_sequence')}}</label>
                                                    <select class="js-select" name="sequence" id="select_level_number"
                                                            required>
                                                        @foreach($sequences as $key=>$sequence)
                                                            @if($key == 0)
                                                                <option
                                                                    value="{{$sequence}}"
                                                                    selected>{{$sequence}}</option>
                                                            @else
                                                                <option
                                                                    value="{{$sequence}}"
                                                                    disabled>{{$sequence}}</option>
                                                            @endif

                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="l_name" class="mb-2">{{translate('level_name')}}</label>
                                                    <input type="text" name="name" value="{{old('name')}}" id="l_name"
                                                           class="form-control"
                                                           placeholder="{{ translate('ex') }}: {{ translate('Platinum') }}"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="d-flex flex-column justify-content-around gap-3">
                                                    <h5 class="text-center text-capitalize">{{translate('level_icon')}}</h5>

                                                    <div class="d-flex justify-content-center">
                                                        <div class="upload-file">
                                                            <input type="file" name="image" class="upload-file__input"
                                                                   accept="image/png" required>
                                                            <div class="upload-file__img w-auto h-auto">
                                                                <img width="150"
                                                                     src="{{asset('public/assets/admin-module/img/media/upload-file.png')}}"
                                                                     alt="">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <p class="opacity-75 mx-auto max-w220 text-center">{{translate('File Format - png
                                                        Image Size - Maximum Size 5 MB.')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="text-capitalize mb-2">{{translate('Set a Deserving Reward and Target for Upgrading to the Next Level.')}}</h5>
                        <p>{{translate('Setting the Stage for Rewards and Targets. Once a target is completed or fulfilled, move on to the next one')}}</p>
                    </div>
                    <div class="card-body pb-5">
                        <div class="row justify-content-center">
                            <div class="col-xl-9">
                                <div class="card mb-4">
                                    <div class="card-header pt-4 border-0 text-center">
                                        <h5 class="text-capitalize mb-2 pt-2">{{translate('Reward Type')}}</h5>
                                        <p>{{translate('The driver will receive that reward amount while completing this level targets')}}</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="px-xxl-3 pb-3">
                                            <div class="bg-input p-4 rounded">
                                                <div class="row align-items-end g-4">
                                                    <div class="col-sm-6">
                                                        <label for="rewardType"
                                                               class="mb-2">{{translate('reward_type')}}</label>
                                                        <select class="js-select" name="reward_type" id="rewardType"
                                                                required>
                                                            <option value="" selected
                                                                    disabled>{{translate('select_reward_type')}}</option>
                                                            <option
                                                                value="{{NO_REWARDS}}">{{translate('no_rewards')}}</option>
                                                            <option value="{{WALLET}}">{{translate('wallet')}}</option>
                                                            <option
                                                                value="{{LOYALTY_POINTS}}">{{translate('loyalty_points')}}</option>
                                                        </select>
                                                    </div>
                                                    <div id="rewardAmountDiv" class="col-sm-6 d-none">
                                                        <label id="rewardAmountLabel" for="rewardAmount"
                                                               class="mb-2">{{translate('reward_amount')}}
                                                            ({{session()->get('currency_symbol') ?? '$'}})</label>
                                                        <input type="number" step=".01" name="reward_amount" max="999999999"
                                                               value="{{old('reward_amount')}}"
                                                               id="rewardAmount" class="form-control"
                                                               placeholder="{{ translate('ex') }}: 500">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header pt-4 border-0 text-center">
                                        <h5 class="text-capitalize mb-2 pt-2">{{translate('Set Target to Promote from This Level')}}</h5>
                                        <p>{{translate('Here are some targets for reaching the next level')}}</p>
                                    </div>
                                    <div class="card-body">
                                        <div class="px-xxl-3 pb-3">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="check-toggle-item">
                                                        <label class="custom-checkbox">
                                                            <input type="checkbox" class="test"
                                                                   id="minimum_ride_complete"
                                                                   name="minimum_ride_complete" checked
                                                                   value="{{old('minimum_ride_complete')}}" {{old('minimum_ride_complete')==1?'checked':''}}>
                                                            {{translate('minimum_ride_complete')}}
                                                        </label>
                                                        <div class="check-toggle-content">
                                                            <div class="bg-input p-4 rounded ms-4">
                                                                <div class="row g-3">
                                                                    <div class="col-sm-6">
                                                                        <div>
                                                                            <input type="number" name="targeted_ride"
                                                                                   value="{{old('targeted_ride')}}"
                                                                                   id="min_ride_complete" min="1"
                                                                                   step="1" max="999999999"
                                                                                   class="form-control"
                                                                                   placeholder="{{ translate('Minimum Ride Number') }}">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div>
                                                                            <input type="number"
                                                                                   name="targeted_ride_point" min="1"
                                                                                   step="1" max="999999999"
                                                                                   value="{{old('targeted_ride_point')}}"
                                                                                   id="points" class="form-control"
                                                                                   placeholder="{{ translate('Points') }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="check-toggle-item">
                                                        <label class="custom-checkbox">
                                                            <input type="checkbox" class="test" id="minimum_earn_amount"
                                                                   name="minimum_earn_amount"
                                                                   value="{{old('minimum_earn_amount')}}" {{old('minimum_earn_amount')==1?'checked':''}}>
                                                            {{translate('minimum_earn_amount')}}
                                                        </label>
                                                        <div class="check-toggle-content">
                                                            <div class="bg-input p-4 rounded ms-4">
                                                                <div class="row g-3">
                                                                    <div class="col-sm-6">
                                                                        <input type="number" step=".01" min=".01"
                                                                               name="targeted_amount" max="999999999"
                                                                               value="{{old('targeted_amount')}}"
                                                                               id="min_earn_amount" class="form-control"
                                                                               placeholder="{{ translate('minimum_earn_amount') }}">
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <input type="number"
                                                                               name="targeted_amount_point" min="1"
                                                                               step="1" max="999999999"
                                                                               value="{{old('targeted_amount_point')}}"
                                                                               id="points2" class="form-control"
                                                                               placeholder="{{ translate('Points') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="check-toggle-item">
                                                        <label class="custom-checkbox">
                                                            <input type="checkbox" class="test"
                                                                   id="maximum_cancellation_rate"
                                                                   name="maximum_cancellation_rate"
                                                                   value="{{old('maximum_cancellation_rate')}}" {{old('maximum_cancellation_rate')==1?'checked':''}}>
                                                            {{translate('maximum_cancellation_rate')}} (%)
                                                        </label>
                                                        <div class="check-toggle-content">
                                                            <div class="bg-input p-4 rounded ms-4">
                                                                <div class="row g-3">
                                                                    <div class="col-sm-6">
                                                                        <input type="number" name="targeted_cancel"
                                                                               value="{{old('targeted_cancel')}}"
                                                                               id="max_cancellation_rate" min="1"
                                                                               max="100" step="1"
                                                                               class="form-control"
                                                                               placeholder="{{ translate('maximum_cancellation_rate') }}">
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <input type="number"
                                                                               name="targeted_cancel_point"
                                                                               value="{{old('targeted_cancel_point')}}"
                                                                               id="points3" class="form-control" min="1"
                                                                               step="1"
                                                                               placeholder="{{ translate('Points') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="check-toggle-item">
                                                        <label class="custom-checkbox">
                                                            <input type="checkbox" class="test"
                                                                   id="minimum_review_receive"
                                                                   name="minimum_review_receive"
                                                                   value="{{old('minimum_review_receive')}}" {{old('minimum_review_receive')==1?'checked':''}}>
                                                            {{translate('minimum_review_receive')}}
                                                        </label>
                                                        <div class="check-toggle-content">
                                                            <div class="bg-input p-4 rounded ms-4">
                                                                <div class="row g-3">
                                                                    <div class="col-sm-6">
                                                                        <input type="number" min="1" step="1"
                                                                               name="targeted_review" max="999999999"
                                                                               value="{{old('targeted_review')}}"
                                                                               id="min_give_review" class="form-control"
                                                                               placeholder="{{ translate('minimum_review_receive') }}">
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <input type="number"
                                                                               name="targeted_review_point" min="1"
                                                                               step="1" max="999999999"
                                                                               value="{{old('targeted_review_point')}}"
                                                                               id="points4" class="form-control"
                                                                               placeholder="{{translate('Points')}}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-3">
                    <button class="btn btn-secondary" type="reset">{{translate('reset')}}</button>
                    <button class="btn btn-primary" type="submit">{{translate('save')}}</button>
                </div>
            </form>
        </div>
    </div>
    <!-- End Main Content -->


    <!-- The Modal -->
    @include('usermanagement::admin.customer.level._info-modal')
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/plugins/swiper@11/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin-module/js/level/level.js') }}"></script>
    <script>
        "use strict";

        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
            },
        });
        $("#hintModal").on('click', function () {
            localStorage.setItem('driver-level-hint', true);
        });
        if (localStorage.getItem('driver-level-hint')) {
            $("#driverLevelHint").addClass("d-none");
        }

        function hideReward() {
            let value = $('#rewardType').find(":selected").val()
            if (value !== 'no_rewards') {
                if (value == 'wallet') {
                    $('#rewardAmountLabel').text('{{ translate('reward_amount') }} ({{session()->get('currency_symbol') ?? '$'}})')
                    $('#rewardAmount').removeAttr('step');
                    $('#rewardAmount').attr('step', '.01');
                    $('#rewardAmount').attr('min', '.01');
                } else {
                    $('#rewardAmountLabel').text('{{ translate('reward_points') }}')
                    $('#rewardAmount').removeAttr('step');
                    $('#rewardAmount').attr('step', '1');
                    $('#rewardAmount').attr('min', '1');
                }
                $('#rewardAmountDiv').removeClass('d-none')
                $('#rewardAmount').attr('required', 'required');

            } else {
                $('#rewardAmountDiv').addClass('d-none')
                $('#rewardAmount').removeAttr('step');
                $('#rewardAmount').removeAttr('required');
            }
        }
    </script>
@endpush
