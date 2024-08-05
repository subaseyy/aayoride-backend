@extends('adminmodule::layouts.master')

@section('title', translate('Customer_List'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/dataTables/jquery.dataTables.min.css') }}">
@endpush
@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('user_view')
                <h2 class="fs-22 mb-4 text-capitalize">{{ translate('customer_analytics') }}</h2>

                <div id="statistics">

                </div>
            @endcan
            <h2 class="fs-22 mt-4 text-capitalize">{{ translate('customer_list') }}</h2>

            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ route('admin.customer.index') }}"
                                    class="nav-link {{ is_null(Request::get('value')) ? 'active' : '' }}"
                                    role="tab">{{ translate('all') }}</a>
                            </li>
                            @forelse($levels as $level)
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ Request::get('value') == $level->id ? 'active' : '' }}"
                                        href="{{ url()->current() }}?value={{ $level->id }}">{{ $level->name }}</a>
                                </li>
                            @empty
                            @endforelse

                        </ul>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_customer') }} : </span>
                            <span class="text-primary fs-16 fw-bold"
                                id="total_record_count">{{ $customers->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form class="search-form search-form_style-two">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" name="search" value="{{request()->get('search')}}"
                                                    class="theme-input-style search-form__input"
                                                    placeholder="{{ translate('Search_Here_by_Customer_Name') }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            @can('super-admin')
                                                <a href="{{ route('admin.customer.index', ['value' => request('value')]) }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>

                                                <a href="{{ route('admin.customer.trash') }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                                    <i class="bi bi-recycle"></i>
                                                </a>
                                            @endcan
                                            @can('user_log')
                                                <a href="{{ route('admin.customer.log') }}" class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                                    <i class="bi bi-clock-fill"></i>
                                                </a>
                                            @endcan

                                            @can('user_export')
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-outline-primary"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-download"></i>
                                                        {{ translate('download') }}
                                                        <i class="bi bi-caret-down-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.customer.export') }}?search={{ request()->get('search') }}&&file=excel">{{ translate('excel') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endcan
                                            @can('user_add')
                                                <a href="{{ route('admin.customer.create') }}" type="button"
                                                    class="btn btn-primary text-capitalize">
                                                    <i class="bi bi-plus fs-16"></i> {{ translate('add_customer') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-borderless align-middle table-hover">
                                            <thead class="table-light align-middle text-capitalize">
                                                <tr>
                                                    <th class="sl">{{ translate('SL') }}</th>
                                                    <th class="customer-name text-capitalize">
                                                        {{ translate('customer_name') }}</th>
                                                    <th class="profile-status text-capitalize">
                                                        {{ translate('profile_status') }}</th>
                                                    <th class="contact-info text-capitalize">
                                                        {{ translate('contact_info') }}</th>
                                                    <th class="level">{{ translate('level') }}</th>
                                                    <th class="total-trip text-capitalize">
                                                        {{ translate('total_trip') }}</th>
                                                    @can('user_edit')
                                                        <th class="status">{{ translate('status') }}</th>
                                                    @endcan
                                                    <th class="action text-center">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customers as $key => $customer)
                                                    <tr>
                                                        <td class="sl">{{ $key + $customers->firstItem() }}</td>
                                                        <td class="customer-name">
                                                            <a href="{{ route('admin.customer.show', ['id' => $customer->id]) }}"
                                                                class="media align-items-center gap-2">
                                                                <div class="rounded custom-box-size overflow-hidden" style="--size: 36px">
                                                                    <img loading="lazy" class="fit-object"
                                                                         src="{{ onErrorImage(
                                                                        $customer?->profile_image,
                                                                        asset('storage/app/public/customer/profile') . '/' . $customer?->profile_image,
                                                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                        'customer/profile/',
                                                                    ) }}" alt="">
                                                                </div>

                                                                <div class="media-body">
                                                                    {{ $customer?->first_name . ' ' . $customer?->last_name }}
                                                                </div>
                                                            </a>
                                                        </td>
                                                        <td class="profile-status"><span
                                                                class="badge badge-success badge-warning">{{ $customer->completion_percent }}%</span>
                                                        </td>
                                                        <td class="contact-info">
                                                            <div class="title-color"><a
                                                                    href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a>
                                                            </div>
                                                            <div>
                                                                <a
                                                                    href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                                            </div>
                                                        </td>
                                                        <td class="level">{{ $customer->level?->name }}</td>
                                                        <td class="total-trip">{{ $customer->customerTrips->count() }}
                                                        </td>
                                                        @can('user_edit')
                                                            <td class="status">
                                                                <label class="switcher">
                                                                    <input class="switcher_input status-change"
                                                                        type="checkbox"
                                                                        {{ $customer->is_active == 1 ? 'checked' : '' }}
                                                                        data-url="{{ route('admin.customer.update-status') }}"
                                                                        id="{{ $customer->id }}">
                                                                    <span class="switcher_control"></span>
                                                                </label>
                                                            </td>
                                                        @endcan

                                                        <td class="action">
                                                            <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                                <div
                                                                    class="d-flex justify-content-center gap-2 align-items-center">
                                                                    @can('user_log')
                                                                        <a href="{{ route('admin.customer.log') }}?id={{ $customer->id }}"
                                                                            class="btn btn-outline-primary btn-action">
                                                                            <i class="bi bi-clock-fill"></i>
                                                                        </a>
                                                                    @endcan
                                                                    @can('user_edit')
                                                                        <a href="{{ route('admin.customer.edit', ['id' => $customer->id]) }}"
                                                                            class="btn btn-outline-success btn-action">
                                                                            <i class="bi bi-pen"></i>
                                                                        </a>
                                                                    @endcan
                                                                    @can('user_view')
                                                                        <a href="{{ route('admin.customer.show', ['id' => $customer->id]) }}"
                                                                            class="btn btn-outline-info btn-action">
                                                                            <i class="bi bi-eye-fill"></i>
                                                                        </a>
                                                                    @endcan
                                                                    @can('user_delete')
                                                                            @if(count($customer->getCustomerUnpaidParcelAndTrips())>0|| count($customer->getCustomerPendingTrips())>0|| count($customer->getCustomerAcceptedTrips())>0 || count($customer->getCustomerOngingTrips())>0 )
                                                                                <button data-id="delete-{{ $customer->id }}"
                                                                                        data-message="{{ translate("Sorry you can't delete this customer, because there are ongoing rides or payment due this customer.?") }}"
                                                                                        type="button"
                                                                                        class="btn btn-outline-danger btn-action form-alert-warning">
                                                                                    <i class="bi bi-trash-fill"></i>
                                                                                </button>
                                                                            @else
                                                                                <button data-id="delete-{{ $customer->id }}"
                                                                                        data-message="{{ translate('want_to_delete_this_customer?') }}"
                                                                                        type="button"
                                                                                        class="btn btn-outline-danger btn-action form-alert">
                                                                                    <i class="bi bi-trash-fill"></i>
                                                                                </button>
                                                                                <form
                                                                                    action="{{ route('admin.customer.delete', ['id' => $customer->id]) }}"
                                                                                    method="post" id="delete-{{ $customer->id }}">
                                                                                    @csrf
                                                                                    @method('delete')
                                                                                </form>
                                                                            @endif
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="14">
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

                                    <div class="d-flex justify-content-end">
                                        {{ $customers->links() }}


                                    </div>

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
    <script>
        "use strict";

        loadPartialView('{{ route('admin.customer.statistics') }}', '#statistics')
    </script>
@endpush
