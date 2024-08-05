<div class="mb-4">
    <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
        <li class="nav-item">
            <a href="{{route('admin.trip.show', ['id' => $trip->id, 'type' => Request::get('type'), 'page' => 'summary'])}}" class="text-capitalize nav-link {{!request()->has('page') || request()->get('page') == 'summary' ? 'active' : ''}}">{{translate('trip_summary')}}</a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.trip.show', ['id' => $trip->id, 'type' => Request::get('type'), 'page' => 'log'])}}" class="text-capitalize nav-link {{request()->get('page') == 'log' ? 'active' : ''}}">{{translate('activity_log')}}</a>
        </li>
    </ul>
</div>
