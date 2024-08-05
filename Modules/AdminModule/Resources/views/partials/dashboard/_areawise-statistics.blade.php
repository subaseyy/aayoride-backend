@php($colors = [])
<!-- Area wise Trip Statistics -->
<div class="top-providers">
    @if($trips->count()>0)

        <div class="custom-mh overflow-y-auto position-relative">
            <ul class="list-unstyled gap-4 d-flex flex-column">

                @foreach($trips as $key => $trip)
                    <li>
                        <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-2 fs-13">
                            <div>{{$trip['zone_name']}}</div>
                            <div
                                dir="ltr">{{$volume = number_format(($trip['total_trips']/($totalCount>0?$totalCount:1))*100),1}}
                                % {{ translate('trip_volume')}}</div>
                        </div>
                        @if($volume > -1 && $volume < 33)
                            @php($color = '--bs-yellow')
                        @elseif($volume >= 33 && $volume < 66)
                            @php($color = '--bs-warning')
                        @else
                            @php($color = '--bs-danger')
                        @endif
                        <div class="progress" data-bs-toggle="tooltip" data-bs-html="true"
                             data-bs-custom-class="custom-tooltip"
                             data-bs-title="<div class='progress-tooltip'><span class='dot ongoing'></span>{{ translate("Ongoing Trips") }} - {{$trip['ongoing_trips']}}</div>
                         <div class='progress-tooltip'><span class='dot completed'></span>{{ translate("Trips Completed") }} - {{$trip['completed_trips']}}</div>
                         <div class='progress-tooltip'><span class='dot canceled'></span>{{ translate("Trips Cancelled") }} - {{$trip['cancelled_trips']}}</div>">
                            <div class="progress-bar" role="progressbar"
                                 style="width: {{$volume}}%; background-color: var({{$color}})" aria-valuenow="12"
                                 aria-valuemin="0"
                                 aria-valuemax="100"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="d-flex flex-column gap-2 justify-content-center align-items-center py-5 text-center">
            <div><img src="{{ asset("public/assets/admin-module/img/map-marker-question.png") }}" alt=""></div>
            <div class="fs-16">{{ translate("No Zone Found") }}</div>
            <div>{{ translate("Currently there are no zone is active in your system.") }}</div>
            <div><a class="text-primary text-decoration-underline" href="{{route("admin.zone.index")}}">{{ translate("Go to Zone setup") }}</a></div>
        </div>
    @endif
</div>
<!-- End Area wise Trip Statistics -->
<script>
    if (typeof tooltipTriggerList === 'undefined') {
        let tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        let tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    }

</script>
