@extends('adminmodule::layouts.master')

@section('title', translate('Driver_Identity_Update_Request_List'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mt-4 text-capitalize">{{ translate('Driver_Identity_Update_Request_List') }}</h2>
            <div class="row g-4">
                <div class="col-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="{{ url()->current() }}?status=all"
                                   class="nav-link {{ !request()->has('status') || request()->get('status') =='all'? 'active' : '' }}">{{ translate('all') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ url()->current() }}?status=active"
                                   class="nav-link {{ request()->get('status') =='active' ? 'active' : '' }}">{{ translate('active') }}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="{{ url()->current() }}?status=inactive"
                                   class="nav-link {{ request()->get('status') =='inactive' ? 'active' : '' }}">{{ translate('inactive') }}</a>
                            </li>
                        </ul>

                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">{{ translate('total_driver') }} : </span>
                            <span class="text-primary fs-16 fw-bold"
                                  id="total_record_count">{{ $drivers->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="driver_all" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="javascript:;" method="GET"
                                              class="search-form search-form_style-two">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" name="search"
                                                       value="{{ request()->get('search') }}"
                                                       id="search" class="theme-input-style search-form__input"
                                                       placeholder="{{ translate('search_here_by_name') }}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                    data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            @can('super-admin')
                                                <a href="{{ route('admin.driver.index', ['status' => request('status')]) }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('refresh') }}">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </a>

                                                <a href="{{ route('admin.driver.trash') }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                                    <i class="bi bi-recycle"></i>
                                                </a>
                                            @endcan
                                            @can('user_log')
                                                <a href="{{ route('admin.driver.log') }}"
                                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip"
                                                   data-bs-title="{{ translate('view_Log') }}">
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
                                                               href="{{ route('admin.driver.profile-update-request-list-export') }}?status={{ request()->get('status') ?? "all" }}&file=excel">{{ translate('excel') }}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endcan
                                            @can('user_add')
                                                <a href="{{ route('admin.driver.create') }}" type="button"
                                                   class="btn btn-primary text-capitalize">
                                                    <i class="bi bi-plus fs-16"></i> {{ translate('add_driver') }}
                                                </a>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-borderless align-middle table-hover">
                                            <thead class="table-light align-middle text-capitalize">
                                            <tr>
                                                <th>{{ translate('SL') }}</th>
                                                <th class="name">{{ translate('name') }}</th>
                                                <th class="contact-info">{{ translate('contact_info') }}</th>
                                                <th class="profile-status">{{ translate('profile_status') }}</th>
                                                <th class="level">{{ translate('level') }}</th>
                                                <th class="total-trip">{{ translate('total_trip') }}</th>
                                                <th class="earning">{{ translate('earning') }}</th>
                                                @can('user_edit')
                                                    <th class="status">{{ translate('status') }}</th>
                                                @endcan
                                                <th class="text-center action">{{ translate('action') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($drivers as $key => $driver)
                                                <tr id="hide-row-{{ $driver->id }}" class="record-row">
                                                    <td>{{ $key + $drivers->firstItem() }}</td>
                                                    <td class="name">
                                                        <a href="{{ route('admin.driver.show', ['id' => $driver->id]) }}"
                                                           class="media align-items-center gap-10">
                                                            <img loading="lazy"
                                                                 src="{{ onErrorImage(
                                                                        $driver?->profile_image,
                                                                        asset('storage/app/public/driver/profile') . '/' . $driver?->profile_image,
                                                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                        'driver/profile/',
                                                                    ) }}"
                                                                 class="rounded custom-box-size" alt=""
                                                                 style="--size: 20px">
                                                            <div class="media-body">{{ $driver?->first_name }}
                                                                {{ $driver?->last_name }}</div>
                                                        </a>
                                                    </td>
                                                    <td class="contact-info">
                                                        <div class="title-color"><a
                                                                    href="tel:{{ $driver->phone }}">{{ $driver->phone }}</a>
                                                        </div>
                                                        <div><a
                                                                    href="mailto:{{ $driver->email }}">{{ $driver->email }}</a>
                                                        </div>
                                                    </td>

                                                    <td class="profile-status">{{ $driver->completion_percent }}%</td>
                                                    <td class="level">{{ $driver->level?->name }}</td>
                                                    <td class="total-trip">{{ $driver->driverTrips->count() }}</td>
                                                    <td>
                                                        {{ set_currency_symbol($driver->userAccount->received_balance + $driver->userAccount->total_withdrawn) }}
                                                    </td>
                                                    @can('user_edit')
                                                        <td class="status">
                                                            <label class="switcher">
                                                                <input class="switcher_input status-change"
                                                                       type="checkbox"
                                                                       {{ $driver->is_active == 1 ? 'checked' : '' }}
                                                                       data-url="{{ route('admin.driver.update-status') }}"
                                                                       id="{{ $driver->id }}">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                    @endcan
                                                    <td class="action">
                                                        <div
                                                                class="d-flex justify-content-center gap-2 align-items-center">
                                                            @can('user_view')
                                                                <button data-bs-toggle="modal"
                                                                        data-bs-target="#approvedRejectedModal"
                                                                        data-id="{{$driver->id}}"
                                                                        class="btn btn-outline-info btn-action">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </button>
                                                                {{--    modal--}}
                                                                <div class="modal fade" id="approvedRejectedModal"
                                                                     tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-lg" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="exampleModalLabel">{{translate('driver_info')}}</h5>
                                                                                <div>
                                                                                    <button type="button"
                                                                                            class="btn-close"
                                                                                            data-bs-dismiss="modal"
                                                                                            aria-label="Close"></button>
                                                                                </div>
                                                                            </div>
                                                                            <form action="{{route('admin.driver.profile-update-request-approved-rejected', ['id' => $driver->id])}}"
                                                                                  method="post"
                                                                                  style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                                                                  id="approvedRejectedForm">
                                                                                @csrf
                                                                                <div class="modal-body">
                                                                                    <div class="row">
                                                                                        <div class="col-12">
                                                                                            <div class="d-flex justify-content-start gap-2">
                                                                                                <div class="profile-image rounded">
                                                                                                    <img src="{{ onErrorImage(
                                                                                                                $driver?->profile_image,
                                                                                                                asset('storage/app/public/driver/profile') . '/' . $driver?->profile_image,
                                                                                                                asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                                                                                'driver/profile/',
                                                                                                            ) }}"
                                                                                                         class="rounded dark-support custom-box-size"
                                                                                                         alt=""
                                                                                                         style="--size: 136px">
                                                                                                </div>
                                                                                                <div class="profile-image-content">
                                                                                                    <div class="d-flex flex-column align-items-start gap-1">
                                                                                                        <h6 class="mb-10">
                                                                                                            {{ $driver?->first_name . ' ' . $driver?->last_name }}
                                                                                                        </h6>
                                                                                                        <div class="d-flex gap-3 align-items-center mb-1">
                                                                                                            <div class="badge bg-primary text-capitalize">
                                                                                                                {{ $driver->level->name ?? translate('no_level_found') }}
                                                                                                            </div>
                                                                                                            <div class="d-flex align-items-center gap-2">
                                                                                                                {{ number_format($driver->receivedReviews->avg('rating'), 1) }}
                                                                                                                <i class="bi bi-star-fill text-warning"></i>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <a
                                                                                                                href="tel:{{ $driver->phone }}">{{ $driver->phone }}</a>
                                                                                                        <a
                                                                                                                href="mailto:{{ $driver->email }}">{{ $driver->email }}</a>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row mt-3">
                                                                                        <div class="col-md-6">
                                                                                            <h4 class="mb-3">{{translate('current_identity_image')}}</h4>
                                                                                            <div class="row">
                                                                                                @foreach($driver->old_identification_image as $image)
                                                                                                    <div class="col-md-6 p-2">
                                                                                                        <img class="img-fluid mb-3"
                                                                                                             src="{{ asset('storage/app/public/driver/identity/'.$image) }}"
                                                                                                             alt="Not found"
                                                                                                             width="240" height="240">
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-6">
                                                                                            <h4 class="mb-3">{{translate('request_for_new_identity_image')}}</h4>
                                                                                            <div class="row">
                                                                                                @foreach($driver->identification_image as $image)
                                                                                                    <div class="col-md-6 p-2">
                                                                                                        <img class="img-fluid mb-3"
                                                                                                             src="{{ asset('storage/app/public/driver/identity/'.$image) }}"
                                                                                                             alt="Not found"
                                                                                                             width="240" height="240">
                                                                                                    </div>
                                                                                                @endforeach
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit"
                                                                                            class="btn btn-secondary"
                                                                                            name="status"
                                                                                            value="rejected"
                                                                                    >{{translate('deny')}}</button>
                                                                                    <button type="submit"
                                                                                            class="btn btn-primary"
                                                                                            name="status"
                                                                                            value="approved">{{translate('Approved')}}</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endcan
                                                            <button data-id="approved-{{ $driver->id }}"
                                                                    data-message="{{ translate('are_you_want_to_approved_this_driver_identity_image_update_request?') }}"
                                                                    type="button"
                                                                    class="btn btn-outline-success btn-action form-alert-approved-rejected">
                                                                <i class="bi bi-check-circle-fill"></i>
                                                            </button>
                                                            <form
                                                                    action="{{ route('admin.driver.profile-update-request-approved-rejected', ['id' => $driver->id,'status'=>'approved']) }}"
                                                                    method="post" id="approved-{{ $driver->id }}"
                                                                    class="d-none">
                                                                @csrf
                                                                @method('post')
                                                            </form>
                                                            <button data-id="rejected-{{ $driver->id }}"
                                                                    data-message="{{ translate('are_you_want_to_rejected_this_driver_identity_image_update_request?') }}"
                                                                    type="button"
                                                                    class="btn btn-outline-danger btn-action form-alert-approved-rejected">
                                                                <i class="bi bi-x-circle-fill"></i>
                                                            </button>
                                                            <form
                                                                    action="{{ route('admin.driver.profile-update-request-approved-rejected', ['id' => $driver->id,'status'=>'rejected']) }}"
                                                                    method="post" id="rejected-{{ $driver->id }}"
                                                                    class="d-none">
                                                                @csrf
                                                                @method('post')
                                                            </form>
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

                                    <div
                                            class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                                        <p class="mb-0"></p>

                                        <div
                                                class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                            <div class="d-flex align-items-center gap-1">
                                            </div>
                                            {!! $drivers->links() !!}
                                        </div>
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
