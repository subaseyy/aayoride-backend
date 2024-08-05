<ul class="common-list">
    @php($count = 0)
    @forelse($trips as $trip)
        @if ($count = 0)
            <li class="pt-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
        @endif
        @php($count++)
        <li class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div class="media align-items-center gap-3">
                <a href="{{ route('admin.customer.show', ['id' => $trip->customer_id]) }}">
                    <div class="avatar avatar-lg rounded">
                        <img src="{{ onErrorImage(
                                                $trip?->vehicleCategory?->image,
                                                asset('storage/app/public/vehicle/category') . '/' . $trip?->vehicleCategory?->image,
                                                asset('public/assets/admin-module/img/media/car.png'),
                                                'vehicle/category/',
                                            ) }}"
                             class="dark-support rounded custom-box-size"
                             alt="" style="--size: 42px">
                    </div>
                </a>

                <div class="media-body ">
                    <a href="{{ route('admin.trip.show', ['id' => $trip->id, 'page' => 'summary']) }}">
                        <h5 class="">{{ translate('trip') }}# {{ $trip->ref_id }}</h5>
                    </a>
                    @php($time_format = getSession('time_format'))
                    <p>{{ date(DATE_FORMAT, strtotime($trip->created_at)) }}</p>
                </div>
            </div>
            @if ($trip->current_status == PENDING || $trip->current_status == 'completed')
                <span
                    class="badge rounded-pill text-capitalize py-2 px-3 badge-success">{{ $trip->current_status }}</span>
            @elseif($trip->current_status == 'cancelled' || $trip->current_status == 'failed' || $trip->current_status == 'rejected')
                <span
                    class="badge rounded-pill text-capitalize py-2 px-3 badge-danger">{{ $trip->current_status }}</span>
            @else
                <span class="badge rounded-pill text-capitalize py-2 px-3 badge-info">{{ $trip->current_status }}</span>
            @endif
        </li>
    @empty
    @endforelse
</ul>
