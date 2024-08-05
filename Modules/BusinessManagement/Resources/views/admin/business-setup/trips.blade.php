@extends('adminmodule::layouts.master')

@section('title', translate('Business_Info'))

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4 text-capitalize">{{translate('business_management')}}</h2>
            <div class="col-12 mb-3">
                <div class="">
                    @include('businessmanagement::admin.business-setup.partials._business-setup-inline')
                </div>
            </div>
            <div class="card mb-3 text-capitalize">
                <form action="{{route('admin.business.setup.trip-fare.store')}}?type=trip_settings" id="trips_form"
                      method="POST">
                    @csrf

                    <div class="card-header">
                        <h5 class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-fill-gear"></i>
                            {{ translate('trips_settings') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row gy-3 pt-3 align-items-end">
                            <div class="col-sm-6">
                                <label class="mb-3 d-flex align-items-center fw-medium gap-2">
                                    {{ translate('add_route_between_pickup_&_destination') }}
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip"
                                       title="{{ translate('If Yes, customers can add routes between pickup and destination') }}">
                                    </i>
                                </label>
                                <div class="d-flex align-items-center form-control mb-4">
                                    <div class="flex-grow-1">
                                        <input required type="radio" id="add_intermediate_points1"
                                               name="add_intermediate_points"
                                               value="1" {{($settings->firstWhere('key_name', 'add_intermediate_points')->value?? 0) == 1 ? 'checked' : ''}}>
                                        <label for="add_intermediate_points1" class="media gap-2 align-items-center">
                                            <i class="tio-agenda-view-outlined text-muted"></i>
                                            <span class="media-body">{{ translate('yes') }}</span>
                                        </label>
                                    </div>

                                    <div class="flex-grow-1">
                                        <input required type="radio" id="add_intermediate_points"
                                               name="add_intermediate_points"
                                               value="0" {{($settings->firstWhere('key_name', 'add_intermediate_points')->value?? 0) == 0 ? 'checked' : ''}}>
                                        <label for="add_intermediate_points" class="media gap-2 align-items-center">
                                            <i class="tio-table text-muted"></i>
                                            <span class="media-body">{{ translate('no') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-4 text">
                                    <label for="trip_request_active_time"
                                           class="mb-3">{{ translate('trip_request_active_time ') }}</label>
                                    <div class="input-group_tooltip">
                                        <input required type="number" class="form-control" placeholder="Ex: 5"
                                               id="trip_request_active_time" name="trip_request_active_time"
                                               value="{{$settings->firstWhere('key_name', 'trip_request_active_time')?->value}}">
                                        <i class="bi bi-info-circle-fill text-primary tooltip-icon"
                                           data-bs-toggle="tooltip" data-bs-toggle="tooltip"
                                           data-bs-title="{{translate('Customers’ trip requests will be visible to drivers for the time (in minutes) you have set here') . '. '. translate('When the time is over, the requests get removed automatically.')}}"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 flex-wrap justify-content-end">
                            <button type="submit"
                                    class="btn btn-primary text-uppercase">{{ translate('submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card mb-3 text-capitalize">
                <div class="card-header">
                    <h5 class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-fill-gear"></i>
                        {{ translate('trips_cancellation_messages') }}
                        <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                           data-bs-toggle="tooltip"
                           title="{{ translate('changes_may_take_some_hours_in_app') }}"></i>
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.business.setup.trip-fare.cancellation_reason.store') }}"
                          method="post">
                        @csrf
                        <div class="row gy-3 pt-3 align-items-end">
                            <div class="col-sm-6 col-md-6">
                                <label for="title" class="mb-3 d-flex align-items-center fw-medium gap-2">
                                    {{ translate('trip_cancellation_reason') }} <small>({{translate('Max 150 character')}})</small>
                                    <i class="bi bi-info-circle-fill text-primary cursor-pointer"
                                       data-bs-toggle="tooltip"
                                       title="{{ translate('Driver & Customer cancel trip confirmation reason') }}">
                                    </i>
                                </label>
                                <input id="title" name="title" type="text"
                                       placeholder="{{translate('Ex : vehicle problem')}}" class="form-control"
                                       maxlength="150" required>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label for="cancellationType" class="mb-3 d-flex align-items-center fw-medium gap-2">
                                    {{ translate('cancellation_type') }}
                                </label>
                                <select class="js-select" id="cancellationType" name="cancellation_type"
                                        required>
                                    <option value="" disabled
                                            selected>{{translate('select_cancellation_type')}}</option>
                                    @foreach(CANCELLATION_TYPE as $key=> $item)
                                        <option value="{{$key}}">{{translate($item)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label for="userType" class="mb-3 d-flex align-items-center fw-medium gap-2">
                                    {{ translate('user_type') }}
                                </label>
                                <select class="js-select" id="userType" name="user_type" required>
                                    <option value="" disabled selected>{{translate('select_user_type')}}</option>
                                    <option value="driver">{{translate('driver')}}</option>
                                    <option value="customer">{{translate('customer')}}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-3 flex-wrap justify-content-end">
                                    {{--                                    <button class="btn btn-secondary text-uppercase" type="reset">--}}
                                    {{--                                        {{ translate('reset') }}--}}
                                    {{--                                    </button>--}}
                                    <button type="submit"
                                            class="btn btn-primary text-uppercase">{{ translate('submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="card">
                <div class="card-header border-0 d-flex flex-wrap gap-3 justify-content-between align-items-center">
                    <h5 class="d-flex align-items-center gap-2 m-0">
                        <i class="bi bi-person-fill-gear"></i>
                        {{ translate('trip_cancellation_reason_list') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle">
                            <thead class="table-light align-middle">
                            <tr>
                                <th class="sl">{{translate('SL')}}</th>
                                <th class="text-capitalize">{{translate('Reason')}}</th>
                                <th class="text-capitalize">{{translate('cancellation_type')}}</th>
                                <th class="text-capitalize">{{translate('user_type')}}</th>
                                <th class="text-capitalize">{{translate('Status')}}</th>
                                <th class="text-center action">{{translate('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($cancellationReasons as $key => $cancellationReason)
                                <tr>
                                    <td class="sl">{{ $key + $cancellationReasons->firstItem() }}</td>
                                    <td>
                                        {{$cancellationReason->title}}
                                    </td>
                                    <td>
                                        {{ CANCELLATION_TYPE[$cancellationReason->cancellation_type] }}
                                    </td>
                                    <td>
                                        {{ $cancellationReason->user_type == 'driver' ? translate('driver') : translate('customer') }}
                                        {{$cancellationReason->status}}
                                    </td>
                                    <td class="text-center">
                                        <label class="switcher mx-auto">
                                            <input class="switcher_input status-change"
                                                   data-url="{{ route('admin.business.setup.trip-fare.cancellation_reason.status') }}"
                                                   id="{{ $cancellationReason->id }}"
                                                   type="checkbox"
                                                   name="status" {{ $cancellationReason->is_active == 1 ? "checked": ""  }} >
                                            <span class="switcher_control"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2 align-items-center">
                                            <button class="btn btn-outline-primary btn-action editData"
                                                    data-id="{{$cancellationReason->id}}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                            <button data-id="delete-{{ $cancellationReason?->id }}"
                                                    data-message="{{ translate('want_to_delete_this_cancellation_reason?') }}"
                                                    type="button"
                                                    class="btn btn-outline-danger btn-action form-alert">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                            <form
                                                action="{{ route('admin.business.setup.trip-fare.cancellation_reason.delete', ['id' => $cancellationReason?->id]) }}"
                                                id="delete-{{ $cancellationReason?->id }}" method="post">
                                                @csrf
                                                @method('delete')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div
                                            class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                            <img
                                                src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}"
                                                alt="" width="100">
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
    </div>
    <div class="d-flex justify-content-end mt-3">
        {{ $cancellationReasons->links() }}
    </div>

    @foreach($cancellationReasons as $key => $cancellationReason)
    <div class="modal fade" id="editDataModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <!-- End Main Content -->
                @include('businessmanagement::admin.business-setup.edit-cancellation-reason')
            </div>
        </div>
    </div>
    @endforeach
@endsection

@push('script')
    <script>
        "use strict";

        let permission = false;
        @can('business_edit')
            permission = true;
        @endcan

        $('#trips_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
        $(document).ready(function () {
            $('.editData').click(function () {
                let id = $(this).data('id');
                let url = "{{ route('admin.business.setup.trip-fare.cancellation_reason.edit', ':id') }}";
                url = url.replace(':id', id);
                $.get({
                    url: url,
                    success: function (data) {
                        $('#editDataModal .modal-content').html(data);
                        $('#updateForm').removeClass('d-none');
                        $('#editDataModal').modal('show');
                    },
                    error: function (xhr, status, error) {
                        console.log(error);
                    }
                });
            });
        });

    </script>
@endpush
