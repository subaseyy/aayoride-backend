@extends('adminmodule::layouts.master')

@section('title', translate('Update_Employee'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">{{ translate('update_employee') }}</h2>
            </div>

            <form action="{{ route('admin.employee.update',  $employee->id) }}" id='myForm' method="post"
                  enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="card">
                    <div class="card-body">
                        <div class="row gy-4">
                            <div class="col-lg-8">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('general_information') }}
                                </h5>

                                <div class="row align-items-end">
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label for="f_name"
                                                   class="mb-2 text-capitalize">{{ translate('first_name') }}</label>
                                            <input type="text" value="{{ $employee?->first_name }}" name="first_name"
                                                   id="f_name" class="form-control"
                                                   placeholder="{{ translate('Ex: Maximilian') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label for="l_name" class="mb-2">{{ translate('last_name') }}</label>
                                            <input type="text" value="{{ $employee?->last_name }}" name="last_name"
                                                   id="l_name" class="form-control"
                                                   placeholder="{{ translate('Ex: Schwarzmüller') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="phone_number" class="mb-2">{{ translate('phone') }}</label>
                                            <input type="tel" pattern="[0-9]{1,14}" value="{{ $employee->phone }}"
                                                   id="phone_number" class="form-control w-100 text-dir-start"
                                                   placeholder="{{ translate('Ex: xxxxx xxxxxx') }}" required>
                                            <input type="hidden" id="phone_number-hidden-element" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="address" class="mb-2">{{ translate('address') }}</label>
                                            <input type="text" name="address"
                                                   value="{{ $employeeAddress->address ?? '' }}" id="address"
                                                   class="form-control" placeholder="{{ translate('Ex: Dhaka') }}"
                                                   required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label for="identity_type"
                                                   class="mb-2">{{ translate('identity_type') }}</label>
                                            <select name="identification_type" class="js-select text-capitalize"
                                                    id="identity_type" required>
                                                <option value="passport"
                                                    {{ $employee->identification_type == 'passport' ? 'selected' : '' }}>
                                                    {{ translate('passport') }}</option>
                                                <option value="nid"
                                                    {{ $employee->identification_type == 'nid' ? 'selected' : '' }}>
                                                    {{ translate('NID') }}</option>
                                                <option value="driving_license"
                                                    {{ $employee->identification_type == 'driving_license' ? 'selected' : '' }}>
                                                    {{ translate('driving_license') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-4">
                                            <label for="identity_card_num"
                                                   class="mb-2">{{ translate('identity_number') }}</label>
                                            <input type="text" value="{{ $employee->identification_number }}"
                                                   name="identification_number" id="identity_card_num"
                                                   class="form-control"
                                                   placeholder="{{ translate('Ex: 3032') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-column justify-content-around gap-3">
                                            <h5 class="text-capitalize">{{ translate('identity_card_image') }}</h5>
                                            <div class="d-flex">
                                                <div class="upload-file" id="multi_image_picker">
                                                    <div class="upload-file__img upload-file__img_banner d-flex">
                                                        @foreach ($employee->identification_image as $img)
                                                            <img class="p-1"
                                                                 src="{{ asset('storage/app/public/employee/identity') }}/{{ $img }}">
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="d-flex flex-column justify-content-around gap-3">
                                    <h5 class="text-center text-capitalize">{{ translate('employee_image') }}</h5>

                                    <div class="d-flex justify-content-center">
                                        <div class="upload-file">
                                            <input type="file" name="profile_image" class="upload-file__input"
                                                   accept=".jpg, .jpeg, .png">
                                            <div class="upload-file__img w-auto h-auto">
                                                <img width="150"
                                                     src="{{ onErrorImage(
                                                        $employee?->profile_image,
                                                        asset('storage/app/public/employee/profile') . '/' . $employee?->profile_image,
                                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                                        'employee/profile/',
                                                    ) }}"
                                                     alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="opacity-75 mx-auto max-w220">
                                    {{ translate('File Format - jpg, png, jpeg') }}
                                    {{ translate('Image Size - Maximum Size 5 MB.') }}</p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="fw-semibold text-primary text-uppercase mb-4 text-capitalize">
                            {{ translate('employee_responsibility') }}</h6>
                        <div class="mb-4 max-w300">
                            <label for="employee-role" class="mb-2">{{ translate('employee_role') }}</label>
                            <select name="role_id" id="employee-role" class="form-control js-select" required>

                                <option>{{ translate('--Select_Role--') }}</option>

                                @forelse($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $employee->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}
                                    </option>
                                @empty
                                @endforelse
                            </select>
                        </div>

                        <div id="roles">
                            @if($employee?->role)

                                <h5 class="fw-semibold mt-5 mb-3 text-capitalize">{{ translate('module_access') }}</h5>
                                <div class="row g-3">
                                    <input type="hidden" name="role_id" value="{{ $role['id'] }}">
                                    @foreach ($employee?->role['modules'] as $key => $module)
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="badge-primary p-3">
                                                    <label class="custom-checkbox">
                                                        {{ translate($module) }}
                                                    </label>
                                                </div>
                                                <div
                                                    class="card-body d-flex flex-wrap align-items-center column-gap-4 row-gap-3">
                                                    @if (array_key_exists($module, MODULES))
                                                        @foreach (MODULES[$module] as $key => $permission)

                                                            @if (in_array($module, $employee->moduleAccess->pluck('module_name')->toArray()))
                                                                @foreach ($employee->moduleAccess as $moduleAccess)

                                                                    @if ($moduleAccess->module_name == $module)
                                                                        <label class="custom-checkbox">
                                                                            <input type="checkbox"
                                                                                   name="permission[{{ $module }}][]"
                                                                                   value="{{ $permission }}"
                                                                                {{ $moduleAccess->$permission == 1 ? 'checked' : '' }}>
                                                                            {{ translate($permission) }}
                                                                        </label>
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                <label class="custom-checkbox">
                                                                    <input type="checkbox"
                                                                           name="permission[{{ $module }}][]"
                                                                           value="{{ $permission }}">
                                                                    {{ translate($permission) }}
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>

                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="text-primary text-uppercase mb-4">{{ translate('account_information') }}</h5>

                        <div class="row align-items-end">
                            <div class="col-sm-4">
                                <div class="mb-4">
                                    <label for="p_email" class="mb-2">{{ translate('email') }}</label>
                                    <input type="email" value="{{ $employee->email }}" name="email" id="p_email"
                                           class="form-control"
                                           placeholder="{{ translate('Ex: company@company.com') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4 input-group_tooltip">
                                    <label for="password" class="mb-2">{{ translate('password') }}</label>
                                    <input type="password" name="password" id="password" class="form-control"
                                           placeholder="{{ translate('Ex: ********') }}">
                                    <i id="password-eye" class="mt-3 bi bi-eye-slash-fill text-primary tooltip-icon"
                                       data-bs-toggle="tooltip" data-bs-title=""></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-4 input-group_tooltip">
                                    <label for="confirm_password"
                                           class="mb-2">{{ translate('confirm_password') }}</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                           class="form-control" placeholder="{{ translate('Ex: ********') }}">
                                    <i id="conf-password-eye"
                                       class="mt-3 bi bi-eye-slash-fill text-primary tooltip-icon"
                                       data-bs-toggle="tooltip" data-bs-title=""></i>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-3 mt-3">
                    <button class="btn btn-primary" type="submit">{{ translate('save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- End Main Content -->

@endsection

@push('script')
    <link href="{{ asset('public/assets/admin-module/css/intlTelInput.min.css') }}" rel="stylesheet"/>
    <script src="{{ asset('public/assets/admin-module/js/intlTelInput.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin-module/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ asset('public/assets/admin-module/js/password.js') }}"></script>

    <script>
        "use strict";
        initializePhoneInput("#phone_number", "#phone_number-hidden-element");

        $("#multi_image_picker").spartanMultiImagePicker({
            fieldName: 'identity_images[]',
            maxCount: 2,
            rowHeight: '130px',
            groupClassName: 'upload-file__img upload-file__img_banner',
            dropFileLabel: "{{ translate('Drop_here') }}",
            placeholderImage: {
                image: "{{ asset('public/assets/admin-module/img/media/banner-upload-file.png') }}",
                width: '100%',
            },

            onRenderedPreview: function (index) {
                toastr.success('{{ translate('image_added') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            },
            onRemoveRow: function (index) {

            },
            onExtensionErr: function (index, file) {
                toastr.error('{{ translate('please_only_input_png_or_jpg_type_file') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            },
            onSizeErr: function (index, file) {
                toastr.error('{{ translate('file_size_too_big') }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        });
        $("#employee-role").on('change', function () {
            let value = $(this).val()
            loadRoles(value)
        })

        function loadRoles(obj) {

            $.ajax({
                url: '{{ route('admin.employee.role.get-roles') }}',
                _method: 'PUT',
                data: {
                    id: obj
                },
                success: function (data) {

                    $('#roles').empty().html(data.view)
                },
            });
        }
    </script>
@endpush
