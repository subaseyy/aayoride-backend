<div class="row justify-content-center g-2 g-xl-3">
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card border text-center analytical_data">
            <div class="card-body">
                <div class="analytical_data-icon rounded-circle mx-auto">
                    <img src="{{asset('public/assets/admin-module/img/svg/total_customer.svg')}}" class="svg" alt="">
                </div>
                <h3 class="analytical_data-count">{{$total}}</h3>
                <div class="fw-semibold text-capitalize">{{translate('total_driver')}}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card border text-center analytical_data analytical_data-color2">
            <div class="card-body">
                <div class="analytical_data-icon rounded-circle mx-auto">
                    <img src="{{asset('public/assets/admin-module/img/svg/active_customer.svg')}}" class="svg" alt="">
                </div>
                <h3 class="analytical_data-count">{{$active}}</h3>
                <div class="fw-semibold text-capitalize">{{translate('active_driver')}}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card border text-center analytical_data analytical_data-color3">
            <div class="card-body">
                <div class="analytical_data-icon rounded-circle mx-auto">
                    <img src="{{asset('public/assets/admin-module/img/svg/inactive_customer.svg')}}" class="svg" alt="">
                </div>
                <h3 class="analytical_data-count">{{$inactive}}</h3>
                <div class="fw-semibold text-capitalize">{{translate('inactive_driver')}}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card border text-center analytical_data analytical_data-color4">
            <div class="card-body">
                <div class="analytical_data-icon rounded-circle mx-auto">
                    <img src="{{asset('public/assets/admin-module/img/svg/new_customer.svg')}}" class="svg" alt="">
                </div>
                <h3 class="analytical_data-count">{{$car}}</h3>
                <div class="fw-semibold text-capitalize">{{translate('car_driver')}}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-3 col-md-4 col-6">
        <div class="card border text-center analytical_data analytical_data-color5">
            <div class="card-body">
                <div class="analytical_data-icon rounded-circle mx-auto">
                    <img src="{{asset('public/assets/admin-module/img/svg/loyal_customer.svg')}}" class="svg" alt="">
                </div>
                <h3 class="analytical_data-count">{{$motor_bike}}</h3>
                <div class="fw-semibold text-capitalize">{{translate('motorbike_driver')}}</div>
            </div>
        </div>
    </div>
</div>
