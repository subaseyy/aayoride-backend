@extends('adminmodule::layouts.master')

@section('title', translate('Social_Media_Links'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4">{{translate('Social_Media_Links')}}</h2>

            <div class="card mb-30">
                <div class="card-body">
                    <form id="data-form" action="{{route('admin.business.pages-media.store-social-link')}}"
                          method="post">
                        @csrf
                        <h5 class="text-primary mb-4 text-uppercase">{{translate('add_new_link')}}</h5>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-4 selDiv">
                                    <label for="social_media_name" class="mb-2">{{translate('social_media_name')}}
                                        <span class="text-danger">*</span></label>
                                    <select name="name" id="social_media_name" class="js-select" required>
                                        <option value="" selected disabled>-- {{translate('Select_Social_Media')}}
                                            --
                                        </option>
                                        <option value="facebook">{{translate('facebook')}}</option>
                                        <option value="twitter">{{translate('twitter')}}</option>
                                        <option value="linkedin">{{translate('LinkedIn')}}</option>
                                        <option value="instagram">{{translate('instagram')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="redirect_link" class="mb-2">{{translate('redirect_link')}} <span
                                            class="text-danger">*</span></label>
                                    <input name="link" type="text" id="redirect_link" class="form-control" required
                                           placeholder="{{translate('Ex: https://www.facebook.com/6amtech')}}">
                                </div>
                            </div>
                            <input type="hidden" id="id" name="id">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button class="btn btn-primary" id="save-btn"
                                            type="submit">{{translate('submit')}}</button>
                                    <button class="btn btn-primary d-none" id="update-btn"
                                            type="submit">{{translate('update')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <h2 class="fs-22 text-capitalize">{{translate('social_media_link_list')}}</h2>

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
                    <span class="text-muted">{{translate('total')}}:</span>
                    <span class="text-primary fs-16 fw-bold">{{$links->total()}}</span>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                <form action="javascript:;"
                                      class="search-form search-form_style-two" method="get">
                                    <div class="input-group search-form__input_group">
                                            <span class="search-form__icon">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        <input type="search" id="search" name="search"
                                               value="{{request()->get('search')}}"
                                               class="theme-input-style search-form__input"
                                               placeholder="Search Here">
                                    </div>
                                    <button type="submit" class="btn btn-primary search-submit"
                                            data-url="{{ url()->full() }}">{{translate('search')}}</button>
                                </form>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="{{route('admin.business.pages-media.log')}}"
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-clock-fill"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="table-responsive mt-3">
                                <table class="table table-borderless align-middle">
                                    <thead class="table-light align-middle">
                                    <tr>
                                        <th>{{translate('SL')}}</th>
                                        <th class="text-capitalize">{{translate('social_media_name')}}</th>
                                        <th class="text-capitalize">{{translate('redirect_link')}}</th>
                                        @can('business_edit')
                                            <th>{{translate('status')}}</th>
                                        @endcan
                                        <th class="text-center">{{translate('action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($links as $key => $link)
                                        <tr>
                                            <td>{{$links->firstItem() + $key}}</td>
                                            <td id="row-name" class="text-capitalize">{{$link->name}}</td>
                                            <td id="row-link">{{$link->link}}</td>
                                            @can('business_edit')
                                                <td>
                                                    <label class="switcher">
                                                        <input class="switcher_input status-change"

                                                               id="{{$link->id}}"
                                                               data-url="{{route('admin.business.pages-media.update-social-link-status')}}"
                                                               type="checkbox"
                                                            {{$link->is_active == 1? 'checked' : ''}}
                                                        >
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </td>
                                            @endcan
                                            <td>
                                                <div
                                                    class="d-flex justify-content-center gap-2 align-items-center">
                                                    @can('business_view')
                                                        <a href="{{route('admin.business.pages-media.log')}}?id={{$link->id}}"
                                                           class="btn btn-outline-primary btn-action">
                                                            <i class="bi bi-clock-fill"></i>
                                                        </a>
                                                    @endcan
                                                    @can('business_edit')
                                                        <a id=""
                                                           data-id="{{$link->id}}"
                                                           data-name="{{$link->name}}"
                                                           data-link="{{$link->link}}"
                                                           class="update-information btn btn-outline-info btn-action">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                    @endcan
                                                    @can('business_edit')
                                                        <button type="button"
                                                                data-id="delete-{{ $link->id }}"
                                                                data-message="{{ translate('want_to_delete_this_link?') }}"
                                                                class="btn btn-outline-danger btn-action form-alert">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                        <form
                                                            action="{{route('admin.business.pages-media.delete-social-link')}}"
                                                            method="post" id="delete-{{$link->id}}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{$link->id}}">
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
                                {!! $links->links() !!}
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
    <script src="{{ asset('public/assets/admin-module/js/business-management/pages/social-media.js') }}"></script>

    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#data-form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });

        $('#update-btn').on('click', function () {
            $("#data-form").submit(function (e) {
                e.preventDefault();
            });
            let id = $('#id').val();
            let name = $('#social_media_name').val();
            let link = $('#redirect_link').val();
            let url = "{{route('admin.business.pages-media.update-social-link',":id")}}";
            url = url.replace(':id', id);
            if (name === "") {
                toastr.error('{{translate('social_name_is_required')}}.');
                return false;
            }
            if (link === "") {
                toastr.error('{{translate('social_link_is_required')}}.');
                return false;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    name: name,
                    link: link
                },
                beforeSend: function () {
                },
                success: function (response) {
                    setInterval(function () {
                        toastr.error('{{translate('update_failed')}}');
                    }, 5000)
                    toastr.success('{{translate('updated_successfully')}}');
                    location.reload()
                },
                error: function (xhr, status, error) {
                    $('.preloader').addClass('d-none')
                    toastr.error('{{translate('update_failed')}}');
                }
            })
        })
    </script>
@endpush
