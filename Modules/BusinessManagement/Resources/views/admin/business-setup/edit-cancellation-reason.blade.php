<form
    action="{{route('admin.business.setup.trip-fare.cancellation_reason.update', ['id' => $cancellationReason?->id])}}"
    method="post" id="updateForm" class="d-none">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-fill-gear"></i>
            {{translate('trip_cancellation_reason_update')}}
            <i class="bi bi-info-circle-fill text-primary cursor-pointer"
               data-bs-toggle="tooltip"
               title="{{ translate('changes_may_take_some_hours_in_app') }}"></i>
        </h5>
        <button type="button" class="btn-close outline-none shadow-none" data-bs-dismiss="modal"
                aria-label="Close">
        </button>
    </div>
    <div class="modal-body">
        <div class="form-group mb-3 add_active_2 update-lang_form">
            <label for="updateTitle" class="form-label">{{translate('trip_cancellation_reason')}} <small>({{translate('Max 150 character')}})</small></label>
            <input id="updateTitle" class="form-control" name="title" maxlength="150" value="{{$cancellationReason->title}}"
                   type="text" required>
        </div>
        <div class="form-group mb-3 add_active_2 update-lang_form">
            <label for="updateCancellationType" class="form-label">{{translate('cancellation_type')}}</label>
            <select class="form-control h--45px js-select" id="updateCancellationType" name="cancellation_type"
                    required>
                @foreach(CANCELLATION_TYPE as $key=> $item)
                    <option
                        value="{{$key}}" {{$cancellationReason->cancellation_type == $key ? 'selected' : ''}}>{{translate($item)}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3 add_active_2 update-lang_form">
            <label for="updateUserType" class="form-label">{{ translate('user_type') }}</label>
            <select id="updateUserType" name="user_type" class="form-control h--45px js-select" required="">
                <option value="driver" {{$cancellationReason->user_type == "driver" ? 'selected' : ''}}>
                    Driver
                </option>
                <option value="customer" {{$cancellationReason->user_type == "customer" ? 'selected' : ''}}>
                    Customer
                </option>
            </select>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('close')}}</button>
        <button type="submit" class="btn btn-primary">{{translate('update')}}</button>
    </div>
</form>

