<div class="table-responsive mt-3">
    <table class="table table-borderless align-middle table-hover">
        <thead class="table-light align-middle text-capitalize text-nowrap">
        <tr>
            <th class="text-center sl">{{translate('SL')}}</th>
            <th class="text-center trip-id">{{translate('trip_ID')}}</th>
            <th class="text-center date">{{translate('date')}}</th>
            <th class="text-center customer-name">{{translate('customer')}}</th>
            <th class="text-center driver">{{translate('driver')}}</th>
            <th class="text-center trip-cost">{{translate('trip_cost')}} ({{getSession('currency_symbol')}})</th>
            <th class="text-center coupon-discount">
                {{translate('coupon')}} <br /> {{translate('discount')}} ({{getSession('currency_symbol')}})
            </th>
            <th class="text-center additional-fee text-capitalize">
                {{translate('additional_fee')}} ({{getSession('currency_symbol')}})
            </th>
            <th class="text-center text-capitalize total-trip-cost">
                {{translate('total_trip')}} <br />  {{translate('cost')}} ({{getSession('currency_symbol')}})
            </th>
            <th class="text-center admin-commission">
                {{translate('admin')}} <br />  {{translate('commission')}} ({{getSession('currency_symbol')}})
            </th>
            <th class="text-center trip-status">{{translate('trip_payment_status')}}</th>
            <th class="text-center trip-status">{{translate('trip_status')}}</th>
            <th class="text-center action text-center">{{translate('action')}}</th>
        </tr>
        </thead>
        <tbody>
        @forelse($trips as $key => $trip)
            <tr>
                <td class="text-center sl">{{$trips->firstItem() + $key}}</td>
                <td class="text-center trip-id"><a href="{{route('admin.trip.show', ['type' => $type, 'id' => $trip->id, 'page' => 'summary'])}}">{{$trip->ref_id}}</a></td>
                <td class="text-center text-nowrap date">
                    <div dir="ltr">
                        {{date('d F Y', strtotime($trip->created_at))}}, <br /> {{date('h:i a', strtotime($trip->created_at))}}
                    </div>
                </td>
                <td class="text-center customer-name"><a target="_blank"
                       @if($trip->customer)
                           href="{{route('admin.customer.show', [$trip->customer?->id])}}"
                       @endif>
                        {{ $trip->customer?->id ? $trip->customer?->first_name. ' ' . $trip->customer?->last_name : translate('no_customer_assigned') }}
                    </a>
                </td>
                <td class="text-center text-capitalize driver">
                    <a target="_blank"
                       @if($trip->driver)
                           href="{{route('admin.driver.show', [$trip->driver?->id])}}"
                        @endif
                    >
                        {{ $trip->driver?->id ? $trip->driver?->first_name. ' ' . $trip->driver?->last_name : translate('no_driver_assigned') }}
                    </a>
                </td>
                <td class="text-center trip-cost">{{ getCurrencyFormat($trip->actual_fare)}}</td>
                <td class="text-center coupon-discount">{{getCurrencyFormat($trip->coupon_amount + 0)}}</td>
                <td class="text-center text-capitalize additional-fee">
                    <div>{{translate('delay_fee')}}: {{getCurrencyFormat($trip->fee?->delay_fee)}}</div>
                    <div>{{translate('idle_fee')}}: {{getCurrencyFormat($trip->fee?->idle_fee)}}</div>
                    <div>{{translate('cancellation_fee')}}: {{getCurrencyFormat($trip->fee?->cancellation_fee)}}</div>
                    <div>{{translate('Vat/Tax')}}: {{getCurrencyFormat($trip->fee?->vat_tax)}}</div>
                </td>
                <td class="text-center total-trip-cost">{{ getCurrencyFormat($trip->paid_fare) }}</td>
                <td class="text-center admin-commission">{{getCurrencyFormat($trip->fee?->admin_commission)}}</td>
                <td class="text-center trip-status"><span class="badge badge-{{ $trip->payment_status == PAID? 'primary' : 'warning' }}">{{translate($trip->payment_status)}}</span></td>
                <td class="text-center trip-status"><span class="badge badge-{{ $trip->current_status == 'completed'? 'primary' : 'warning' }}">{{translate($trip->current_status)}}</span></td>
                <td class="text-center action">
                    <div class="d-flex justify-content-center gap-2 align-items-center">
                        @can('trip_log')
                        <a href="{{route('admin.trip.show', ['id' => $trip->id, 'type' => Request::get('type'), 'page' => 'log'])}}" class="btn btn-outline-info btn-action">
                            <i class="bi bi-clock-fill"></i>
                        </a>
                        @endcan
                        @can('trip_view')
                        <a href="{{route('admin.trip.show', ['type' => $type, 'id' => $trip->id, 'page' => 'summary'])}}" class="btn btn-outline-info btn-action">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                        @endcan
{{--                        @can('trip_delete')--}}
{{--                        <button--}}
{{--                            data-id="delete-{{ $trip->id }}" data-message="{{ translate('want_to_delete_this_trip_request?') }}"--}}
{{--                            type="button" class="btn btn-outline-danger btn-action form-alert">--}}
{{--                            <i class="bi bi-trash-fill"></i>--}}
{{--                        </button>--}}
{{--                        <form action="{{route('admin.trip.delete', ['id' => $trip->id])}}" method="post" id="delete-{{$trip->id}}">--}}
{{--                            @csrf--}}
{{--                            @method('delete')--}}
{{--                        </form>--}}
{{--                        @endcan--}}
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
    <p class="mb-0"></p>
    {{$trips->render()}}
</div>
