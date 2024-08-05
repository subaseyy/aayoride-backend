@section('title', translate('discount_List'))

@extends('adminmodule::layouts.master')

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h2 class="ffs-22 mt-4 text-capitalize">{{ translate('all_discount') }}</h2>

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{!request()->has('status') || request()->get('status')=='all'?'active':''}}"
                                   href="{{url()->current()}}?status=all">
                                    {{ translate('all') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{request()->get('status')=='active'?'active':''}}"
                                   href="{{url()->current()}}?status=active">
                                    {{ translate('active') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{request()->get('status')=='inactive'?'active':''}}"
                                   href="{{url()->current()}}?status=inactive">
                                    {{ translate('inactive') }}
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_discounts') }} : </span>
                            <span class="text-primary fs-16 fw-bold"
                                  id="total_record_count">{{ $discounts->total() }}</span>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="{{url()->full()}}"
                                      class="search-form search-form_style-two" method="GET">
                                    @foreach(request()->query() as $key => $value)
                                        @if ($key !== 'search') <!-- Exclude search parameter -->
                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        <input type="search" class="theme-input-style search-form__input"
                                               value="{{request()->get('search')}}" name="search" id="search"
                                               placeholder="{{translate('search_here_by_title')}}">
                                    </div>
                                    <button type="submit"
                                            class="btn btn-primary">{{ translate('search') }}</button>
                                </form>


                                <div class="d-flex flex-wrap gap-3">
                                    @can('super-admin')
                                        <a href="{{ route('admin.promotion.discount-setup.index',['status'=>request('status')]) }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                           data-bs-title="{{ translate('refresh') }}">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </a>

                                        <a href="{{ route('admin.promotion.discount-setup.trashed') }}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                           data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                            <i class="bi bi-recycle"></i>
                                        </a>
                                    @endcan

                                    @can('promotion_log')
                                        <a href="{{route('admin.promotion.discount-setup.log')}}"
                                           class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                           data-bs-title="{{ translate('view_Log') }}">
                                            <i class="bi bi-clock-fill"></i>
                                        </a>
                                    @endcan

                                    @can('promotion_export')
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="dropdown">
                                                <i class="bi bi-download"></i>
                                                {{ translate('download') }}
                                                <i class="bi bi-caret-down-fill"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                <li><a class="dropdown-item"
                                                       href="{{route('admin.promotion.discount-setup.export')}}?status={{request()->get('status') ?? "all"}}&&file=excel">{{translate('excel')}}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    @endcan

                                    @can('promotion_add')
                                        <a href="{{route('admin.promotion.discount-setup.create')}}" type="button"
                                           class="btn btn-primary text-capitalize">
                                            <i class="bi bi-plus fs-16"></i> {{ translate('add_discount') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle table-hover text-nowrap text-center">
                                    <thead class="table-light align-middle text-capitalize">
                                    <tr>
                                        <th>{{ translate('SL') }}</th>
                                        <th class="discount_image">{{ translate('image') }}</th>
                                        <th class="discount_title">{{ translate('discount_title') }}</th>
                                        <th class="zone">{{ translate('zone') }}</th>
                                        <th class="customer_level">{{ translate('customer_level') }}</th>
                                        <th class="customer">{{ translate('customer') }}</th>
                                        <th class="module">{{ translate('module') }}</th>
                                        <th class="discount_amount">{{ translate('discount_amount') }}</th>
                                        <th class="duration">{{ translate('duration') }}</th>
                                        <th class="total_times_used">{{ translate('total_times_used') }}</th>
                                        <th class="total_discount_amount">{{ translate('total_discount') }}
                                            <br> {{ translate('amount') }}
                                            ({{session()->get('currency_symbol') ?? '$'}})
                                        </th>
                                        <th class="discount_status">{{ translate('discount_status') }}</th>
                                        @can('promotion_edit')
                                            <th class="status">{{ translate('status') }}</th>
                                        @endcan
                                        <th class="text-center action">{{ translate('action') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse ($discounts as $key => $discount)
                                        <tr id="hide-row-{{$discount->id}}" class="record-row">
                                            <td>{{ $discounts->firstItem() + $key }}</td>
                                            <td class="discount_image">
                                                <img src="{{ onErrorImage(
                                                $discount?->image,
                                                asset('storage/app/public/promotion/discount') . '/' . $discount?->image,
                                                asset('public/assets/admin-module/img/media/banner-upload-file.png'),
                                                'promotion/discount/',
                                            ) }}"
                                                     class="custom-box-size-banner rounded dark-support" alt="">
                                            </td>
                                            <td class="discount_title">{{ $discount->title }}</td>
                                            <td class="zone">
                                                @if($discount->zone_discount_type == ALL)
                                                    <span class="badge bg-info rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                @else
                                                    @foreach($discount->zones as $zone)
                                                        <span
                                                            class="badge bg-info rounded-pill badge-sm text-capitalize">{{ $zone->name }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="customer_level">
                                                @if($discount->customer_level_discount_type == ALL)
                                                    <span class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                @else
                                                    @foreach($discount->customerLevels as $level)
                                                        <span
                                                            class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ $level->name }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="customer">
                                                @if($discount->customer_discount_type == ALL)
                                                    <span class="badge bg-success rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                @else
                                                    @foreach($discount->customers as $customer)
                                                        <span
                                                            class="badge bg-success rounded-pill badge-sm text-capitalize">{{ $customer->first_name }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="module">
                                                @if(in_array(ALL,$discount->module_discount_type))
                                                    <span class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ALL}}</span>
                                                @elseif(in_array(PARCEL,$discount->module_discount_type) && in_array(CUSTOM,$discount->module_discount_type))
                                                    <span
                                                        class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ PARCEL }}</span>
                                                    @foreach($discount->vehicleCategories as $category)
                                                        <span
                                                            class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ $category->name }}</span>
                                                    @endforeach
                                                @elseif(in_array(PARCEL,$discount->module_discount_type))
                                                    <span
                                                        class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ PARCEL }}</span>
                                                @elseif(in_array(CUSTOM,$discount->module_discount_type))
                                                    @foreach($discount->vehicleCategories as $category)
                                                        <span
                                                            class="badge bg-warning rounded-pill badge-sm text-capitalize">{{ $category->name }}</span>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td class="discount_amount">{{ $discount->discount_amount_type == PERCENTAGE? $discount->discount_amount.'%': set_currency_symbol($discount->discount_amount) }}</td>
                                            <td class="duration" class="text-capitalize vehicle-features">
                                                {{translate('start')}}
                                                : {{date('Y-m-d',strtotime($discount->start_date))}} <br>
                                                {{translate('end')}} : {{date('Y-m-d',strtotime($discount->end_date))}}
                                                <br>
                                                {{translate('duration')}}
                                                : {{ Carbon\Carbon::parse($discount->end_date)->diffInDays($discount->start_date)}}
                                                Days
                                            </td>
                                            <td class="total_times_used">{{ (int)$discount->total_used }}</td>
                                            <td class="total_discount_amount">{{ set_currency_symbol(round($discount->total_amount,2)) }}</td>
                                            <td class="discount_status">
                                                @php($date = Carbon\Carbon::now()->startOfDay())
                                                @if($date->gt($discount->end_date))
                                                    <span
                                                        class="badge badge-danger">{{ translate(EXPIRED) }}</span>
                                                @elseif (!$discount->is_active)
                                                    <span
                                                        class="badge badge-warning">{{ translate(CURRENTLY_OFF) }}</span>
                                                @elseif ($date->lt($discount->start_date))
                                                    <span
                                                        class="badge badge-info">{{ translate(UPCOMING) }}</span>
                                                @elseif ($date->lte($discount->end_date))
                                                    <span
                                                        class="badge badge-success">{{ translate(RUNNING) }}</span>
                                                @endif
                                            </td>
                                            @can('promotion_edit')
                                                <td class="status">
                                                    <label class="switcher mx-auto">
                                                        @if($date->gt($discount->end_date))
                                                            <input class="switcher_input status-change"
                                                                   data-url={{ route('admin.promotion.discount-setup.status') }} id="{{ $discount->id }}"
                                                                   type="checkbox" disabled>
                                                            <span class="switcher_control"
                                                                  title="{{ translate('discount already completed, You do not change this status.') }}"></span>
                                                        @else
                                                            <input class="switcher_input status-change"
                                                                   data-url={{ route('admin.promotion.discount-setup.status') }} id="{{ $discount->id }}"
                                                                   type="checkbox" {{$discount->is_active?'checked':''}}>
                                                            <span class="switcher_control"></span>
                                                        @endif
                                                    </label>
                                                </td>
                                            @endcan
                                            <td class="action">
                                                <div class="d-flex justify-content-center gap-2 align-items-center">
                                                    @can('promotion_log')
                                                        <a href="{{route('admin.promotion.discount-setup.log')}}?id={{$discount->id}}"
                                                           class="btn btn-outline-primary btn-action"
                                                           title="{{ translate('history_log') }}">
                                                            <i class="bi bi-clock-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('promotion_edit')
                                                        <a href="{{route('admin.promotion.discount-setup.edit', ['id'=>$discount->id])}}"
                                                           class="btn btn-outline-info btn-action"
                                                           title="{{ translate('edit_discount') }}">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan

                                                    @can('promotion_delete')
                                                        <button title="{{ translate('delete_discount') }}"
                                                                data-id="delete-{{ $discount->id }}"
                                                                data-message="{{ translate('want_to_delete_this_discount._?') }} {{ translate('You_can_recover_it_from_the_“Deleted_discount”_section') }}"
                                                                type="button"
                                                                class="btn btn-outline-danger btn-action form-alert">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>

                                                        <form
                                                            action="{{ route('admin.promotion.discount-setup.delete', ['id'=>$discount->id]) }}"
                                                            id="delete-{{ $discount->id }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                        </form>
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

                            <div class="d-flex justify-content-end">
                                {!! $discounts->withQueryString()->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
