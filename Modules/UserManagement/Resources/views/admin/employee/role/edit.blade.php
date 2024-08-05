@extends('adminmodule::layouts.master')

@section('title', translate('Employee_Attributes'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center gap-3 justify-content-between mb-3">
                <h2 class="fs-22">{{translate('Employee_Attributes')}}</h2>
            </div>

            <div class="card">
                <form id="form_data" action="{{route('admin.employee.role.update', ['id' => $role['id']])}}"
                      method="post">
                    @csrf
                    @method('put')
                    <div class="card-body">
                        <h6 class="fw-semibold text-primary text-uppercase mb-4">{{translate('update_role')}}</h6>

                        <div class="mb-4">
                            <label for="role-name" class="mb-2">
                                {{translate('role_name')}}</label>
                            <input type="text" name="name" value="{{$role['name']}}" class="form-control"
                                   placeholder="{{translate('Ex: Business Analyst')}}">
                        </div>

                        <div class="d-flex flex-wrap align-items-center column-gap-4 row-gap-2">
                            <h6 class="fw-medium mt-5 mb-3 text-capitalize">{{translate('available_modules')}}</h6>
                            <div class="d-flex flex-wrap align-items-center column-gap-4 row-gap-3">
                                <div class="col-2 pb-0">
                                    <label class="custom-checkbox">
                                        <input type="checkbox" id="select-all-modules" {{ count($role->modules ?? []) == count(MODULES) ? 'checked' : '' }}>
                                        {{ translate('Select_All') }}
                                    </label>
                                </div>
                                @foreach(MODULES as $key => $module)
                                    <div class="col-2 pb-0">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="modules[]"
                                                class="module-checkbox"   value="{{ $key }}" {{ in_array($key, $role->modules) ? 'checked' : '' }}>
                                            {{ translate($key) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex gap-3 flex-wrap justify-content-end mt-5">
                            <button type="submit"
                                    class="btn btn-primary text-uppercase">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
<script>
    document.getElementById('select-all-modules').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.module-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = this.checked;
        }, this);

        updateSelectAllStatus(); // Update the "Select All" checkbox status
    });

    document.querySelectorAll('.module-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            updateSelectAllStatus(); // Update the "Select All" checkbox status
        });
    });

    function updateSelectAllStatus() {
        var checkboxes = document.querySelectorAll('.module-checkbox');
        var selectAllCheckbox = document.getElementById('select-all-modules');

        var allChecked = true;
        var anyChecked = false;

        checkboxes.forEach(function(checkbox) {
            if (!checkbox.checked) {
                allChecked = false;
            } else {
                anyChecked = true;
            }
        });

        if (anyChecked) {
            selectAllCheckbox.checked = allChecked;
        } else {
            selectAllCheckbox.checked = false;
        }
    }
</script>
@endpush
