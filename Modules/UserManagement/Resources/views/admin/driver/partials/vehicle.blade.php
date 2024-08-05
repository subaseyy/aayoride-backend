<div class="tab-pane fade active show" role="tabpanel">
    <h2 class="fs-22 mb-3">{{ translate('vehicle') }}</h2>

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-2 mb-4">
                <h5 class="d-flex align-items-center gap-2 text-primary text-capitalize">
                    <i class="bi bi-person-fill-gear"></i>
                    {{ translate('vehicle_info') }}
                </h5>
            </div>
            <div class="row gy-5">
                <div class="col-lg-4">
                    <div class="media flex-wrap gap-3 gap-lg-4">
                        <div class="avatar avatar-135 rounded">
                            <img src="{{ onErrorImage(
                                $commonData['driver']?->vehicle?->model?->image,
                                asset('storage/app/public/vehicle/model') . '/' . $commonData['driver']?->vehicle?->model?->image,
                                asset('public/assets/admin-module/img/media/upload-file.png'),
                                'vehicle/model/',
                            ) }}"
                                class="rounded dark-support fit-object-contain" alt="">
                        </div>
                        <div class="media-body">
                            <div class="d-flex flex-column align-items-start gap-1">
                                <h6 class="mb-10">{{ $commonData['driver']?->vehicle?->brand?->name }}
                                    - {{ $commonData['driver']?->vehicle?->model?->name ?? 'Not found' }}</h6>
                                <ul class="nav text-dark d-flex flex-column gap-2 mb-0">
                                    <li>
                                        <span class="text-muted">{{ translate('owner') }}</span>
                                        <span class="">{{ $commonData['driver']?->vehicle?->ownership }}</span>
                                    </li>
                                    <li>
                                        <span class="text-muted">{{ translate('category') }}</span>
                                        <span class="">
                                            {{ $commonData['driver']?->vehicle?->category->name }}</span>
                                    </li>
                                    <li>
                                        <span class="text-muted">{{ translate('brand') }}</span>
                                        <span
                                            class="">{{ $commonData['driver']?->vehicle?->brand?->name }}</span>
                                    </li>
                                    <li>
                                        <span class="text-muted">{{ translate('model') }}</span>
                                        <span
                                            class="">{{ $commonData['driver']?->vehicle?->model?->name }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex justify-content-around flex-wrap gap-3">
                        <div class="d-flex align-items-center flex-column gap-3">
                            <div class="circle-progress" data-parsent="{{ $otherData['vehicle_rate'] ?? 0 }}"
                                data-color="#28A745">
                                <div class="content">
                                    <h6 class="persent">{{ $otherData['vehicle_rate'] ?? 0 }}%</h6>
                                </div>
                            </div>
                            <h6 class="fw-semibold fs-12 text-capitalize">
                                {{ translate('trip_rate_for_this_vehicle') }}</h6>
                        </div>

                        <div class="d-flex align-items-center flex-column gap-3">
                            <div class="circle-progress" data-parsent="{{ $otherData['parcel_rate'] ?? 0 }}"
                                data-color="#0073B4">
                                <div class="content">
                                    <h6 class="persent">{{ $otherData['parcel_rate'] ?? 0 }}%</h6>
                                </div>
                            </div>
                            <h6 class="fw-semibold fs-12 text-capitalize">
                                {{ translate('parcel_delivery_rate_for_this_vehicle') }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="text-primary mb-3 d-flex gap-2 align-items-center"><i class="bi bi-truck-front-fill"></i> Vehicle
                Specification</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-borderX-0 p-lg">
                    <tbody>
                        <tr>
                            <td>{{ translate('viin') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->vin_number }}</td>
                            <td>{{ translate('fuel_type') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->fuel_type }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('licence_plate_number') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->licence_plate_number }}</td>
                            <td>{{ translate('engine') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->model?->engine }} {{ translate('cc') }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('licence_expire_date') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->licence_expire_date }}</td>
                            <td>{{ translate('seat_capacity') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->model?->seat_capacity }}</td>
                        </tr>
                        <tr>
                            <td>{{ translate('transmission') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->transmission }}</td>
                            <td>{{ translate('hatch_bag_capacity') }}</td>
                            <td>{{ $commonData['driver']?->vehicle?->model?->hatch_bag_capacity }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="text-primary mb-3 d-flex align-items-center gap-2"><i
                    class="bi bi-paperclip"></i>{{ translate('attached_documents') }}</h5>
            @foreach ($commonData['driver']->vehicle->documents ?? [] as $document)
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a download="{{ $document }}"
                        href="{{ asset('storage/app/public/vehicle/document') }}/{{ $document }}"
                        class="border rounded p-3 d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-paperclip fs-22"></i>
                            <h6 class="fs-12">{{ $document }}</h6>
                        </div>
                        <i class="bi bi-arrow-down-circle-fill fs-16 text-primary"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
