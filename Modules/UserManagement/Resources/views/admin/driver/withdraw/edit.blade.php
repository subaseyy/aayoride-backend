@extends('adminmodule::layouts.master')

@section('title', translate('Withdrawal_Methods'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h2 class="fs-22 mb-4 text-capitalize">{{translate('Update Withdraw Method')}}</h2>

                <form action="{{route('admin.driver.withdraw-method.update', [$method->id])}}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="">{{translate('Setup Method Info')}}</h5>
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip" data-bs-title="Choose your business location"></i>
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
                                               placeholder="{{ translate('select_method_name') }}"
                                               value="{{$method->method_name}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-2 align-items-center mb-3">
                                        <input type="checkbox"
                                               id="makeMethodDefault" {{$method['is_default'] ? 'checked' : ''}}>
                                        <label for="makeMethodDefault">
                                            {{translate("Make This Method Default")}}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4" id="custom-field-section">
                                @forelse($method->method_fields as $key => $field)

                                    <div class="bg-light p-4 rounded">
                                        <div class="row align-items-center">
                                            @if($key != 0)
                                            <div class="col-12">
                                                <div class="mb-1 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-outline-danger btn-action remove-field" data-value="${counter}">
                                                        <i class="tio-delete"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="col-md-4 mb-4">
                                                <label for="field_type{{$key}}" class="mb-2">{{translate('Input_Field_Type')}}
                                                    <span class="text-danger">*</span></label>
                                                <select class="form-control js-select" name="field_type[{{$key}}]" required
                                                        id="field_type{{$key}}">
                                                    <option value="string" {{$field['input_type'] == 'string' ? 'selected' : ''}}>{{translate('string')}}</option>
                                                    <option value="number" {{$field['input_type'] == 'number' ? 'selected' : ''}}>{{translate('number')}}</option>
                                                    <option value="date" {{$field['input_type'] == 'date' ? 'selected' : ''}}>{{translate('date')}}</option>
                                                    <option value="password" {{$field['input_type'] == 'password' ? 'selected' : ''}}>{{translate('password')}}</option>
                                                    <option value="email" {{$field['input_type'] == 'email' ? 'selected' : ''}}>{{translate('email')}}</option>
                                                    <option value="phone" {{$field['input_type'] == 'phone' ? 'selected' : ''}}>{{translate('phone')}}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label for="field_name{{$key}}" class="mb-2">{{translate('field_name')}}
                                                        <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="field_name[{{$key}}]"
                                                           placeholder="{{ translate('select_field_name') }}" value="{{$field['input_name']}}"
                                                           required id="field_name{{$key}}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label for="placeholder{{$key}}"
                                                           class="mb-2">{{translate('placeholder_text')}}
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="placeholder_text[{{$key}}]"
                                                           placeholder="{{ translate('select_placeholder_text') }}"
                                                           value="{{$field['placeholder']}}" id="placeholder"
                                                           required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex align-items-center gap-2 justify-content-end">
                                                    @if($field['is_required'])
                                                    <input type="checkbox" value="1"
                                                           name="is_required[{{$key}}]" id="flexCheckDefault__{{$key}}" checked>
                                                    @else
                                                        <input type="checkbox" value="1"
                                                               name="is_required[{{$key}}]" id="flexCheckDefault__{{$key}}">
                                                    @endif
                                                    <label for="flexCheckDefault__{{$key}}">
                                                        {{translate('make_this_field_required')}}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>

                            <div class="d-flex justify-content-end gap-3 flex-wrap mt-4">
                                <button type="submit" class="btn btn-primary demo_check">
                                    {{ translate('Update Information') }}
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
        $(".remove-field").on('click', function () {
            let fieldRowId = $(this).data('value')
            $(`#field-row--${fieldRowId}`).remove();
            counter--;
        })

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
        });

        function remove_field(selector) {
            $('#field-row--' + selector).remove()
        }
    </script>
@endpush
