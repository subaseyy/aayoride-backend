<div class="col-12">
    <div class="">
        <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
            <li class="nav-item">
                <a href="{{route('admin.trip.index', [ALL])}}" class="nav-link text-capitalize {{Request::is('admin/trip/list/all*') ? 'active' : ''}}">{{translate('all_trips')}}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.trip.index', [PENDING])}}" class="nav-link text-capitalize {{Request::is('admin/trip/list/pending*') ? 'active' : ''}}">{{translate(PENDING)}}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.trip.index', [ACCEPTED])}}" class="nav-link text-capitalize {{Request::is('admin/trip/list/accepted*') ? 'active' : ''}}">{{translate('request_accepted')}}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.trip.index', [ONGOING])}}" class="nav-link text-capitalize {{Request::is('admin/trip/list/ongoing*') ? 'active' : ''}}">{{translate(ONGOING)}}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.trip.index', [COMPLETED])}}" class="nav-link text-capitalize {{Request::is('admin/trip/list/completed*') ? 'active' : ''}}">{{translate('completed')}}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.trip.index', [CANCELLED])}}" class="nav-link text-capitalize {{Request::is('admin/trip/list/cancelled*') ? 'active' : ''}}">{{translate('canceled')}}</a>
            </li>
        </ul>
    </div>
</div>
