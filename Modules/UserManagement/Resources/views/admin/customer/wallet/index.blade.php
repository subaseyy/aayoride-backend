@extends('adminmodule::layouts.master')

@section('title', translate('Customer_Wallet'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('user_add')
                <div class="d-flex justify-content-between gap-3 align-items-center mb-4">
                    <h2 class="fs-22 text-capitalize">{{translate('add_fund')}}</h2>
                </div>

                <form action="{{route('admin.customer.wallet.store')}}" method="post" enctype="multipart/form-data"
                      id="formSubmit">
                    @csrf
                    <div class="card">
                        <div class="card-body">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <label for="customer" class="mb-2">{{translate('customer')}}</label>
                                        <select name="customer_id" id="customer" class="js-select-customer">
                                            <option selected disabled>-- {{translate('select_Customer')}}--
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="mb-4">
                                        <label for="amount" class="mb-2">{{translate('amount')}}</label>
                                        <input type="number" name="amount" value="{{old('amount')}}" id="amount"
                                               class="form-control" step=".01" placeholder="Ex: 100" required>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="mb-4">
                                        <label for="points4" class="mb-2">{{translate('reference')}}
                                            ({{translate('optional')}})</label>
                                        <input type="text" name="reference" value="{{old('reference')}}" id="reference"
                                               class="form-control" placeholder="Ex: 800">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-3 mt-3">
                        <button class="btn btn-primary" id="addWallet" type="submit">{{translate('save')}}</button>
                    </div>
                </form>
            @endcan
            <div class="d-flex justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">{{translate('wallet_transaction_report')}}</h2>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="fw-semibold text-primary text-uppercase mb-4">{{translate('filter_data')}}</h6>
                    <form method="get">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="customer1" class="mb-2">{{translate('customer')}}</label>
                                    <select id="customer1" class="js-select-customer_2" name="user_id">
                                        <option selected disabled>-- {{translate('select_Customer')}} --</option>
                                        @if(request()->get('user_id') && request()->get('user_id') == 'all')
                                            <option value="all" selected>All Customer</option>
                                        @endif
                                        @if (request()->get('user_id') && $customer_info = Modules\UserManagement\Entities\User::find(request()->get('user_id')))
                                            <option value="{{$customer_info->id}}"
                                                    selected>{{$customer_info?->first_name.' '.$customer_info?->last_name}}
                                                ({{$customer_info->phone}})
                                            </option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="mb-4">
                                    <label for="dateRange" class="mb-2">{{translate('date_range')}}</label>
                                    <select name="data" id="dateRange" class="js-select">
                                        <option value="0" disabled selected>{{translate('Date_Range')}}</option>
                                        <option
                                            value="all_time" {{!empty(request()->has('data')) && request()->input('data')=='all_time'?'selected':''}}>{{translate('All_Time')}}</option>
                                        <option
                                            value="this_week" {{!empty(request()->has('data')) && request()->input('data')=='this_week'?'selected':''}}>{{translate('This_Week')}}</option>
                                        <option
                                            value="last_week" {{!empty(request()->has('data')) && request()->input('data')=='last_week'?'selected':''}}>{{translate('Last_Week')}}</option>
                                        <option
                                            value="this_month" {{!empty(request()->has('data')) && request()->input('data')=='this_month'?'selected':''}}>{{translate('This_Month')}}</option>
                                        <option
                                            value="last_month" {{!empty(request()->has('data')) && request()->input('data')=='last_month'?'selected':''}}>{{translate('Last_Month')}}</option>
                                        <option
                                            value="custom_date" {{!empty(request()->has('data')) && request()->input('data')=='custom_date'?'selected':''}}>{{translate('Custom_Date')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div
                                class="col-sm-6 {{!empty(request()->has('data')) && request()->input('data')=='custom_date'?'':'d-none'}}"
                                id="fromFilterDiv">
                                <label for="from">{{translate('From')}}</label>
                                <input type="date" class="form-control" id="from" name="start"
                                       value="{{!empty(request()->has('data')) && request()->input('data')=='custom_date' ?request()->input('start'):''}}">
                            </div>
                            <div
                                class="col-sm-6 {{!empty(request()->has('data')) && request()->input('data')=='custom_date'?'':'d-none'}}"
                                id="toFilterDiv">
                                <label for="to">{{translate('To')}}</label>
                                <input type="date" class="form-control" id="to" name="end"
                                       value="{{!empty(request()->has('data')) && request()->input('data')=='custom_date' ?request()->input('end'):''}}">
                            </div>

                        </div>
                        <div class="d-flex justify-content-end gap-3 mt-3">
                            <button class="btn btn-primary date-range-submit" data-url="{{ url()->full() }}"
                                    type="submit">{{translate('filter')}}</button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="tab-content mt-3">
                <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="javascript:;" method="GET" class="search-form search-form_style-two">
                                    <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        <input type="search" name="search" value="{{ request()->get('search') }}" id="search"
                                               class="theme-input-style search-form__input"
                                               placeholder="{{translate('Search_here_by_Transaction_Id')}}">
                                    </div>
                                    <button type="submit" class="btn btn-primary search-submit"
                                            data-url="{{ url()->full() }}">{{translate('search')}}</button>
                                </form>
                                @can('user_export')
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-download"></i>
                                                {{translate('download')}}
                                                <i class="bi bi-caret-down-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('admin.customer.wallet.export', ['file' => 'excel', request()->getQueryString()]) }}">
                                                        {{ translate('excel') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endcan
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle table-hover text-nowrap">
                                    <thead class="table-light align-middle text-capitalize">
                                    <tr>
                                        <th class="sl">{{translate('SL')}}</th>
                                        <th class="text-center">{{translate('transaction_id')}}</th>
                                        <th class="text-center">{{translate('reference')}}</th>
                                        <th class="text-center">{{translate('transaction_date')}}</th>
                                        <th class="text-center">{{translate('customer_name')}}</th>
                                        <th class="text-center">{{translate('debit')}}</th>
                                        <th class="text-center">{{translate('credit')}}</th>
                                        <th class="text-center">{{translate('balance')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($transactions as $key => $transaction)
                                        <tr>
                                            <td class="sl">{{$key + $transactions->firstItem()}}</td>
                                            <td class="text-center">{{$transaction->id}}</td>
                                            <td class="text-center">{{$transaction->trx_ref_id}}</td>
                                            <td class="text-center">{{$transaction->created_at}}</td>
                                            <td class="text-center">{{$transaction?->user?->first_name .' ' . $transaction?->user?->last_name}}</td>
                                            <td class="text-center">{{set_currency_symbol($transaction->debit)}}</td>
                                            <td class="text-center">{{set_currency_symbol($transaction->credit)}}</td>
                                            <td class="text-center">{{set_currency_symbol($transaction->balance)}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12">
                                                <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                    <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                                                    <p class="text-center">{{translate('no_data_available')}}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                            </div>

                            <div
                                class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">

                                <div
                                    class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                    {!! $transactions->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module/js/user-management/customer/wallet/index.js')}}"></script>

    <script>
        "use strict";


        $('.js-select-customer').select2({
            placeholder: "Select Customer",
            allowClear: false,
            ajax: {
                url: '{{route('admin.customer.get-all-ajax')}}',
                data: function (params) {
                    return {
                        search: params.term, // search term
                        page: params.page,
                        all_customer: 1
                    };
                },
                processResults: function (data) {

                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });

        $('.js-select-customer_2').select2({
            placeholder: "Select Customer",
            allowClear: false,
            ajax: {
                url: '{{route('admin.customer.get-all-ajax')}}',
                data: function (params) {
                    return {
                        search: params.term, // search term
                        page: params.page,
                        all_customer: 1
                    };
                },
                processResults: function (data) {

                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);
                    $request.then(success);
                    $request.fail(failure);
                    return $request;
                }
            }
        });

        // Select a specific customer if the 'customer_id' query parameter is present in the URL
        let customerId = <?php echo json_encode(request()->get('user_id')); ?>; // convert PHP array to JavaScript object
        if (customerId) {
            $('.js-select-customer_2').val(customerId).trigger('change');
        }
    </script>
@endpush

