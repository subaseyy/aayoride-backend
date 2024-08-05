@section('title', translate('banner_Setup'))

@extends('adminmodule::layouts.master')

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                <h2 class="fs-22 mt-4 mb-3 text-capitalize">{{ translate('deleted_banner_list') }}</h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted text-capitalize">{{ translate('total_banners') }} : </span>
                    <span class="text-primary fs-16 fw-bold">{{ $banners->total() }}</span>
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
                                    <tr id="hide-row-{{ $banner->id }}">
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
                                        <td class="redirect_link fs-10 text-nowrap max-w220 overflow-hidden text-truncate"><a href="{{ $banner->redirect_link }}">{{ $banner->redirect_link }}</a></td>
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
                                                <button
                                                    data-route="{{ route('admin.promotion.banner-setup.restore', $banner->id) }}"
                                                    data-message="{{ translate('Want_to_recover_this_banner?_') . translate('if_yes,_this_banner_will_be_available_again_in_the_Banner_List') }}"
                                                    class="btn btn-outline-primary btn-action restore-data">
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                                <button data-id="delete-{{ $banner->id }}"
                                                    data-message="{{ translate('want_to_permanent_delete_this_banner?') }} {{ translate('you_cannot_revert_this_action') }}"
                                                    type="button" class="btn btn-outline-danger btn-action form-alert">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>

                                                <form
                                                    action="{{ route('admin.promotion.banner-setup.permanent-delete', ['id' => $banner->id]) }}"
                                                    id="delete-{{ $banner->id }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            </div>
                                        </td>
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

                    <div class="d-flex justify-content-end">
                        {!! $banners->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
