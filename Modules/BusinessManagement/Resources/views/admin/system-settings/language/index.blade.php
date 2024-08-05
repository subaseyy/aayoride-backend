@extends('adminmodule::layouts.master')

@section('title', translate('Languages'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <h2 class="fs-22 mb-4 text-capitalize">{{translate('system_settings')}}</h2>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
        <div class="mb-3">
            @include('businessmanagement::admin.system-settings.partials._system-settings-inline')
        </div>
        <!-- End Inlile Menu -->

        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger mb-3" role="alert">
                            <i class="bi bi-info-circle-fill"></i>
                            {{translate('changing_some_settings_will_take_time_to_show_effect_please_clear_session_or_wait_for_60_minutes_else_browse_from_incognito_mode')}}
                        </div>

                        <div class="card">
                            <div class="px-3 py-4">
                                <div class="row justify-content-between align-items-center flex-grow-1">
                                    <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                        <h5 class="mb-0 d-flex">
                                            {{translate('language_table')}}
                                        </h5>
                                    </div>
                                    <div class="col-sm-8 col-md-6 col-lg-4">
                                        <div class="d-flex gap-10 justify-content-sm-end">
                                            <button class="btn btn-primary btn-icon-split" data-bs-toggle="modal"
                                                    data-bs-target="#lang-modal">
                                                <i class="bi bi-plus-circle-fill"></i>
                                                <span class="text">{{translate('add_new_language')}}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive datatable-custom" id="table-div">
                                <table id="datatable"
                                       class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{ translate('SL')}}</th>
                                        <th>{{translate('code')}}</th>
                                        <th>{{translate('direction')}}</th>
                                        <th class="text-center">{{translate('status')}}</th>
                                        <th class="text-center">{{translate('default_status')}}</th>
                                        <th class="text-center">{{translate('action')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if($language)
                                        @foreach($language->value as $key =>$data)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>{{$data['code']}}</td>
                                                <td>{{$data['direction']}}</td>
                                                <td>
                                                    <label class="switcher mx-auto">
                                                        <input type="checkbox"
                                                               data-url="{{route('admin.business.languages.update-status')}}"
                                                               data-code="{{$data['code']}}"
                                                               class="switcher_input language-status-change status_{{$data['code']}}" {{$data['status']==1?'checked':''}}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label class="switcher mx-auto">
                                                        <input type="checkbox"
                                                               data-url="{{route('admin.business.languages.update-default-status', ['code'=>$data['code']])}}"
                                                               class="switcher_input default-language-change" {{ ((array_key_exists('default', $data) && $data['default']) ? 'checked': ((array_key_exists('default', $data) && $data['default']==false) ? '' : 'disabled')) }}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="dropdown d-flex justify-content-center">
                                                        <button class="text-center btn btn-secondary dropdown-toggle"
                                                                type="button" id="dropdownMenuButton"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="bi bi-gear-fill"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @if($data['code']!='en')
                                                                <li>
                                                                    <a class="dropdown-item" data-bs-toggle="modal"
                                                                       data-bs-target="#lang-modal-update-{{$data['code']}}">{{translate('update')}}</a>
                                                                </li>
                                                                @if ($data['default'])
                                                                @else
                                                                    <li>
                                                                        <a class="dropdown-item delete"
                                                                           id="{{route('admin.business.languages.delete',[$data['code']])}}"
                                                                        >{{translate('delete')}}</a>
                                                                    </li>

                                                                @endif
                                                            @endif
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{route('admin.business.languages.translate',[$data['code']])}}">{{translate('translate')}}</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="lang-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{translate('new_language')}}</h5>
                    <div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <form action="{{route('admin.business.languages.add-new')}}" method="post"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                      id="language_form">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="form-group">
                                    <label for="message-text"
                                           class="col-form-label">{{translate('language')}} :</label>
                                    <select id="message-text" name="code" class="form-control js-select-modal">
                                        @foreach(LANGUAGES as $lang)
                                            <option value="{{$lang['code']}}">{{$lang['name']}}
                                                - {{$lang['nativeName']}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="name" id="lang_name">
                                </div>
                            </div>
                            <div class="col-12 mb-4">
                                <div class="form-group">
                                    <label class="col-form-label">{{translate('direction')}} :</label>
                                    <select class="form-control" name="direction">
                                        <option value="ltr">{{ translate('LTR') }}</option>
                                        <option value="rtl">{{ translate('RTL') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                aria-label="Close">{{translate('close')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('Add')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @if ($language)
        @foreach($language->value as $key =>$data)
            <div class="modal fade" id="lang-modal-update-{{$data['code']}}" tabindex="-1" role="dialog"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">{{translate('new_language')}}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('admin.business.languages.update')}}" method="post">
                            @csrf
                            <input type="hidden" name="code" value="{{$data['code']}}">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="lang_code"
                                                   class="col-form-label">{{translate('language')}}</label>
                                            <select name="code" id="lang_code" class="form-control js-select2-custom"
                                                    disabled>
                                                @foreach(LANGUAGES as $lang)
                                                    <option
                                                        value="{{$lang['code']}}" {{ $data['code']== $lang['code'] ?'selected':'' }}>
                                                        {{$lang['name']}} - {{$lang['nativeName']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="col-form-label">{{translate('direction')}} :</label>
                                            <select class="form-control" name="direction">
                                                <option
                                                    value="ltr" {{isset($data['direction'])?$data['direction']=='ltr'?'selected':'':''}}>
                                                    LTR
                                                </option>
                                                <option
                                                    value="rtl" {{isset($data['direction'])?$data['direction']=='rtl'?'selected':'':''}}>
                                                    RTL
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        aria-label="Close">{{translate('close')}}</button>

                                <button type="submit" class="btn btn-primary">{{translate('update')}} <i
                                        class="fa fa-plus"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/business-management/language/index.js') }}"></script>
    <!-- Page level custom scripts -->
    <script>
        "use strict";

        $(".delete").click(function (e) {
            e.preventDefault();

            Swal.fire({
                title: '{{translate('are_you_sure_to_delete_this')}}?',
                text: "{{translate('you_will_not_be_able_to_revert_this')}}!",
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: 'var(--bs-primary)',
                confirmButtonText: '{{translate('yes')}}, {{translate('delete_it')}}!'
            }).then((result) => {
                if (result.value) {
                    window.location.href = $(this).attr("id");
                }
            })
        });

    </script>
@endpush
