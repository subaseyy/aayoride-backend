@extends('adminmodule::layouts.master')

@section('title', translate('Withdraw_Information'))

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">{{translate('withdraw')}}</h2>
            </div>
            <div class="content container-fluid">
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="badge-primary p-3">
                                <h5 class="text-capitalize">
                                    {{translate('withdraw_information')}}
                                </h5>
                            </div>
                            <div class="row p-3">
                                <div class="col-3 h-100">
                                    <h6 class="pb-2 d-flex flex-wrap gap-1"> <span>{{translate('amount')}}</span> <span>:</span> <span>{{getCurrencyFormat($request->amount)}}</span></h6>
                                    <h6 class="d-flex flex-wrap gap-1"> <span>{{translate('request_at')}}</span> <span>:</span> <span>{{\Carbon\Carbon::parse($request->created_at)->format('Y-m-d h:i a')}}</span> </h6>
                                </div>
                                <div class="col-3 h-100">
                                    <h6 class="pb-2">{{translate('request_note_by_driver')}}</h6>
                                    <p>{{$request->note}}</p>
                                </div>
                                    <div class="col-4 h-100">
                                        <h6 class="pb-2">{{translate('note_by_admin')}}</h6>
                                        <p>{{$request->rejection_cause}}</p>
                                    </div>
                                <div class="col-2 h-100">
                                    @if (is_null($request->is_approved))
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal">
                                            {{translate('proceed')}}
                                        </button>
                                    @else
                                        <div>
                                            @if($request->is_approved==1)
                                                <h6>{{ translate("status") }}: <label class="badge badge-success p-2 rounded-bottom">
                                                        {{translate('approved')}}
                                                    </label></h6>
                                                <h6>{{translate('updated_at')}}: {{\Carbon\Carbon::parse($request->updated_at)->format('Y-m-d h:i a')}}</h6>
                                            @else
                                                <h6>{{ translate("status") }}: <label class="badge badge-danger p-2 rounded-bottom">
                                                        {{translate('denied')}}
                                                    </label></h6>
                                                <h6>{{translate('updated_at')}}: {{\Carbon\Carbon::parse($request->updated_at)->format('Y-m-d h:i a')}}</h6>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="card h-100">
                            <div class="badge-primary p-3">
                                <h5 class="text-capitalize">
                                    {{translate('driver_information')}}
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-wrap column-gap-4 row-gap-3">
                                <div class="form-group">
                                    <p>{{translate('name')}}
                                        : {{$request->user ? $request->user?->first_name . ' '. $request->user?->last_name : translate('driver_not_found')}}</p>
                                    <p>{{translate('email')}}: {{$request?->user?->email ?? translate('driver_not_found')}}</p>
                                    <p>{{translate('phone')}}: {{$request?->user?->phone ?? translate('driver_not_found')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card h-100">
                            <div class="badge-primary p-3">
                                <h5 class="text-capitalize">
                                    {{translate('payment_method')}}
                                </h5>
                            </div>
                            <div class="card-body d-flex flex-wrap column-gap-4 row-gap-3">
                                <div class="form-group">
                                    @forelse($request->method_fields as $key => $mf)
                                        <p class="text-capitalize">{{translate($key)}}: <b>{{$mf}}</b></p>
                                    @empty
                                    @endforelse

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ translate('withdraw_request_process') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('admin.driver.withdraw.action',[$request['id']])}}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="approve_btn"
                                   class="col-form-label">{{translate('Request')}}:</label>
                            <select name="is_approved" class="form-control" id="approve_btn">
                                <option value="1">{{translate('approve')}}</option>
                                <option value="0">{{translate('deny')}}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message-text"
                                   class="col-form-label">{{translate('note_about_transaction_or_request')}}
                                :</label>
                            <textarea class="form-control" name="rejection_cause" minlength="1" maxlength="1000" id="message-text"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{translate('close')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

