<div class="table-responsive mt-3">
    <table class="table table-borderless align-middle table-hover">
        <thead class="table-light align-middle text-capitalize text-nowrap">
        <tr>
            <th>{{translate('SL')}}</th>
            <th>{{translate('trip_ID')}}</th>
            <th>{{translate('date')}}</th>
            <th>{{translate('customer')}}</th>
            <th>{{translate('driver')}}</th>
            <th>{{translate('trip_cost')}}({{session()->get('currency_symbol') ?? '$'}})</th>
            <th>{{translate('trip')}} <br /> {{translate('discount')}}({{session()->get('currency_symbol') ?? '$'}})</th>
            <th>{{translate('coupon')}} <br /> {{translate('discount')}}({{session()->get('currency_symbol') ?? '$'}})</th>
            <th class="text-capitalize">{{translate('additional_fee')}}({{session()->get('currency_symbol') ?? '$'}})</th>
            <th class="text-capitalize">{{translate('total_trip')}} <br />  {{translate('cost')}}({{session()->get('currency_symbol') ?? '$'}})</th>
            <th>{{translate('admin')}} <br />  {{translate('commission')}}({{session()->get('currency_symbol') ?? '$'}})</th>
            <th>{{translate('trip_status')}}</th>
            <th class="text-center">{{translate('action')}}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($trips as $key => $trip)
            @php( $trip->current_status == 'completed' ? $trip_amount = $trip->actual_fare : $trip_amount = $trip->estimated_fare )
            @php($total = $trip_amount - $trip->discount_amount - $trip->coupon_amount +
                    $trip->vat_tax + $trip->additional_charge + $trip->waiting_fee +
                    $trip->idle_fee + $trip->cancellation_fee)
            <tr>
                <td>{{$trips->firstItem() + $key}}</td>
                <td># {{$trip->ref_id}}</td>
                <td class="text-nowrap">{{date('d F Y', strtotime($trip->created_at))}}, <br /> {{date('h:i a', strtotime($trip->created_at))}}</td>
                @php($c_name = $trip->customer?->id ? $trip->customer?->first_name. ' ' . $trip->customer?->last_name : 'no_customer_assigned')
                <td><a target="_blank" href="{{route('admin.customer.show', [$trip->customer?->id])}}">{{$c_name}}</a></td>
                @php($d_name = $trip->driver?->id ? $trip->driver?->first_name. ' ' . $trip->driver?->last_name : 'no_driver_assigned')
                <td class="text-capitalize">
                    @if($trip->driver)
                        <a target="_blank" href="{{route('admin.driver.show', [$trip->driver?->id])}}"
                        >{{$trip->driver?->first_name. ' ' . $trip->driver?->last_name}}</a>
                        {{translate($d_name)}}</a>
                    @endif

                </td>
                <td>{{$trip->current_status == 'completed' ? $trip->paid_fare + 0 : $trip->estimated_fare + 0}}</td>
                <td>{{$trip->discount_amount + 0}}</td>
                <td>{{$trip->coupon_amount + 0}}</td>
                <td class="min-w200 text-capitalize">
                    <div>{{translate('waiting_fee')}} : ${{$trip->waiting_fee}}</div>
                    <div>{{translate('idle_fee')}}: ${{$trip->idle_fee}}</div>
                    <div>{{translate('cancellation_fee')}}: ${{$trip->cancellation_fee}}</div>
                </td>
                <td>{{$total}}</td>
                <td>15</td>
                <td><span class="badge badge-{{ $trip->current_status == 'completed'? 'primary' : 'warning' }}">{{translate($trip->current_status)}}</span></td>
                <td>
                    <div class="d-flex justify-content-center gap-2 align-items-center">
                        <button type="button" class="btn btn-outline-primary btn-action" data-bs-toggle="modal"
                                data-bs-target="#activityLogModal">
                            <i class="bi bi-clock-fill"></i>
                        </button>
                        <a href="{{route('admin.trip.show', ['type' => $type, 'id' => $trip->id, 'page' => 'summary'])}}" class="btn btn-outline-info btn-action">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        <button
                            data-id="delete-{{ $trip->id }}" data-message="{{ translate('want_to_delete_this_trip_request?') }}"
                            type="button" class="btn btn-outline-danger btn-action form-alert">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                        <form action="{{route('admin.trip.delete', ['id' => $trip->id])}}" method="post" id="delete-{{$trip->id}}">
                            @csrf
                            @method('delete')
                        </form>
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

<div class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
    <p class="mb-0">
    </p>
    {{$trips->render()}}
</div>
