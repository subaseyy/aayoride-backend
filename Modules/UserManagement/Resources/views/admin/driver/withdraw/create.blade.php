@extends('adminmodule::layouts.master')

@section('title', translate('withdrawalMethods'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="fs-22 mb-4 text-capitalize">{{translate('Add New Withdraw Method')}}</h2>

                <form action="{{route('admin.driver.withdraw-method.store')}}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="">{{translate('Setup Method Info')}}</h5>
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer" data-bs-toggle="tooltip" data-bs-title="Choose your business location"></i>
                                </div>

                                <button class="btn btn-primary text-capitalize" id="add-more-field">
                                    <i class="tio-add"></i>
                                    {{translate('add_More_Fields')}}
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row gy-4 align-items-end">
                                <div class="col-md-6">
                                    <div class="">
                                        <label for="method_name" class="mb-2">{{translate('method_name')}} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="method_name" id="method_name"
                                            placeholder="{{ translate('select_method_name') }}" value="" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 align-items-center mb-3">
                                        <input type="checkbox" id="makeMethodDefault" checked>
                                        <label for="makeMethodDefault">
                                            {{translate("Make This Method Default")}}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4" id="custom-field-section">
                                <div class="bg-light p-4 rounded">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 mb-4">
                                            <label for="field_type" class="mb-2">{{translate('Input_Field_Type')}}
                                                <span class="text-danger">*</span></label>
                                            <select class="form-control js-select" name="field_type[]" required
                                                    id="field_type">
                                                <option value="" selected
                                                        disabled>{{translate('Select_Input_Field_Type')}}</option>
                                                <option value="string">{{translate('string')}}</option>
                                                <option value="number">{{translate('number')}}</option>
                                                <option value="date">{{translate('date')}}</option>
                                                <option value="password">{{translate('password')}}</option>
                                                <option value="email">{{translate('email')}}</option>
                                                <option value="phone">{{translate('phone')}}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label for="field_name" class="mb-2">{{translate('field_name')}} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="field_name[]"
                                                    placeholder="{{ translate('select_field_name') }}" value="" required id="field_name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-4">
                                                <label for="placeholder" class="mb-2">{{translate('placeholder_text')}}
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="placeholder_text[]"
                                                    placeholder="{{ translate('select_placeholder_text') }}" value="" id="placeholder"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center gap-2 justify-content-end">
                                                <input type="checkbox" value="1"
                                                    name="is_required[0]" id="flexCheckDefault__0" checked>
                                                <label for="flexCheckDefault__0">
                                                    {{translate('make_this_field_required')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-3 flex-wrap mt-4">
{{--                                <button type="button" class="btn btn-secondary">--}}
{{--                                    {{ translate('reset') }}--}}
{{--                                </button>--}}
                                <button type="submit" class="btn btn-primary demo_check">
                                    {{ translate('Save Information') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection


@push('script')
    <script>
        jQuery(document).ready(function ($) {
            counter = 1;

            $('#add-more-field').on('click', function (event) {
                if (counter < 15) {
                    event.preventDefault();
                    $('#custom-field-section').append(
                        `<div class="bg-light p-4 rounded mt-4" id="field-row--${counter}">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="mb-1 d-flex justify-content-end">
                                        <button type="button" class="btn btn-outline-danger btn-action remove-field" data-value="${counter}">
                                            <i class="tio-delete"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <label for="field_type${counter}" class="mb-2">{{translate('Input_Field_Type')}}
                                        <span class="text-danger">*</span></label>
                                    <select class="form-control js-select" name="field_type[]" required id=field_type${counter}>
                                        <option value="" selected
                                                disabled>{{translate('Select_Input_Field_Type')}}</option>
                                        <option value="string">{{translate('string')}}</option>
                                        <option value="number">{{translate('number')}}</option>
                                        <option value="date">{{translate('date')}}</option>
                                        <option value="password">{{translate('password')}}</option>
                                        <option value="email">{{translate('email')}}</option>
                                        <option value="phone">{{translate('phone')}}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="field_name${counter}" class="mb-2">{{translate('field_name')}} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="field_name[]"
                                            placeholder="{{ translate('select_field_name') }}" value="" required id="field_name${counter}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="placeholder${counter}" class="mb-2">{{translate('placeholder_text')}}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="placeholder_text[]"
                                            placeholder="{{ translate('select_placeholder_text') }}" value="" id="placeholder${counter}"
                                            required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 justify-content-end">
                                        <input type="checkbox" value="1"
                                            name="is_required[${counter}]" id="flexCheckDefault__${counter}" checked>
                                        <label for="flexCheckDefault__${counter}">
                                            {{translate('make_this_field_required')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>`
                    );

                    $(".js-select").select2();
                    $(".remove-field").on('click', function () {
                        let fieldRowId = $(this).data('value')
                        $(`#field-row--${fieldRowId}`).remove();
                        counter--;
                    })

                    counter++;
                } else {
                    Swal.fire({
                        title: '{{translate('maximum_limit_reached')}}',
                        confirmButtonText: '{{translate('ok')}}',
                    });
                }
            })
            $('form').on('reset', function (event) {
                if (counter > 1) {
                    $('#custom-field-section').html("");
                    $('#method_name').val("");
                }

                counter = 1;
            })
        });
    </script>
@endpush
