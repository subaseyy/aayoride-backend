@extends('adminmodule::layouts.master')

@section('title', translate('withdraw_requests'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row g-4">
            <div class="col-12">
                <h2 class="fs-22 mt-4 text-capitalize">{{translate('withdraw_requests')}}</h2>
                <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                    <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=all" class="nav-link
                                {{ !request()->has('status') || request()->get('status') =='all'? 'active' : '' }}">{{translate('all')}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status={{PENDING}}" class="nav-link
                                {{ request()->get('status') ==PENDING ? 'active' : '' }}">{{translate(PENDING)}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status={{APPROVED}}" class="nav-link
                                   {{ request()->get('status') ==APPROVED ? 'active' : '' }}">{{translate(APPROVED)}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status={{SETTLED}}" class="nav-link
                                   {{ request()->get('status') ==SETTLED ? 'active' : '' }}">{{translate(SETTLED)}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=denied" class="nav-link
                                {{ request()->get('status') ==DENIED ? 'active' : '' }}">{{translate(DENIED)}}</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted text-capitalize">{{translate('total_requests')}} : </span>
                        <span class="text-primary fs-16 fw-bold">{{$requests->total()}}</span>
                    </div>
                </div>

                <div class="card card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                        <form action="javascript:;" method="GET"
                              class="search-form search-form_style-two">
                            <div class="input-group search-form__input_group">
                                    <span class="search-form__icon">
                                        <i class="bi bi-search"></i>
                                    </span>
                                <input type="search" name="search" value="{{ request()->get('search') }}" id="search"
                                       class="theme-input-style search-form__input"
                                       placeholder="{{translate('search_here_by_customer_name')}}">
                            </div>
                            <button type="submit" class="btn btn-primary search-submit"
                                    data-url="{{ url()->full() }}">{{translate('search')}}</button>
                        </form>

                        <div class="d-flex gap-2 flex-wrap">
                            @if((!request()->has('status') || request()->get('status') == ALL) || request()->get('status') == APPROVED)
                                <button class="btn btn-primary settle-btn d-none" data-status="{{SETTLED}}">
                                    <span class="text">{{ translate("Settle") }}</span>
                                </button>
                            @endif

                            @if((!request()->has('status') || request()->get('status') == ALL) || request()->get('status') == PENDING)
                                <button class="btn btn-outline-danger denied-btn d-none" data-status="{{DENIED}}">
                                    <span class="text">{{ translate("Deny") }}</span>
                                </button>
                                <button class="btn btn-success approve-btn d-none" data-status="{{APPROVED}}">
                                    <span class="text">{{ translate("Approve") }}</span>
                                </button>
                            @endif
                            @if((!request()->has('status') || request()->get('status') == ALL) || in_array(request()->get('status'),[APPROVED,DENIED,SETTLED]))

                                <button class="btn btn-outline-danger reverse-btn d-none" data-status="reverse">
                                    <span class="text">{{ translate('Reverse') }}</span>
                                </button>
                            @endif


                            <div class="dropdown">
                                <button type="button" id="selectWithdrawMethod" data-url="{{ url()->full() }}"
                                        class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    {{ !request()->has('method') || request()->get('method') == ALL ?   translate("All Withdraw Methods") : request()->get('method') }}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right select-dropdown">
                                    <li><a class="dropdown-item withdraw-method" href="#"
                                           data-value="{{ALL}}">{{ translate("All Withdraw Methods") }}</a></li>
                                    @foreach($withdrawMethods as $withdrawMethod)
                                        <li><a class="dropdown-item withdraw-method" href="#"
                                               data-value="{{$withdrawMethod->method_name}}">{{$withdrawMethod->method_name}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="dropdown multiple-invoice d-none">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    Download
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><button class="dropdown-item invoice-btn" data-status="invoice">{{ translate("Invoice") }}</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown all-invoice">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    Download
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{ route('admin.driver.withdraw.multiple-invoice',
['status'=>(!request()->has('status') || request()->get('status') == ALL)?ALL:request()->get('status'),'method'=>(!request()->has('method') || request()->get('method') == ALL)?ALL:request()->get('method'),
'search'=>(!request()->has('search'))?null:request()->get('search')]) }}">{{ translate("Invoice") }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <form method="POST" action="{{route('admin.driver.withdraw.multiple-action')}}"
                              id="multipleAction">
                            @csrf
                            <input type="hidden" name="type" id="selectType" value="">
                            <input type="hidden" name="status" id="selectStatus" value="">
                            <table
                                class="table multiselect-table table-borderless align-middle table-hover text-nowrap">
                                <thead class="table-light align-middle text-capitalize">
                                <tr>
                                    <th>
                                        <input class="leading_checkbox" type="checkbox">
                                    </th>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('amount')}}</th>
                                    <th>{{ translate('name') }}</th>
                                    <th>{{ translate('withdraw_method') }}</th>
                                    <th>{{translate('request_time')}}</th>
                                    <th class="text-center">{{translate('status')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($requests as $key=>$request)
                                    <tr>
                                        <td><input type="checkbox" name="ids[]" value="{{$request->id}}"></td>
                                        <td>{{$requests->firstItem()+$key}}</td>
                                        <td>{{ set_currency_symbol($request['amount'] ?? 0) }}</td>
                                        <td>
                                            @if (isset($request->user))
                                                <a
                                                    href="{{route('admin.driver.show',$request->user_id)}}"
                                                    class="">{{ $request->user?->full_name }}</a>
                                            @else
                                                <a href="#">{{translate('not_found')}}</a>
                                            @endif
                                        </td>
                                        <td>{{ $request?->method?->method_name ?? translate("Method no longer exit.") }}</td>
                                        <td>{{date('Y-m-d h:i A',strtotime($request->created_at))}}</td>
                                        <td class="text-center">
                                            @if($request->status==SETTLED)
                                                <label class="badge badge-primary">{{translate(SETTLED)}}</label>
                                            @elseif($request->status==APPROVED)
                                                <label class="badge badge-primary">{{translate(APPROVED)}}</label>
                                            @elseif($request->status==DENIED)
                                                <label class="badge badge-danger">{{translate(DENIED)}}</label>
                                            @else
                                                <label class="badge badge-info">{{translate(PENDING)}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                @if (isset($request->user))
                                                    <a href="#"
                                                       class="btn btn-outline-info btn-action withdraw-info-aside_open"
                                                       data-id="{{$request->id}}"
                                                       data-type="view"
                                                       title="{{translate('View')}}">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>

                                                    @if($request->status == PENDING)
                                                        <a href="#"
                                                           class="btn btn-outline-success btn-action withdraw-info-aside_open"
                                                           data-id="{{$request->id}}"
                                                           data-type="{{APPROVED}}"
                                                           title="{{translate('Approved')}}">
                                                            <i class="bi bi-check-lg"></i>
                                                        </a>
                                                    @endif
                                                    @if($request->status == PENDING)
                                                        <a href="#"
                                                           class="btn btn-outline-danger btn-action withdraw-info-aside_open"
                                                           data-id="{{$request->id}}"
                                                           data-type="{{DENIED}}"
                                                           title="{{translate('Denied')}}">
                                                            <i class="bi bi-x-lg"></i>
                                                        </a>
                                                    @endif
                                                    @if($request->status == APPROVED)

                                                        <a href="#"
                                                           class="btn btn-outline-success btn-action withdraw-info-aside_open"
                                                           data-id="{{$request->id}}"
                                                           data-type="{{SETTLED}}"
                                                           title="{{translate('Settled')}}">
                                                            <img
                                                                src="{{asset('public/assets/admin-module/img/svg/sattled.svg')}}"
                                                                class="svg" alt="">
                                                        </a>
                                                    @endif
                                                    @if(in_array($request->status,[APPROVED,DENIED,SETTLED]))

                                                        <a href="#"
                                                           class="btn btn-outline-warning btn-action withdraw-info-aside_open"
                                                           data-id="{{$request->id}}"
                                                           data-type="reverse"
                                                           title="{{translate('Reverse status Back to Pending')}}">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{route('admin.driver.withdraw.single-invoice',$request->id)}}"
                                                       class="btn btn-outline-warning btn-action"
                                                       title="{{translate('Download')}}">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @else
                                                    <a href="#">
                                                        {{translate('account_disabled')}}
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @include('usermanagement::admin.driver.withdraw-request._aside-bar-view',$request)

                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div
                                                class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                <img
                                                    src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}"
                                                    alt="" width="100">
                                                <p class="text-center">{{translate('no_data_available')}}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-center justify-content-md-end">
                            <!-- Pagination -->
                            {{$requests->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{asset('public/assets/admin-module/js/withdraw-request.js')}}"></script>
    <script>
        "use strict"
        $(".withdraw-info-aside_open").on("click", function () {
            let id = $(this).data('id');
            let type = $(this).data('type');
            if (type == 'view') {
                $(".submit-button"+id).addClass('d-none')
            } else {
                $(".submit-button"+id).removeClass('d-none')
            }
            if (type == "{{APPROVED}}") {
                $(".status-approve"+id).removeClass('d-none')
                $('.status-approve'+id+' textarea').prop('disabled', false);
                $('input.status-approve'+id+'[name="ids[]"]').prop('disabled', false);
                $('input.status-approve'+id+'[name="status"]').prop('disabled', false);
            } else {
                $(".status-approve"+id).addClass('d-none')
                $('.status-approve'+id+' textarea').prop('disabled', true);
                $('input.status-approve'+id+'[name="ids[]"]').prop('disabled', true);
                $('input.status-approve'+id+'[name="status"]').prop('disabled', true);
            }
            if (type == "{{DENIED}}") {
                $(".status-deny"+id).removeClass('d-none')
                $('.status-deny'+id+' textarea').prop('disabled', false);
                $('input.status-deny'+id+'[name="ids[]"]').prop('disabled', false);
                $('input.status-deny'+id+'[name="status"]').prop('disabled', false);
            } else {
                $(".status-deny"+id).addClass('d-none')
                $('.status-deny'+id+' textarea').prop('disabled', true);
                $('input.status-deny'+id+'[name="ids[]"]').prop('disabled', true);
                $('input.status-deny'+id+'[name="status"]').prop('disabled', true);
            }
            if (type == "{{SETTLED}}") {
                $(".status-settle"+id).removeClass('d-none')
                $('input.status-settle'+id+'[name="ids[]"]').prop('disabled', false);
                $('input.status-settle'+id+'[name="status"]').prop('disabled', false);
            } else {
                $(".status-settle"+id).addClass('d-none')
                $('input.status-settle'+id+'[name="ids[]"]').prop('disabled', true);
                $('input.status-settle'+id+'[name="status"]').prop('disabled', true);
            }
            if (type == "reverse") {
                $(".status-reverse"+id).removeClass('d-none')
                $('input.status-reverse'+id+'[name="ids[]"]').prop('disabled', false);
                $('input.status-reverse'+id+'[name="status"]').prop('disabled', false);
            } else {
                $(".status-reverse"+id).addClass('d-none')
                $('input.status-reverse'+id+'[name="ids[]"]').prop('disabled', true);
                $('input.status-reverse'+id+'[name="status"]').prop('disabled', true);
            }
            $("#asideBar" + id).addClass("active");
            $('.singleActionButtonSubmit').on('click', function () {
                let dataId = $(this).data('id');
                $("#singleAction" + dataId).submit();
            })
        });

        $('.settle-btn').on('click', function () {
            $("#selectStatus").val($(this).data('status'));
            $("#multipleAction").submit();
        })
        $('.denied-btn').on('click', function () {
            $("#selectStatus").val($(this).data('status'));
            $("#selectType").val("type");
            $("#multipleAction").submit();
        })
        $('.approve-btn').on('click', function () {
            $("#selectStatus").val($(this).data('status'));
            $("#selectType").val("type");
            $("#multipleAction").submit();
        })
        $('.reverse-btn').on('click', function () {
            $("#selectStatus").val($(this).data('status'));
            $("#multipleAction").submit();
        })
        $('.invoice-btn').on('click', function () {
            $("#selectStatus").val($(this).data('status'));
            $("#multipleAction").submit();
        })

    </script>
@endpush
