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
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h5 class="text-primary text-uppercase">{{ translate('Testimonial') }}</h5>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.testimonial.update') }}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="reviewer_name"
                                                   class="mb-2">{{ translate('Reviewer_Name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="reviewer_name"
                                                   name="reviewer_name" placeholder="{{ translate('Ex: Ahmed') }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="designation" class="mb-2">{{ translate('Designation') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="designation" name="designation"
                                                   placeholder="{{ translate('Ex: Engineer') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="rating" class="mb-2">{{ translate('Rating') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" min="0" max="5" step=".1" class="form-control"
                                                   id="rating" name="rating"
                                                   placeholder="{{ translate('Ex: 5 Star') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="review" class="mb-2">{{ translate('Review') }} <span
                                                    class="text-danger">*</span></label>
                                            <textarea name="review" id="review" rows="4" class="form-control"
                                                      placeholder="{{ translate('Ex: review ...') }}"
                                                      required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center">
                                    <div class="d-flex flex-column gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-capitalize">{{ translate('Reviewer Image') }} <span
                                                    class="text-danger">*</span></h6>
                                            <span class="badge badge-primary">{{ translate('1:1') }}</span>
                                        </div>

                                        <div class="d-flex">
                                            <div class="upload-file">
                                                <input type="file" class="upload-file__input" name="reviewer_image"
                                                       accept="image/png, image/jpeg, image/jpg" required>
                                                <div class="upload-file__img">
                                                    <img
                                                        src="{{ asset('public/assets/admin-module/img/media/upload-file.png') }}"
                                                        alt="">
                                                </div>
                                            </div>
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

                    <div class="mt-4">
                        <div class="table-responsive mt-3">
                            <table class="table table-borderless align-middle table-hover col-mx-w300">
                                <thead class="table-light align-middle text-capitalize">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th class="text-center">{{ translate('Reviewer_Image') }}</th>
                                    <th>{{ translate('Reviewer_Name') }}</th>
                                    <th>{{ translate('Designation') }}</th>
                                    <th>{{ translate('Rating') }}</th>
                                    <th>{{ translate('Review') }}</th>
                                    <th class="text-center">{{ translate('Status') }}</th>
                                    <th class="text-center">{{ translate('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($testimonials as $key => $testimonial)
                                    <tr>
                                        <td>{{$key + $testimonials->firstItem()}}</td>
                                        <td>
                                            <div class="d-flex justify-content-center w-100">
                                                <div class="aspect-1 d-flex justify-content-center align-items-center overflow-hidden rounded w-50px">
                                                    <img class="h-100 fit-object"
                                                         src="{{ $testimonial?->value['reviewer_image'] ? asset('storage/app/public/business/landing-pages/testimonial/'.$testimonial?->value['reviewer_image']) : asset('public/assets/admin-module/img/media/bike.png') }}"
                                                         alt="">
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $testimonial?->value['reviewer_name'] ?? "" }}</td>
                                        <td>{{ $testimonial?->value['designation'] ?? "" }}</td>
                                        <td>{{ $testimonial?->value['rating'] ?? "" }}</td>
                                        <td>
                                            <div class="truncate-line2">{{ $testimonial?->value['review'] ?? "" }}</div>
                                        </td>
                                        <td class="text-center">
                                            <label class="switcher mx-auto">
                                                <input class="switcher_input status-change"
                                                       data-url="{{ route('admin.business.pages-media.landing-page.testimonial.status') }}"
                                                       id="{{ $testimonial?->id }}"
                                                       type="checkbox"
                                                       name="status" {{ $testimonial?->value['status'] == 1 ? "checked": ""  }} >
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                <a href="{{ route('admin.business.pages-media.landing-page.testimonial.edit',$testimonial->id) }}"
                                                   class="btn btn-outline-info btn-action" title="Edit coupon">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button data-id="delete-{{ $testimonial->id }}"
                                                        data-message="{{ translate('want_to_delete_this_testimonial?') }}"
                                                        type="button"
                                                        class="btn btn-outline-danger btn-action form-alert">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>

                                                <form
                                                    action="{{ route('admin.business.pages-media.landing-page.testimonial.delete', ['id' => $testimonial->id]) }}"
                                                    id="delete-{{ $testimonial->id }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6"
                                            class="text-center">{{ translate('no_data_available') }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $testimonials->links() }}
            </div>

        </div>
    </div>
    <!-- End Main Content -->
@endsection


