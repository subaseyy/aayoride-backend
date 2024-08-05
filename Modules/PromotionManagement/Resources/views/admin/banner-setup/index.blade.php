@section('title', translate('banner_Setup'))

@extends('adminmodule::layouts.master')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('promotion_add')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                            <h5 class="text-primary text-uppercase">{{ translate('add_new_banner') }}</h5>
                        </div>

                        <form action="{{ route('admin.promotion.banner-setup.store') }}" id="banner_form"
                            enctype="multipart/form-data" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <label for="banner_title" class="mb-2">{{ translate('banner_title') }}</label>
                                        <input type="text" value="{{ old('banner_title') }}" class="form-control"
                                            id="banner_title" name="banner_title" placeholder="Ex: 50% Off" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="sort_description"
                                            class="mb-2">{{ translate('short_description') }}</label>
                                        <textarea name="short_desc" id="sort_description" placeholder="Type Here..." class="form-control" cols="30"
                                            rows="6" required>{{ old('short_desc') }}</textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label for="redirect_link" class="mb-2">{{ translate('redirect_link') }}</label>
                                        <input type="text" class="form-control" value="{{ old('redirect_link') }}"
                                            id="redirect_link" name="redirect_link" placeholder="Ex: www.google.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex flex-column justify-content-around align-items-center gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <h5 class="text-capitalize">{{ translate('banner_image') }}</h5>
                                        </div>

                                        <div class="d-flex">
                                            <div class="upload-file">
                                                <input type="file" class="upload-file__input" name="banner_image" accept=".jpg, .jpeg, .png" required>
                                                <div class="upload-file__img upload-file__img_banner">
                                                    <img src="{{ asset('public/assets/admin-module/img/media/banner-upload-file.png') }}"
                                                        alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <p class="opacity-75 mx-auto max-w220">
                                            {{ translate('File Format - jpg, .jpeg, .png Image Size - Maximum Size 5 MB. Image Ratio - 3:1') }}
                                        </p>
                                    </div>

                                    <div class="mb-4 text-capitalize">
                                        <label for="time_period" class="mb-2">{{ translate('time_period') }}</label>
                                        <select name="time_period" class="js-select" id="time_period" aria-label="{{ translate('time_period') }}" required onchange="toggleDatePick()">
                                            <option value="" disabled selected>{{ translate('select_time_period') }}</option>
                                            <option value="all_time">{{ translate('all_time') }}</option>
                                            <option value="period">{{ translate('period') }}</option>
                                        </select>
                                    </div>

                                    <div class="row date-pick" id="datePickContainer" style="display: none;">
                                        <div class="col-sm-6">
                                            <div class="mb-4">
                                                <label for="start_date" class="mb-2">{{ translate('start_date') }}</label>
                                                <input type="date" value="{{ old('start_date') }}" name="start_date" id="start_date" min="{{date('Y-m-d',strtotime(now()))}}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-4">
                                                <label for="end_date" class="mb-2">{{ translate('end_date') }}</label>
                                                <input type="date" value="{{ old('end_date') }}" name="end_date" id="end_date" min="{{date('Y-m-d',strtotime(now()))}}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-3">
                                        <button class="btn btn-primary text-uppercase"
                                            type="submit">{{ translate('submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endcan

            <h2 class="fs-22 mt-4 mb-3 text-capitalize">{{ translate('banner_list') }}</h2>

            <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ !request()->has('status') || request()->get('status') == 'all' ? 'active' : '' }}"
                            href="{{ url()->current() }}?status=all">
                            {{ translate('all') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ request()->get('status') == 'active' ? 'active' : '' }}"
                            href="{{ url()->current() }}?status=active">
                            {{ translate('active') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ request()->get('status') == 'inactive' ? 'active' : '' }}"
                            href="{{ url()->current() }}?status=inactive">
                            {{ translate('inactive') }}
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-capitalize">{{ translate('total_banners') }} : </span>
                    <span class="text-primary fs-16 fw-bold" id="total_record_count">{{ $banners->total() }}</span>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                        <form action="{{ url()->full() }}" class="search-form search-form_style-two" method="GET">
                            <div class="input-group search-form__input_group">
                                <span class="search-form__icon">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="search" class="theme-input-style search-form__input"
                                    value="{{ request()->get('search') }}" name="search" id="search"
                                    placeholder="{{ translate('search_here_by_Banner_Title') }}">
                            </div>
                            <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                        </form>


                        <div class="d-flex flex-wrap gap-3">
                            @can('super-admin')
                                <a href="{{ route('admin.promotion.banner-setup.index',['status'=>request('status')]) }}" class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                    <i class="bi bi-arrow-repeat"></i>
                                </a>

                                <a href="{{ route('admin.promotion.banner-setup.trashed') }}"
                                   class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                    <i class="bi bi-recycle"></i>
                                </a>
                            @endcan

                            @can('promotion_log')
                                <a href="{{ route('admin.promotion.banner-setup.log') }}" class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                    <i class="bi bi-clock-fill"></i>
                                </a>
                            @endcan

                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-borderless align-middle text-center">
                            <thead class="table-light align-middle text-nowrap">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th class="text-capitalize name">{{ translate('banner_title') }}</th>
                                    <th class="text-capitalize banner_image">{{ translate('banner_image') }}</th>
                                    <th class="text-capitalize redirect_link">{{ translate('redirect_link') }}</th>
                                    <th class="text-capitalize redirection_count">{{ translate('number_of') }}
                                        <br> {{ translate('total_direction') }}
                                    </th>
                                    <th class="text-capitalize time_period">{{ translate('time_period') }}</th>
                                    <th class="status">{{ translate('status') }}</th>
                                    <th class="text-center action">{{ translate('action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($banners as $key => $banner)
                                    <tr id="hide-row-{{ $banner->id }}" class="record-row">
                                        <td>{{ $banners->firstItem() + $key }}</td>
                                        <td class="name">{{ $banner->name }}</td>
                                        <td class="banner_image">
                                            <img src="{{ onErrorImage(
                                                $banner?->image,
                                                asset('storage/app/public/promotion/banner') . '/' . $banner?->image,
                                                asset('public/assets/admin-module/img/media/banner-upload-file.png'),
                                                'promotion/banner/',
                                            ) }}"
                                                class="custom-box-size-banner rounded dark-support" alt="">
                                        </td>
                                        <td class="redirect_link"><span
                                                class="fs-10 text-break">{{ $banner->redirect_link }}</span></td>
                                        <td class="redirection_count">{{ (int) $banner->total_redirection }}</td>
                                        @if ($banner->time_period == 'period')
                                            <td class="time_period">{{ $banner->start_date }} {{ translate('to') }}
                                                {{ $banner->end_date }}</td>
                                        @else
                                            <td class="time_period text-capitalize">
                                                {{ str_replace('_', ' ', $banner->time_period) }} </td>
                                        @endif
                                        @can('promotion_edit')
                                            <td class="status">
                                                <label class="switcher mx-auto">
                                                    <input class="switcher_input status-change"
                                                        data-url={{ route('admin.promotion.banner-setup.status') }}
                                                        id="{{ $banner->id }}" type="checkbox"
                                                        {{ $banner->is_active ? 'checked' : '' }}>
                                                    <span class="switcher_control"></span>
                                                </label>
                                            </td>
                                        @endcan
                                        <td class="action">
                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                @can('promotion_log')
                                                    <a href="{{ route('admin.promotion.banner-setup.log') }}?id={{ $banner->id }}"
                                                        class="btn btn-outline-primary btn-action">
                                                        <i class="bi bi-clock-fill"></i>
                                                    </a>
                                                @endcan

                                                @can('promotion_edit')
                                                    <a href="{{ route('admin.promotion.banner-setup.edit', ['id' => $banner->id]) }}"
                                                        class="btn btn-outline-info btn-action">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                @endcan

                                                @can('promotion_delete')
                                                    <button data-id="delete-{{ $banner->id }}"
                                                        data-message="{{ translate('want_to_delete_this_banner?') }}"
                                                        type="button" class="btn btn-outline-danger btn-action form-alert">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>

                                                    <form
                                                        action="{{ route('admin.promotion.banner-setup.delete', ['id' => $banner->id]) }}"
                                                        id="delete-{{ $banner->id }}" method="post">
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
                        {!! $banners->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/promotion-management/banner-setup/index.js') }}"></script>
    <script>
        "use strict";

        function toggleDatePick() {
        var timePeriodSelect = document.getElementById('time_period');
        var datePickContainer = document.getElementById('datePickContainer');

        // Check if the selected value is "all_time"
        if (timePeriodSelect.value === 'all_time') {
            datePickContainer.style.display = 'none';  // Hide the date-pick container
        } else {
            datePickContainer.style.display = 'flex'; // Show the date-pick container
        }
    }

        $('#banner_form').submit(function(e) {
            var timePeriod = $('#time_period').val();

            if (timePeriod === 'period' && $('#start_date').val() === '') {
                toastr.error('{{ translate('please_select_start_date') }}');
                e.preventDefault();
            }

            if (timePeriod === 'period' && $('#end_date').val() === '') {
                toastr.error('{{ translate('please_select_end_date') }}');
                e.preventDefault();
            }

            if (!timePeriod) {
                toastr.error('{{ translate('please_select_time_period') }}');
                e.preventDefault();
            }
        });
    </script>
@endpush
