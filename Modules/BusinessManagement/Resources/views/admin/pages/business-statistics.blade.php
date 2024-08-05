@extends('adminmodule::layouts.master')

@section('title', translate('Landing_Page'))

@push('css_or_js')
@endpush

@section('content')
    @php($env = env('APP_MODE') == 'live' ? 'live' : 'test')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('landing_page')}}</h2>
            @include('businessmanagement::admin.pages.partials._landing_page_inline_menu')

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-30">
                        <h5 class="text-primary text-uppercase">{{ translate('Business_Statistic') }}</h5>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.business-statistics.update') }}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-lg-4">
                                <div class="mb-4">
                                    <h6 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="bi bi-calendar"></i>
                                        {{ translate('total_Download') }}
                                    </h6>
                                    <div class="rounded bg-light px-3 py-2">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h6 class="text-center text-capitalize fs-12 fw-medium">{{ translate('Icon / Image') }}
                                                (<span class="text-info">1:1</span>)</h6>
                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input"
                                                           accept="image/png, image/jpeg" name="total_download_image">
                                                    <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                                    <div class="upload-file__img w-auto h-auto" style="--size: 5rem">
                                                        <img width="80"
                                                             src="{{ $data?->value['total_download']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/total-download/'.$data?->value['total_download']['image']):asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="totalDownloadCount"
                                                   class="mb-2">{{ translate('Total Download Count') }}</label>
                                            <input type="text" class="form-control" id="totalDownloadCount"
                                                   name="total_download_count"
                                                   value="{{$data?->value['total_download']['count']??""}}"
                                                   placeholder="{{ translate('Ex: 5') }}" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="totalDownloadContent"
                                                   class="mb-2">{{ translate('Total Download Content') }}</label>
                                            <input type="text" class="form-control" id="totalDownloadContent"
                                                   name="total_download_content"
                                                   value="{{$data?->value['total_download']['content']??""}}"
                                                   placeholder="{{ translate('Ex: Download') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="mb-4">
                                    <h6 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="bi bi-calendar"></i>
                                        {{ translate('complete_Ride') }}
                                    </h6>
                                    <div class="rounded bg-light px-3 py-2">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h6 class="text-center text-capitalize fs-12 fw-medium">{{ translate('Icon / Image') }}
                                                (<span class="text-info">1:1</span>)</h6>
                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input"
                                                           accept="image/png, image/jpeg" name="complete_ride_image">
                                                    <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                                    <div class="upload-file__img w-auto h-auto" style="--size: 5rem">
                                                        <img width="80"
                                                             src="{{ $data?->value['complete_ride']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/complete-ride/'.$data?->value['complete_ride']['image']):asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="completeRideCount"
                                                   class="mb-2">{{ translate('Complete Ride Count') }}</label>
                                            <input type="text" class="form-control" id="completeRideCount"
                                                   name="complete_ride_count"
                                                   value="{{$data?->value['complete_ride']['count']??""}}"
                                                   placeholder="{{ translate('Ex: 5') }}" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="completeRideContent"
                                                   class="mb-2">{{ translate('Complete Ride Content') }}</label>
                                            <input type="text" class="form-control" id="completeRideContent"
                                                   name="complete_ride_content"
                                                   value="{{$data?->value['complete_ride']['content']??""}}"
                                                   placeholder="{{ translate('Ex: Complete Ride') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="mb-4">
                                    <h6 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="bi bi-calendar"></i>
                                        {{ translate('happy_Customer') }}
                                    </h6>
                                    <div class="rounded bg-light px-3 py-2">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h6 class="text-center text-capitalize fs-12 fw-medium">{{ translate('Icon / Image') }}
                                                (<span class="text-info">1:1</span>)</h6>
                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input"
                                                           accept="image/png, image/jpeg"
                                                           name="happy_customer_image">
                                                    <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                                    <div class="upload-file__img w-auto h-auto" style="--size: 5rem">
                                                        <img width="80"
                                                             src="{{ $data?->value['happy_customer']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/happy-customer/'.$data?->value['happy_customer']['image']):asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="happyCustomerCount"
                                                   class="mb-2">{{ translate('Happy Customer Count') }}</label>
                                            <input type="text" class="form-control" id="happyCustomerCount"
                                                   name="happy_customer_count"
                                                   value="{{$data?->value['happy_customer']['count']??""}}"
                                                   placeholder="{{ translate('Ex: 5') }}" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="happyCustomerContent"
                                                   class="mb-2">{{ translate('Happy Customer Content') }}</label>
                                            <input type="text" class="form-control" id="happyCustomerContent"
                                                   name="happy_customer_content"
                                                   value="{{$data?->value['happy_customer']['content']??""}}"
                                                   placeholder="{{ translate('Ex: Happy Customer') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <div class="mb-4">
                                    <h6 class="d-flex align-items-center gap-2 mb-3">
                                        <i class="bi bi-calendar"></i>
                                        {{ translate('24/7 Support') }}
                                    </h6>
                                    <div class="rounded bg-light px-3 py-2">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h6 class="text-center text-capitalize fs-12 fw-medium">{{ translate('Icon / Image') }}
                                                (<span class="text-info">1:1</span>)</h6>
                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input"
                                                           accept="image/png, image/jpeg" name="support_image">
                                                    <span class="edit-btn">
                                                    <i class="bi bi-pencil-square text-primary"></i>
                                                </span>
                                                    <div class="upload-file__img w-auto h-auto" style="--size: 5rem">
                                                        <img width="80"
                                                             src="{{ $data?->value['support']['image'] ? asset('storage/app/public/business/landing-pages/business-statistics/support/'.$data?->value['support']['image']):asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                             alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="supportTitle" class="mb-2">{{ translate('title') }}</label>
                                            <input type="text" class="form-control" id="supportTitle"
                                                   name="support_title"
                                                   value="{{$data?->value['support']['title']??""}}"
                                                   placeholder="{{ translate('Ex: Title') }}" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="supportContent"
                                                   class="mb-2">{{ translate('24/7 Support Content') }}</label>
                                            <input type="text" class="form-control" id="supportContent"
                                                   name="support_content"
                                                   value="{{$data?->value['support']['content']??""}}"
                                                   placeholder="{{ translate('Ex: 24/7 Support') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-secondary text-uppercase"
                                            type="reset">{{ translate('reset') }}</button>
                                    <button class="btn btn-primary text-uppercase"
                                            type="submit">{{ translate('save') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection


