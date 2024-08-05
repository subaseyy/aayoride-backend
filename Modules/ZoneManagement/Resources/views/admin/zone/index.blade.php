@extends('adminmodule::layouts.master')

@section('title', translate('Zone_Setup'))

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            @can('zone_add')
                <div class="d-flex align-items-center gap-3 justify-content-between mb-4">
                    <h2 class="fs-22 text-capitalize">{{ translate('zone_setup') }}</h2>
                </div>
                <form id="zone_form" action="{{ route('admin.zone.store') }}" enctype="multipart/form-data"
                      method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row justify-content-between">
                                        <div class="col-lg-5 col-xl-4 mb-5 mb-lg-0">
                                            <h5 class="text-primary mb-4">{{ translate('instructions') }}</h5>
                                            <div class="d-flex flex-column">
                                                <p>{{ translate('create_zone_by_click_on_map_and_connect_the_dots_together') }}</p>

                                                <div class="media mb-2 gap-3 align-items-center">
                                                    <img
                                                        src="{{asset('public/assets/admin-module/img/svg/map-drag.svg') }}"
                                                        class="svg"
                                                        alt="">
                                                    <div class="media-body ">
                                                        <p>{{ translate('use_this_to_drag_map_to_find_proper_area') }}</p>
                                                    </div>
                                                </div>

                                                <div class="media gap-3 align-items-center">
                                                    <img
                                                        src="{{asset('public/assets/admin-module/img/svg/map-draw.svg') }}"
                                                        class="svg"
                                                        alt="">
                                                    <div class="media-body ">
                                                        <p>{{ translate('click_this_icon_to_start_pin_points_in_the_map_and_connect_them_
                                                            to_draw_a_zone_._Minimum_3_points_required') }}</p>
                                                    </div>
                                                </div>
                                                <div class="map-img mt-4">
                                                    <img
                                                        src="{{ asset('public/assets/admin-module/img/instructions.gif') }}"
                                                        alt="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-7">
                                            <div class="mb-4">
                                                <label for="zone_name"
                                                       class="form-label text-capitalize">{{ translate('zone_name') }}</label>
                                                <input required type="text" class="form-control"
                                                       value="{{old('zone_name') }}" name="name" id="zone_name"
                                                       placeholder="{{ translate('ex') }}: {{ translate('Dhanmondi') }}">
                                            </div>

                                            <div class="form-group mb-3 d-none">
                                                <label class="input-label"
                                                       for="coordinates">{{ translate('coordinates') }}
                                                    <span
                                                        class="input-label-secondary">{{ translate('draw_your_zone_on_the_map') }}</span>
                                                </label>
                                                <textarea required type="text" rows="8" name="coordinates"
                                                          id="coordinates" class="form-control" readonly></textarea>
                                            </div>

                                            <!-- Start Map -->
                                            <div class="map-warper overflow-hidden rounded">
                                                <input id="pac-input" class="controls rounded map-search-box"
                                                       title="{{ translate('search_your_location_here') }}"
                                                       type="text"
                                                       placeholder="{{ translate('search_here') }}"/>
                                                <div id="map-canvas" class="map-height"></div>
                                            </div>
                                            <!-- End Map -->
                                        </div>

                                        <div class="d-flex justify-content-end gap-3 mt-3">
                                            <button class="btn btn-primary"
                                                    type="submit">{{ translate('submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            @endcan
            <div class="col-12">
                <h2 class="fs-22 text-capitalize">{{ translate('zone_list') }}</h2>

                <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                    <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{!request()->has('status') || request()->get('status')==='all'?'active':''}}"
                               href="{{url()->current()}}?status=all">
                                {{ translate('all') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{request()->get('status')==='active'?'active':''}}"
                               href="{{url()->current()}}?status=active">
                                {{ translate('active') }}
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{request()->get('status')==='inactive'?'active':''}}"
                               href="{{url()->current()}}?status=inactive">
                                {{ translate('inactive') }}
                            </a>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted text-capitalize">{{ translate('total_zones') }}:</span>
                        <span class="text-primary fs-16 fw-bold" id="total_record_count">{{ $zones->total() }}</span>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                    <form action="javascript:;"
                                          class="search-form search-form_style-two" method="GET">
                                        <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                            <input type="search" class="theme-input-style search-form__input"
                                                   value="{{ request()->get('search') }}" name="search" id="search"
                                                   placeholder="{{ translate('search_here_by_zone_name') }}">
                                        </div>
                                        <button type="submit"
                                                class="btn btn-primary search-submit" data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                    </form>

                                    <div class="d-flex flex-wrap gap-3">
                                        @can('super-admin')
                                            <a href="{{ route('admin.zone.index') }}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>

                                            <a href="{{ route('admin.zone.trashed') }}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                                <i class="bi bi-recycle"></i>
                                            </a>
                                        @endcan
                                        @can('zone_log')
                                            <a href="{{ route('admin.zone.log') }}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                                <i class="bi bi-clock-fill"></i>
                                            </a>
                                        @endcan

                                        @can('zone_export')
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-outline-primary"
                                                        data-bs-toggle="dropdown">
                                                    <i class="bi bi-download"></i>
                                                    {{ translate('download') }}
                                                    <i class="bi bi-caret-down-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                    <li><a class="dropdown-item"
                                                           href="{{route('admin.zone.export') }}?status={{request()->get('status') ?? "all"}}&&file=excel">{{ translate('excel') }}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endcan

                                    </div>
                                </div>

                                <div class="table-responsive mt-3">
                                    <table class="table table-borderless align-middle">
                                        <thead class="table-light align-middle">
                                        <tr>
                                            <th>{{ translate('SL') }}</th>
                                            <th class="text-capitalize name">{{ translate('zone_name') }}</th>
                                            <th class="text-center text-capitalize trip-request-volume">{{ translate('trip_request_volume') }}</th>
                                            @can('zone_edit')
                                                <th class="status">{{ translate('status') }}</th>
                                            @endcan
                                            <th class="text-center action">{{ translate('action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($volumePercentage = 0)
                                        @forelse ($zones as $key => $zone)
                                            <tr id="hide-row-{{$zone->id}}" class="record-row">
                                                <td>{{ $zones->firstItem() + $key }}</td>
                                                <td class="name">{{ $zone->name }}</td>
                                                @php($volumePercentage = ($zone->tripRequest_count > 0) ? ($tripsCount/$zone->tripRequest_count) * 100 : 0)
                                                <td class="text-center total-vehicle">{{$volumePercentage < 33.33 ? translate('low') : ($volumePercentage == 66.66 ? translate('medium') : translate('high'))}}</td>
                                                @can('zone_edit')
                                                    <td class="status">
                                                        <label class="switcher">
                                                            <input class="switcher_input status-change"
                                                                   data-url={{ route('admin.zone.status') }} id="{{ $zone->id }}"
                                                                   type="checkbox" {{$zone->is_active?'checked':''}}>
                                                            <span class="switcher_control"></span>
                                                        </label>
                                                    </td>
                                                @endcan
                                                <td class="action">
                                                    <div class="d-flex justify-content-center gap-2 align-items-center">
                                                        @can('zone_log')
                                                            <a href="{{route('admin.zone.log') }}?id={{$zone->id}}"
                                                               class="btn btn-outline-primary btn-action">
                                                                <i class="bi bi-clock-fill"></i>
                                                            </a>
                                                        @endcan
                                                        @can('zone_edit')
                                                            <a href="{{ route('admin.zone.edit', ['id'=>$zone->id]) }}"
                                                               class="btn btn-outline-info btn-action">
                                                                <i class="bi bi-pencil-fill"></i>
                                                            </a>
                                                        @endcan
                                                        @can('zone_delete')
                                                            <button
                                                                data-id="delete-{{ $zone->id }}" data-message="{{ translate('want_to_delete_this_zone?') }}"
                                                                type="button" class="btn btn-outline-danger btn-action form-alert">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>

                                                            <form
                                                                action="{{ route('admin.zone.delete', ['id'=>$zone->id]) }}"
                                                                id="delete-{{ $zone->id }}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7">
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
                                <div class="d-flex justify-content-end">
                                    {{$zones->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')
    @php($map_key = businessConfig(GOOGLE_MAP_API)?->value['map_api_key'] ?? null)
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ $map_key }}&libraries=drawing,places&v=3.50"></script>
    <script src="{{asset('public/assets/admin-module/js/zone-management/zone/index.js') }}"></script>
    <script>
        "use strict";
        //zone form submit
        $('#zone_form').on('submit', function (e) {
            if ($('#coordinates').val() === '') {
                toastr.error('{{ translate('please_define_zone') }}')
                e.preventDefault();
            }
        })


        let map; // Global declaration of the map
        let drawingManager;
        let lastPolygon = null;
        let polygons = [];

        function resetMap(controlDiv) {
            // Set CSS for the control border.
            const controlUI = document.createElement("div");
            controlUI.style.backgroundColor = "#fff";
            controlUI.style.border = "2px solid #fff";
            controlUI.style.borderRadius = "3px";
            controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
            controlUI.style.cursor = "pointer";
            controlUI.style.marginTop = "8px";
            controlUI.style.marginBottom = "22px";
            controlUI.style.textAlign = "center";
            controlUI.title = "Reset map";
            controlDiv.appendChild(controlUI);
            // Set CSS for the control interior.
            const controlText = document.createElement("div");
            controlText.style.color = "rgb(25,25,25)";
            controlText.style.fontFamily = "Roboto,Arial,sans-serif";
            controlText.style.fontSize = "10px";
            controlText.style.lineHeight = "16px";
            controlText.style.paddingLeft = "2px";
            controlText.style.paddingRight = "2px";
            controlText.innerHTML = "X";
            controlUI.appendChild(controlText);
            // Setup the click event listeners: simply set the map to Chicago.
            controlUI.addEventListener("click", () => {
                lastPolygon.setMap(null);
                $('#coordinates').val('');
            });
        }

        function initialize() {
            let myLatLng = {
                lat: 23.757989,
                lng: 90.360587
            };

            let myOptions = {
                zoom: 10,
                center: myLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            }
            map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    editable: true
                }
            });
            drawingManager.setMap(map);
            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        map.setCenter(pos);
                    });
            }

            google.maps.event.addListener(drawingManager, "overlaycomplete", function (event) {

                if (lastPolygon) {
                    lastPolygon.setMap(null);
                }
                $('#coordinates').val(event.overlay.getPath().getArray());
                lastPolygon = event.overlay;
                auto_grow();
            });

            const resetDiv = document.createElement("div");
            resetMap(resetDiv, lastPolygon);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(resetDiv);

            // Create the search box and link it to the UI element.
            const input = document.getElementById("pac-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            // Bias the SearchBox results towards current map's viewport.
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            let markers = [];

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length === 0) {
                    return;
                }
                // Clear out the old markers.
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                // For each place, get the icon, name and location.
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        return;
                    }
                    const icon = {
                        url: place.icon,
                        size: new google.maps.Size(71, 71),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25),
                    };
                    // Create a marker for each place.
                    markers.push(
                        new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                        })
                    );

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }

        window.addEventListener('load', initialize);

        function set_all_zones() {
            $.get({
                url: '{{route('admin.zone.get-zones',['status'=> request()->get('status')=='active'?'active':(request()->get('status')=='inactive'?'inactive':'all')])}}',
                dataType: 'json',
                success: function (data) {
                    for (let i = 0; i < data.length; i++) {
                        polygons.push(new google.maps.Polygon({
                            paths: data[i],
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "#FF0000",
                            fillOpacity: 0.1,
                        }));
                        polygons[i].setMap(map);
                    }
                },
            });
        }

        set_all_zones();

    </script>
@endpush
