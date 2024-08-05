<div class="row justify-content-center g-3">
    <div class="col-lg-3 col-md-3 col-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
                    <div class="analytical_data-icon mb-0 rounded">
                        <img src="{{asset('public/assets/admin-module/img/svg/total_customer.svg')}}" class="svg" alt="">
                    </div>
                    <h3 class="fs-21 text-primary">{{$totalCustomers}}</h3>
                </div>
                <div class="text-muted mb-1">{{translate('total')}}</div>
                <h6 class="fw-semibold text-capitalize">{{translate('total_customer')}}</h6>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-3 col-6">
        <div class="card analytical_data analytical_data-color3">
            <div class="card-body">
                <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
                    <div class="analytical_data-icon mb-0 rounded">
                        <img src="{{asset('public/assets/admin-module/img/svg/inactive_customer.svg')}}" class="svg" alt="">
                    </div>
                    <h3 class="fs-21 text-primary">{{$inactive}}</h3>
                </div>
                <div class="text-muted mb-1">{{translate('total')}}</div>
                <h6 class="fw-semibold text-capitalize">{{translate('in-active_customer')}}</h6>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-6">
        <div class="card analytical_data analytical_data-color4">
            <div class="card-body">
                <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
                    <div class="analytical_data-icon mb-0 rounded">
                        <img src="{{asset('public/assets/admin-module/img/svg/new_customer.svg')}}" class="svg" alt="">
                    </div>
                    <h3 class="fs-21 text-primary">{{$newCustomers}}</h3>
                </div>
                <div class="text-muted mb-1">{{translate('total')}}</div>
                <h6 class="fw-semibold text-capitalize">{{translate('new_customer')}}</h6>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-6">
        <div class="card analytical_data analytical_data-color5">
            <div class="card-body">
                <div class="d-flex align-items-end justify-content-between gap-3 mb-4">
                    <div class="analytical_data-icon mb-0 rounded">
                        <img src="{{asset('public/assets/admin-module/img/svg/active_customer.svg')}}" class="svg" alt="">
                    </div>
                    <h3 class="fs-21 text-primary">{{$active}}</h3>
                </div>
                <div class="text-muted mb-1">{{translate('total')}}</div>
                <h6 class="fw-semibold text-capitalize">{{translate('active_customer')}}</h6>
            </div>
        </div>
    </div>
</div>
