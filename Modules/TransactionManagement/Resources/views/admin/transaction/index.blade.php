@extends('adminmodule::layouts.master')

@section('title', translate('Transaction'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">{{translate('transaction_list')}}</h2>
                <div class="d-flex align-items-center gap-2 text-capitalize">
                    <span class="text-muted">{{translate('total_transactions')}} : </span>
                    <span class="text-primary fs-16 fw-bold" id="">{{$transactions->total()}}</span>
                </div>
            </div>

            <div class="tab-content mt-3">
                <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="javascript:;" method="GET"
                                      class="search-form search-form_style-two">
                                    <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        <input type="search" name="search" value="{{request()->get('search')}}" id="search"
                                               class="theme-input-style search-form__input"
                                               placeholder="{{translate('Search_Here_by_Transaction_Id')}}">
                                    </div>
                                    <button type="submit" class="btn btn-primary search-submit" data-url="{{ url()->full() }}">{{translate('search')}}</button>
                                </form>
                                @can('transaction_export')
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
                                                       href="{{route('admin.transaction.export', ['file' => 'excel', request()->getQueryString()])}}">{{translate('excel')}}</a>
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
                                        <th class="text-center">{{translate('transaction_to')}}</th>
                                        <th class="text-center">{{translate('credit')}}</th>
                                        <th class="text-center">{{translate('debit')}}</th>
                                        <th class="text-center">{{translate('balance')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($transactions as $key => $transaction)
                                        <tr>
                                            <td class="sl">{{$key + $transactions->firstItem()}}</td>
                                            <td class="text-center">{{$transaction->id}}</td>
                                            <td class="text-center">{{ $transaction->trx_ref_id ?? '-' }}</td>
                                            <td class="text-center">{{date('d-m-Y h:i A', strtotime($transaction->created_at))}}</td>
                                            <td class="text-center">
                                                {{ $transaction?->user?->first_name . ' ' . $transaction?->user?->last_name }}
                                                <small class="opacity-75 d-block">
                                                    {{ ucwords(str_replace('_', ' ', $transaction->account) )}} {{ $transaction->transaction_type != null ? '(' . ucwords($transaction->transaction_type) .')' : ""}}
                                                </small>
                                            </td>
                                            <td class="text-center">{{getCurrencyFormat($transaction->credit)}}</td>
                                            <td class="text-center">{{getCurrencyFormat($transaction->debit)}}</td>
                                            <td class="text-center">{{getCurrencyFormat($transaction->balance)}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">
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
                                class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                {!! $transactions->links() !!}
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
    <script>

    </script>
@endpush

