@push('css_or_js')
    <style>
        #map-layer {
            max-width: 706px;
            min-height: 430px;
        }
    </style>

@endpush
<div class="col-lg-4">
    <div class="card">
        <div class="card-body">
            <h5 class="text-center mb-3 text-capitalize">{{translate('trip_status')}}</h5>

            <div class="mb-3">
                <label for="trip_status" class="mb-2">{{translate('trip_status')}}</label>
                <select name="trip_status" id="trip_status" class="js-select" disabled>
                    <option selected>{{translate($trip->current_status)}}</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="payment_status" class="mb-2">{{translate('payment_status')}}</label>
                <select name="payment_status" id="payment_status" class="js-select" disabled>
                    <option selected>{{translate($trip->payment_status)}}</option>
                </select>
            </div>
            <div class="mb-4">
                <div id="map-layer"></div>
            </div>

            <div>
                <ul class="list-icon">
                    <li>
                        <div class="media gap-2">
                            <img width="18" src="{{asset('public/assets/admin-module/img/svg/gps.svg')}}" class="svg" alt="">
                            <div class="media-body">{{$trip->coordinate->pickup_address}}</div>
                        </div>
                    </li>
                    <li>
                        <div class="media gap-2">
                            <img width="18" src="{{asset('public/assets/admin-module/img/svg/map-nav.svg')}}" class="svg" alt="">
                            <div class="media-body">
                                <div>{{$trip->coordinate->destination_address}}</div>
                                @if($trip->entrance)
                                    <a href="#" class="text-primary d-flex">{{$trip->entrance}}</a>

                                @endif
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="media gap-2">
                            <img width="18" src="{{asset('public/assets/admin-module/img/svg/distance.svg')}}" class="svg" alt="">
                            @if($trip->current_status == 'completed')
                                <div class="media-body text-capitalize">{{translate('total_distance')}} - {{$trip->actual_distance}} {{translate('km')}}</div>
                            @else
                                <div class="media-body text-capitalize">{{translate('total_distance')}} - {{$trip->estimated_distance}} {{translate('km')}}</div>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        let map;
        let waypoints;

        function initMap() {
            const mapLayer = document.getElementById("map-layer");
            const defaultOptions = { zoom: 9};
            map = new google.maps.Map(mapLayer, defaultOptions);

            const directionsService = new google.maps.DirectionsService;
            const directionsDisplay = new google.maps.DirectionsRenderer;
            directionsDisplay.setMap(map);

            const start = ({lat: {{$trip->coordinate->pickup_coordinates->latitude}}, lng: {{$trip->coordinate->pickup_coordinates->longitude}}});
            const end = ({lat: {{$trip->coordinate->destination_coordinates->latitude}}, lng: {{$trip->coordinate->destination_coordinates->longitude}}});
            drawPath(directionsService, directionsDisplay,start,end);
        }
        function drawPath(directionsService, directionsDisplay,start,end) {

            directionsService.route({
                origin: start,
                destination: end,
                travelMode: "DRIVING"
            },
            function(response, status) {
                if (status === 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    toastr.error('{{translate('problem_in_showing_direction._status:_')}}' + status);
                }
            });
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{businessConfig(GOOGLE_MAP_API)?->value['map_api_key'] ?? null}}&callback=initMap">
    </script>
@endpush
