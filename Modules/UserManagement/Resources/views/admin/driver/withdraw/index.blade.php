@extends('adminmodule::layouts.master')

@section('title', translate('withdraw_method_list'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row g-4">
            <div class="col-12">
                <h2 class="fs-22 mt-4 text-capitalize">{{translate('withdraw_method_list')}}</h2>
                <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                    <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=all" class="nav-link
                                {{ !request()->has('status') || request()->get('status') =='all'? 'active' : '' }}">{{translate('all')}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=active" class="nav-link
                                   {{ request()->get('status') =='active' ? 'active' : '' }}">{{translate('active')}}</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{url()->current()}}?status=inactive" class="nav-link
                                {{ request()->get('status') =='inactive' ? 'active' : '' }}">{{translate('inactive')}}</a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted text-capitalize">{{translate('total_methods')}} : </span>
                        <span class="text-primary fs-16 fw-bold"
                              id="total_record_count">{{$withdrawalMethods->total()}}</span>
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
                                       placeholder="{{translate('search_here_by_Method_Name')}}">
                            </div>
                            <button type="submit" class="btn btn-primary search-submit"
                                    data-url="{{ url()->full() }}">{{translate('search')}}</button>
                        </form>

                        <div class="d-flex flex-wrap gap-3">
                            @can('user_add')
                                <a href="{{route('admin.driver.withdraw-method.create')}}" type="button"
                                   class="btn btn-primary text-capitalize">
                                    <i class="bi bi-plus fs-16"></i> {{translate('add_method')}}
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table id="datatable"
                               class="table table-borderless align-middle table-hover text-nowrap">
                            <thead class="table-light align-middle text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('method_name')}}</th>
                                <th>{{ translate('method_fields') }}</th>
                                @can('user_edit')
                                    <th class="text-center">{{translate('default_method')}}</th>
                                @endcan
                                @can('user_edit')
                                    <th>{{translate('status')}}</th>
                                @endcan
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($withdrawalMethods as $key=>$withdrawalMethod)
                                <tr id="hide-row-{{$withdrawalMethod->id}}">
                                    <td>{{$withdrawalMethods->firstitem()+$key}}</td>
                                    <td>{{$withdrawalMethod['method_name']}}</td>
                                    <td>
                                        @foreach($withdrawalMethod['method_fields'] as $keyData=>$method_field)
                                            @if($keyData==0)
                                                <div class="fz-12 d-flex flex-column gap-1">
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <div>{{translate('Field Name')}}</div>
                                                        :
                                                        <div>{{$withdrawalMethod['method_fields'][$keyData]['input_name'] }}</div>
                                                    </div>
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <div>{{translate('Type')}}</div>
                                                        :
                                                        <div>{{ $withdrawalMethod['method_fields'][$keyData]['input_type'] }}</div>
                                                    </div>
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <div>{{translate('Placeholder')}}</div>
                                                        :
                                                        <div>{{ $withdrawalMethod['method_fields'][$keyData]['placeholder'] }}</div>
                                                    </div>
                                                    <div class="d-flex gap-1 align-items-center">
                                                        <div>{{translate('Is Required')}}</div>
                                                        :
                                                        <div>{{ $withdrawalMethod['method_fields'][$keyData]['is_required'] && $withdrawalMethod['method_fields'][$keyData]['is_required'] == 1 ? translate('yes') : translate('no') }}</div>
                                                    </div>
                                                    <div class="mt-1">
                                                        <button type="button"
                                                                class="bg-transparent border-0 p-0 d-flex align-items-center gap-2 fw-bold text-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#methodDetailsModal{{$key}}">
                                                            {{translate('See All')}}
                                                            <i class="bi bi-arrow-right"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach

                                        <div class="modal fade" id="methodDetailsModal{{$key}}" tabindex="-1"
                                             aria-labelledby="methodDetailsModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header pb-0 border-0">
                                                        <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="text-center">
                                                            <h5 class="mb-2">{{translate('Withdraw Method List')}}</h5>
                                                            <div
                                                                class="d-flex gap-1 align-items-center justify-content-center fs-12 title-color mb-4">
                                                                <div>{{translate('Method Name')}}</div>
                                                                :
                                                                <div
                                                                    class="fw-bold">{{translate('Master Card')}}</div>
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table class="table align-middle">
                                                                    <tbody>
                                                                    @foreach($withdrawalMethod['method_fields'] as $keyData=>$method_field)

                                                                        <tr>
                                                                            <td>{{$keyData+1}}</td>
                                                                            <td>
                                                                                <div
                                                                                    class="fz-12 d-flex flex-column gap-1">
                                                                                    <div
                                                                                        class="d-flex gap-1 align-items-center">
                                                                                        <div>{{translate('Field Name')}}</div>
                                                                                        :
                                                                                        <div>{{$withdrawalMethod['method_fields'][$keyData]['input_name'] }}</div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex gap-1 align-items-center">
                                                                                        <div>{{translate('Type')}}</div>
                                                                                        :
                                                                                        <div>{{ $withdrawalMethod['method_fields'][$keyData]['input_type'] }}</div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex gap-1 align-items-center">
                                                                                        <div>{{translate('Placeholder')}}</div>
                                                                                        :
                                                                                        <div>{{ $withdrawalMethod['method_fields'][$keyData]['placeholder'] }}</div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                @if($withdrawalMethod['method_fields'][$keyData]['is_required'] && $withdrawalMethod['method_fields'][$keyData]['is_required'] == 1)
                                                                                    <div
                                                                                        class="d-flex gap-2 align-items-center">
                                                                                        <img class="text-primary"
                                                                                             src="{{asset('public/assets/admin-module/img/svg/check.svg')}}"
                                                                                             alt="" class="svg">
                                                                                        {{translate('Required')}}
                                                                                    </div>
                                                                                @else
                                                                                    <div class="">
                                                                                        {{translate('optional')}}
                                                                                    </div>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </td>
                                    @can('user_edit')
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <label class="switcher">
                                                    <input type="checkbox" class="switcher_input default-status"
                                                           data-url="{{route('admin.driver.withdraw-method.default-status-update')}}"
                                                           id="{{$withdrawalMethod->id}}" {{$withdrawalMethod->is_default?'checked':''}}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </div>
                                        </td>
                                    @endcan
                                    @can('user_edit')
                                        <td>
                                            <label class="switcher">
                                                <input class="switcher_input status-change"
                                                       data-url="{{ route('admin.driver.withdraw-method.active-status-update') }}"
                                                       id="{{ $withdrawalMethod->id }}"
                                                       type="checkbox" {{$withdrawalMethod->is_active?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                    @endcan
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @can('user_edit')
                                                <a href="{{route('admin.driver.withdraw-method.edit',[$withdrawalMethod->id])}}"
                                                   class="btn btn-outline-warning btn-action">
                                                    <i class="bi bi-pen"></i>
                                                </a>
                                            @endcan
                                            @can('user_delete')
                                                @if(!$withdrawalMethod->is_default)
                                                    <a class="btn btn-outline-danger btn-action form-alert"
                                                       href="javascript:"
                                                       title="{{translate('Delete')}}"
                                                       data-id="delete-{{ $withdrawalMethod->id }}"
                                                       data-message="{{ translate('want_to_delete_this_item?') }}">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                    <form
                                                        action="{{route('admin.driver.withdraw-method.delete',[$withdrawalMethod->id])}}"
                                                        method="post" id="delete-{{$withdrawalMethod->id}}">
                                                        @csrf @method('delete')
                                                    </form>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14">
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
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="px-4 d-flex justify-content-center justify-content-md-end">
                            <!-- Pagination -->
                            {{$withdrawalMethods->links()}}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

