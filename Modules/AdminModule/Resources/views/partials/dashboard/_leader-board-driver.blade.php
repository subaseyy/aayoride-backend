<div class="tab-pane fade active show" id="today-tab-pane" role="tabpanel">
    @if(count($leadDriver)>0)
    <div class="d-flex flex-wrap flex-xl-nowrap gap-5">
        <div class="leader-board-wrap d-flex gap-4 align-items-end">
            @if ($leadDriver->has(1))
                <div class="leader-board d-flex flex-column align-items-center">
                    <h5 class="text-warning">{{ $leadDriver[1]->total_records }}</h5>
                    <h6 class="fs-12 fw-semibold my-1 text-warning">{{ translate('trips') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">

                        <img src="{{ onErrorImage(
                            $leadDriver[1]?->driver?->profile_image,
                            asset('storage/app/public/driver/profile') . '/' . $leadDriver[1]?->driver?->profile_image,
                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                            'driver/profile/',
                        ) }}"
                            alt="" class="fit-object rounded-circle dark-support">
                    </div>
                    <div class="leader-board-column second bg-warning mt-3">
                        <span class="leader-board-position text-warning mx-auto"> 2 </span>
                        <h6 class="fs-12 mt-10 text-center text-white">
                            {{ substr($leadDriver[1]?->driver?->first_name, 0, 16) }}</h6>
                    </div>
                </div>
            @else
                <div class="leader-board d-flex flex-column align-items-center">
                    <h5 class="text-warning">{{ 0 }}</h5>
                    <h6 class="fs-12 fw-semibold my-1 text-warning">{{ translate('trips') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">

                        <img src="{{ asset('public/assets/admin-module/img/driver.png') }}"
                             alt="" class="fit-object rounded-circle dark-support">
                    </div>
                    <div class="leader-board-column second bg-warning mt-3">
                        <span class="leader-board-position text-warning mx-auto"> 2 </span>
                        <h6 class="fs-12 mt-10 text-center text-white">
                            {{ translate('no_Driver') }}</h6>
                    </div>
                </div>
            @endif
            @if ($leadDriver->has(0))
                <div class="leader-board d-flex flex-column align-items-center">
                    <h5 class="text-primary">{{ $leadDriver[0]->total_records }}</h5>
                    <h6 class="fs-12 fw-semibold my-1 text-primary">{{ translate('trips') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                        <img src="{{ onErrorImage(
                            $leadDriver[0]?->driver?->profile_image,
                            asset('storage/app/public/driver/profile') . '/' . $leadDriver[0]?->driver?->profile_image,
                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                            'driver/profile/',
                        ) }}"
                            alt="" class="fit-object rounded-circle dark-support">
                        <img width="36" src="{{ asset('public/assets/admin-module/img/icons/badge.png') }}"
                            class="dark-support leader-board-badge" alt="">
                    </div>
                    <div class="leader-board-column first bg-primary mt-3">
                        <span class="leader-board-position text-primary mx-auto"> 1 </span>
                        <h6 class="fs-12 mt-10 text-center text-white">
                            {{ substr($leadDriver[0]?->driver?->first_name, 0, 16) }}</h6>
                    </div>
                </div>
                @else
                    <div class="leader-board d-flex flex-column align-items-center">
                        <h5 class="text-primary">{{ 0 }}</h5>
                        <h6 class="fs-12 fw-semibold my-1 text-primary">{{ translate('trips') }}</h6>
                        <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                            <img src="{{ asset('public/assets/admin-module/img/driver.png') }}"
                                 alt="" class="fit-object rounded-circle dark-support">
                            <img width="36" src="{{ asset('public/assets/admin-module/img/icons/badge.png') }}"
                                 class="dark-support leader-board-badge" alt="">
                        </div>
                        <div class="leader-board-column first bg-primary mt-3">
                            <span class="leader-board-position text-primary mx-auto"> 1 </span>
                            <h6 class="fs-12 mt-10 text-center text-white">
                                {{ translate('no_Driver') }}</h6>
                        </div>
                    </div>
            @endif
            @if ($leadDriver->has(2))
                <div class="leader-board d-flex flex-column align-items-center">
                    <h5>{{ $leadDriver[2]->total_records }}</h5>
                    <h6 class="fs-12 fw-semibold my-1">{{ translate('trips') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                        <img src="{{ onErrorImage(
                            $leadDriver[2]?->driver?->profile_image,
                            asset('storage/app/public/driver/profile') . '/' . $leadDriver[2]?->driver?->profile_image,
                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                            'driver/profile/',
                        ) }}"
                            alt="" class="fit-object rounded-circle dark-support">
                    </div>
                    <div class="leader-board-column mt-3">
                        <span class="leader-board-position text-warning mx-auto"> 3 </span>
                        <h6 class="fs-12 mt-10 text-center">{{ substr($leadDriver[2]->driver?->first_name, 0, 16) }}</h6>
                    </div>
                </div>
                @else
                    <div class="leader-board d-flex flex-column align-items-center">
                        <h5>{{ 0 }}</h5>
                        <h6 class="fs-12 fw-semibold my-1">{{ translate('trips') }}</h6>
                        <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                            <img src="{{ asset('public/assets/admin-module/img/driver.png') }}"
                                 alt="" class="fit-object rounded-circle dark-support">
                        </div>
                        <div class="leader-board-column mt-3">
                            <span class="leader-board-position text-warning mx-auto"> 3 </span>
                            <h6 class="fs-12 mt-10 text-center">{{ translate('no_Driver') }}</h6>
                        </div>
                    </div>
            @endif
        </div>
        <div class="flex-grow-1 ps-xxl-5">
            <ol class="leader-board-list max-h-345px mb-0 overflow-y-auto">
                @forelse($leadDriver as $key => $ld)
                    <li>
                        <div class="d-flex align-items-center gap-3">
                            <div>{{ ++$key }}.</div>
                            <div class="media align-items-center gap-3">
                                <div class="avatar avatar-sm rounded-circle">
                                    <img src="{{ onErrorImage(
                                        $ld?->driver?->profile_image,
                                        asset('storage/app/public/driver/profile') . '/' . $ld?->driver?->profile_image,
                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                        'driver/profile/',
                                    ) }}"
                                        alt="" class="dark-support rounded-circle custom-box-size"
                                        style="--size: 28px">
                                </div>
                                <div class="media-body">{{ $ld->driver?->first_name . ' ' . $ld->driver?->last_name }}
                                </div>
                            </div>
                        </div>
                        <h6 class="fs-12">{{ $ld->total_records }}</h6>
                    </li>
                @empty
                    <div class="py-3">
                        <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                            <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                            <p class="text-center">{{translate('no_data_available')}}</p>
                        </div>
                    </div>
                @endforelse
            </ol>
        </div>
    </div>
    @else
        <div class="py-3">
            <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                <img src="{{ asset('public/assets/admin-module/img/driver.png') }}" alt="" width="100">
                <p class="text-center">{{translate('no_Driver_Found')}}</p>
            </div>
        </div>
    @endif
</div>
