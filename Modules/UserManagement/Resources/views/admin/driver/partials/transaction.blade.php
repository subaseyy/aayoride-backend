<div class="tab-pane fade active show" id="transaction-pane" role="tabpanel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-30 mb-3 gap-3">
        <h2 class="fs-22 text-capitalize">{{translate('all_transaction')}}</h2>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted">Total Transaction : </span>
            <span class="text-primary fs-16 fw-bold">{{$otherData['transactions']->total()}}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                <form action="javascript:;" method="GET" class="search-form search-form_style-two">
                    <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                        <input type="search" name="search" value="{{$otherData['search']}}" id="search"
                               class="theme-input-style search-form__input"
                               placeholder="{{translate('Search Here')}}">
                    </div>
                    <button type="submit" class="btn btn-primary search-submit"
                            data-url="{{ url()->full() }}">{{translate('search')}}</button>
                </form>

                <div class="d-flex flex-wrap gap-3">
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i>
                            {{translate('download')}}
                            <i class="bi bi-caret-down-fill"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <li><a class="dropdown-item"
                                   href="{{route('admin.driver.transaction-export', ['id' => $commonData['driver']->id,'file' => 'excel','search' => request()->input('search')])}}">{{translate('excel')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-borderless align-middle table-hover">
                    <thead class="table-light align-middle text-capitalize">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('transaction_ID')}}</th>
                        <th>{{translate('type')}}</th>
                        <th class="text-capitalize">{{translate('transaction_to')}}</th>
                        <th>{{ translate('debit')}} ({{session()->get('currency_symbol') ?? '$'}})</th>
                        <th>{{ translate('credit')}} ({{session()->get('currency_symbol') ?? '$'}})</th>
                        <th class="text-capitalize">{{translate('last_balance')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($otherData['transactions'] as $key => $item)
                        <tr>
                            <td>{{$key + $otherData['transactions']->firstItem()}}</td>
                            <td>{{$item->id}}</td>
                            <td>{{ translate($item->account)}}</td>
                            <td>{{$item->user?->first_name . ' ' . $item->user?->last_name}}</td>
                            <td>{{$item->debit}}</td>
                            <td>+ {{$item->credit}}</td>
                            <td>{{set_currency_symbol($item->balance)}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15">
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

            <div class="table-bottom d-flex flex-column flex-sm-row justify-content-end align-items-center gap-2">
                {!! $otherData['transactions']->withQueryString()->links() !!}
            </div>
        </div>
    </div>
</div>
