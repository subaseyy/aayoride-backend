@extends('adminmodule::layouts.master')

@section('title', translate('Clean_Database'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <h2 class="fs-22 mb-4 text-capitalize">{{translate('system_settings')}}</h2>
        <!-- End Page Title -->

        <!-- Inline Menu -->
        <div class="mb-3">
            @include('businessmanagement::admin.system-settings.partials._system-settings-inline')
        </div>
        <!-- End Inline Menu -->


        <div class="row">
            <div class="col-12 mb-3">
                <div class="alert badge-danger mb-0 mx-sm-2" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    {{translate('This_page_contains_sensitive_information.Make_sure_before_changing.')}}
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-6 col-xl-3">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="checkbox" name="select_all" value="sasdgg" id="select-all-modules">
                                    <label class="text-dark text-capitalize custom-checkbox" for="select-all-modules">{{translate('select_all')}}</label>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.business.clean-database.clean') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @foreach($tables as $key=>$table)
                                    <div class="col-sm-6 col-xl-3">
                                        <div class="card table-card mb-4">
                                            <div class="card-body d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <input type="checkbox" name="tables[]" value="{{ $table }}" class="select_all module-checkbox"
                                                           id="table_{{ $key }}">
                                                    <label class="text-dark text-lowercase custom-checkbox" for="table_{{ $key }}">{{ $table }}</label>
                                                </div>
                                                <span class="badge badge-info">{{ \Illuminate\Support\Facades\DB::table($table)->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-end gap-10 flex-wrap mt-3">
                                <button type="{{ env('APP_MODE') != 'demo' ? 'submit' : 'button' }}"
                                        onclick="{{ env('APP_MODE') != 'demo' ? '' : 'call_demo()' }}"
                                        class="btn btn-primary">{{ translate('clear') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script
        src="{{ asset('public/assets/admin-module/js/business-management/system-settings/clean-database.js') }}"></script>

    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $("form").on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
                return;
            }
            e.preventDefault();
            Swal.fire({
                title: '{{translate('Are you sure?')}}',
                text: "{{translate('Sensitive_data! Make_sure_before_changing.')}}",
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    this.submit();
                } else {
                    e.preventDefault();
                }
            })
        });
    </script>
@endpush
