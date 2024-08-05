<h5 class="fw-semibold mt-5 mb-3 text-capitalize">{{translate('module_access')}}</h5>

<div class="row g-3">
    <input type="hidden" name="role_id" value="{{$role['id']}}">
    @foreach($role['modules'] as $key => $module)
        <div class="col-lg-6">
            <div class="card">
                <div class="badge-primary p-3">
                    <label class="custom-checkbox">
                        {{translate($module)}}
                    </label>
                </div>
                <div class="card-body d-flex flex-wrap align-items-center column-gap-4 row-gap-3">

                    @if(array_key_exists($module, MODULES))
                        @foreach(MODULES[$module] as $permission)
                            <label class="custom-checkbox">
                                <input type="checkbox" name="permission[{{$module}}][]" value="{{$permission}}">
                                {{translate($permission)}}
                            </label>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
