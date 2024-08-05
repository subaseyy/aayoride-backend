<a href="#" class="header-icon count-btn notification-icon" data-bs-toggle="dropdown">
    <i class="bi bi-bell-fill"></i>
    @if($notification->count() != 0)
        <span class="count">{{$notification->count()}}</span>
    @endif
</a>

<div class="dropdown-menu dropdown-menu-right">
    <ul class="d-flex flex-column gap-2 list-group-notification">
        <a id="0" href="#" data-value="0"
           class="dropdown-item-text media gap-3 align-items-center seen-notification">
            <h1><i class="bi bi-check2-all"></i></h1>
            <div class="media-body ">
                <h6 class="card-title">{{ translate('mark_all_as_read')}}</h6>
            </div>
        </a>
        @forelse($notification as $notification)
            <a id="{{$notification->id}}" data-value="{{ $notification->id }}"
               @if($notification->message == 'new_customer_registered')
                   href="{{route('admin.customer.show', ['id' => $notification->model_id])}}"
               @elseif($notification->message == 'new_driver_registered')
                   href="{{route('admin.driver.show', ['id' => $notification->model_id])}}"
               @elseif($notification->model == 'trip_request')
                   href="{{route('admin.trip.show', ['id' => $notification->model_id])}}"
               @endif
               class="dropdown-item-text media gap-3 align-items-center seen-notification">
                <h1><i class="bi bi-info-circle-fill"></i></h1>
                <div class="media-body ">
                    <h6 class="card-title">{{ translate($notification->message)}}</h6>
                    <span
                        class="card-text fz-12 text-opacity-75">{{\Carbon\Carbon::parse($notification->created_at)->diffForHumans()}}</span>
                </div>
            </a>
        @empty
            <a class="dropdown-item-text media gap-3 align-items-center">
                <h1><i class="bi bi-info-circle-fill"></i></h1>
                <div class="media-body ">
                    <h6 class="card-title text-capitalize">{{ translate('no_new_notifications')}}</h6>
                </div>
            </a>
        @endforelse
    </ul>
</div>
