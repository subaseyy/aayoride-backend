@section('title', 'Zone Setup')

@extends('adminmodule::layouts.master')

@section('content')
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="col-12">
                    <h2 class="fs-22 text-capitalize mb-3">{{ translate('deleted_zone_list') }}</h2>
                    <div class="d-flex flex-wrap justify-content-end my-3 gap-3">
                        <div class="d-flex gap-2">
                            <span class="text-muted">{{translate('total_zone')}} : </span>
                            <span class="text-primary fs-16 fw-bold">{{$zones->total()}}</span>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">

                                    <div class="tmodel/inable-responsive mt-3">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>{{ translate('SL') }}</th>
                                                    <th class="text-capitalize name">{{ translate('zone_name') }}</th>
                                                    <th class="text-capitalize trip-request-volume">{{ translate('trip_request_volume') }}</th>
                                                    <th class="text-center action">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($zones as $zone)
                                                    <tr>
                                                        <td>{{ $loop->index + 1 }}</td>
                                                        <td class="name">{{ $zone->name }}</td>
                                                        @php($volume_percentage = ($zone->tripRequest_count > 0) ? ($tripsCount/$zone->tripRequest_count) * 100 : 0)
                                                        <td class="total-vehicle">{{$volume_percentage < 33.33 ? 'low' : ($volume_percentage == 66.66 ? 'medium' : 'high')}}</td>
                                                        <td class="action">
                                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                                <button
                                                                    data-bs-toggle="tooltip" data-bs-title="{{ translate('restore_zone') }}"
                                                                    data-route="{{ route('admin.zone.restore', ['id' => $zone->id]) }}"
                                                                    data-message="{{ translate('Want_to_recover_this_zone?_') . translate('if_yes,_this_zone_will_be_available_again') }}"
                                                                    class="btn btn-outline-primary btn-action restore-data">
                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                </button>
{{--                                                                <a href="{{route('admin.zone.restore', ['id' => $zone->id])}}" class="btn btn-outline-primary btn-action">--}}
{{--                                                                    <i class="bi bi-arrow-repeat"></i>--}}
{{--                                                                </a>--}}
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
                                      {{ $zones->links() }}
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

