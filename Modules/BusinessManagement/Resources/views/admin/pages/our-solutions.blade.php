@extends('adminmodule::layouts.master')

@section('title', translate('Landing_Page'))

@push('css_or_js')
@endpush

@section('content')
    @php($env = env('APP_MODE') == 'live' ? 'live' : 'test')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('landing_page_setup')}}</h2>
            @include('businessmanagement::admin.pages.partials._landing_page_inline_menu')

            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h6 class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-calendar"></i>
                            {{ translate('section_Title') }}
                        </h6>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.our-solutions.update-intro') }}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="{{OUR_SOLUTIONS_SECTION}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="title" class="mb-2">
                                        {{ translate('title') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{ $data?->value['title'] ?? '' }}"
                                           placeholder="{{ translate('Ex: Title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="subTitle" class="mb-2">
                                        {{ translate('sub_Title') }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="subTitle" name="sub_title"
                                           value="{{ $data?->value['sub_title'] ?? '' }}"
                                           placeholder="{{ translate('Ex: Sub_Title') }}" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-secondary text-uppercase" type="reset">
                                        {{ translate('reset') }}
                                    </button>
                                    <button class="btn btn-primary text-uppercase" type="submit">
                                        {{ translate('save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                        <h6 class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-calendar"></i>
                            {{ translate('section_Content') }}
                        </h6>
                    </div>

                    <form action="{{ route('admin.business.pages-media.landing-page.our-solutions.update') }}"
                          id="banner_form" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-4">
                                            <label for="solution_title" class="mb-2">
                                                {{ translate('Title') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control" id="solution_title" name="title"
                                                   placeholder="{{ translate('ex') }}: {{ translate('Ride_Sharing') }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="solution_description" class="mb-2">
                                                {{ translate('Description') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="description" id="solution_description" rows="4" class="form-control"
                                                      placeholder="{{ translate('ex') }}: {{ translate('Section_Description') }}"
                                                      required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="d-flex justify-content-center mb-3">
                                    <div class="d-flex flex-column gap-3 mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-capitalize">
                                                {{ translate('Icon / Image') }}
                                                <span class="text-danger">*</span>
                                            </h6>
                                            <span class="badge badge-primary">{{ '290x290 px' }}</span>
                                        </div>

                                        <div class="d-flex">
                                            <div class="upload-file">
                                                <input type="file" class="upload-file__input" name="image"
                                                       accept="image/png, image/jpeg, image/jpg" required>
                                                <div class="upload-file__img" style="--size: 11rem;">
                                                    <img alt=""
                                                        src="{{ asset('public/assets/admin-module/img/media/upload-file.png') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-secondary text-uppercase" type="reset">
                                        {{ translate('reset') }}
                                    </button>
                                    <button class="btn btn-primary text-uppercase" type="submit">
                                        {{ translate('save') }}
                                    </button>
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
                                    <th>{{ translate('Image') }}</th>
                                    <th>{{ translate('title') }}</th>
                                    <th>{{ translate('sub_Title') }}</th>
                                    <th class="text-center">{{ translate('Status') }}</th>
                                    <th class="text-center">{{ translate('Action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($ourSolutionList as $key => $singleSolution)
                                    <tr>
                                        <td>{{$key + $ourSolutionList->firstItem()}}</td>
                                        <td>
                                            <div class="aspect-1 d-flex align-items-center overflow-hidden rounded w-50px">
                                                <img class="h-100 fit-object"
                                                     src="{{ $singleSolution?->value['image'] ? asset('storage/app/public/business/landing-pages/our-solutions/'.$singleSolution?->value['image']) : asset('public/assets/admin-module/img/media/bike.png') }}"
                                                     alt="">
                                            </div>

                                        </td>
                                        <td>{{ $singleSolution?->value['title'] ?? "" }}</td>
                                        <td>{{ $singleSolution?->value['description'] ?? "" }}</td>
                                        <td class="text-center">
                                            <label class="switcher mx-auto">
                                                <input class="switcher_input status-change"
                                                       data-url="{{ route('admin.business.pages-media.landing-page.our-solutions.status') }}"
                                                       id="{{ $singleSolution?->id }}"
                                                       type="checkbox"
                                                       name="status" {{ $singleSolution?->value['status'] == 1 ? "checked": ""  }} >
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                <a href="{{ route('admin.business.pages-media.landing-page.our-solutions.edit',$singleSolution?->id) }}"
                                                   class="btn btn-outline-info btn-action" title="Edit coupon">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <button data-id="delete-{{ $singleSolution?->id }}"
                                                        data-message="{{ translate('want_to_delete_this_testimonial?') }}"
                                                        type="button"
                                                        class="btn btn-outline-danger btn-action form-alert">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>

                                                <form
                                                    action="{{ route('admin.business.pages-media.landing-page.our-solutions.delete', ['id' => $singleSolution?->id]) }}"
                                                    id="delete-{{ $singleSolution?->id }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
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
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $ourSolutionList->links() }}
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection
