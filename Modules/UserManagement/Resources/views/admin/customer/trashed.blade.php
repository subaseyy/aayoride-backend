@extends('adminmodule::layouts.master')

@section('title', translate('deleted_customer_list'))

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/assets/admin-module/plugins/dataTables/jquery.dataTables.min.css') }}">
@endpush
@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mt-4 text-capitalize">{{ translate('deleted_customer_list') }}</h2>

            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-end align-items-center my-3 gap-3">
                        <div class="d-flex gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_customer') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $customers->total() }}</span>
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
                                                    <th class="action text-center">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customers as $key => $customer)
                                                    <tr>
                                                        <td class="sl">{{ $key + $customers->firstItem() }}</td>
                                                        <td class="name">
                                                            <a href="#" class="media align-items-center gap-10">
                                                                <div class="rounded custom-box-size overflow-hidden" style="--size: 36px">
                                                                    <img loading="lazy" class="fit-object"
                                                                         src="{{ onErrorImage(
                                                                            $customer?->profile_image,
                                                                            asset('storage/app/public/customer/profile') . '/' . $customer?->profile_image,
                                                                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                            'customer/profile/',
                                                                        ) }}" alt="">
                                                                </div>
                                                                <div class="media-body">{{ $customer?->first_name }}
                                                                    {{ $customer?->last_name }}</div>
                                                            </a>
                                                        </td>
                                                        @php($count = 0)
                                                        @if (!is_null($customer?->first_name))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer?->first_name))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->email))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->phone))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->identification_number))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->identification_type))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->identification_image))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->other_documents))
                                                            @php($count++)
                                                        @endif
                                                        @if (!is_null($customer->profile_image))
                                                            @php($count++)
                                                        @endif
                                                        <td class="profile-status"><span
                                                                class="badge badge-success badge-warning">{{ round(($count / 9) * 100) }}%</span>
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
                                                        <td class="total-trip">{{ $customer->customerTrips->count() }}</td>

                                                        <td class="action">
                                                            <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                                <button
                                                                    data-route="{{ route('admin.customer.restore', ['id' => $customer->id]) }}"
                                                                    data-message="{{ translate('Want_to_recover_this_customer?_') . translate('if_yes,_this_customer_will_be_available_again_in_the_Customer_List') }}"
                                                                    class="btn btn-outline-primary btn-action restore-data">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </button>

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

                                    <div
                                        class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
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
