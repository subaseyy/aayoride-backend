<div class="tab-pane fade active show" id="today-tab-pane" role="tabpanel">
    @if(count($leadCustomer) >0)

    <div class="d-flex flex-wrap flex-xl-nowrap gap-5">
        <div class="leader-board-wrap d-flex gap-4 align-items-end">
            @if ($leadCustomer->has(1))
                <div class="leader-board d-flex flex-column align-items-center">
                    <div class="mb-1"><i class="bi bi-star-fill text-warning"></i></div>
                    @php($rating = \Modules\ReviewModule\Entities\Review::query()->where('received_by', $leadCustomer[1]?->customer?->id))
                    <h5 class="text-warning">{{ number_format($rating->avg('rating'), 1)  ?? 0 }}</h5>
                    <h6 class="fs-12 fw-semibold my-1 text-warning">{{ $rating->count('rating') }}
                        {{ translate('ratings') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                        <img src="{{ onErrorImage(
                            $leadCustomer[1]?->customer?->profile_image,
                            asset('storage/app/public/customer/profile') . '/' . $leadCustomer[1]?->customer?->profile_image,
                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                            'customer/profile/',
                        ) }}"
                             alt="" class="fit-object rounded-circle dark-support">
                    </div>
                    <div class="leader-board-column second bg-warning mt-3">
                        <span class="leader-board-position text-warning mx-auto"> 2 </span>
                        <h6 class="fs-12 mt-10 text-center text-white">
                            {{ substr($leadCustomer[1]->customer?->first_name, 0, 16) }}</h6>
                    </div>
                </div>
            @else
                <div class="leader-board d-flex flex-column align-items-center">
                    <div class="mb-1"><i class="bi bi-star-fill text-warning"></i></div>
                    <h5 class="text-warning">{{ number_format( 0) }}</h5>
                    <h6 class="fs-12 fw-semibold my-1 text-warning">{{ 0 }}
                        {{ translate('ratings') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                        <img src="{{ asset('public/assets/admin-module/img/user.png') }}"
                             alt="" class="fit-object rounded-circle dark-support">
                    </div>
                    <div class="leader-board-column second bg-warning mt-3">
                        <span class="leader-board-position text-warning mx-auto"> 2 </span>
                        <h6 class="fs-12 mt-10 text-center text-white">
                            {{ translate('no_Customer') }}</h6>
                    </div>
                </div>
            @endif
            @if ($leadCustomer->has(0))
                <div class="leader-board d-flex flex-column align-items-center">
                    <div class="mb-1"><i class="bi bi-star-fill text-primary"></i>
                    </div>
                    @php($rating = \Modules\ReviewModule\Entities\Review::query()->where('received_by', $leadCustomer[0]?->customer?->id))
                    <h5 class="text-primary">{{ number_format($rating->avg('rating'), 1) ?? 0 }}</h5>
                    <h6 class="fs-12 fw-semibold my-1 text-primary">{{ $rating->count('rating') }}
                        {{ translate('ratings') }}</h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">

                        <img src="{{ onErrorImage(
                            $leadCustomer[0]?->customer?->profile_image,
                            asset('storage/app/public/customer/profile') . '/' . $leadCustomer[0]?->customer?->profile_image,
                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                            'customer/profile/',
                        ) }}"
                             alt="" class="fit-object rounded-circle dark-support">
                        <img width="36" src="{{ asset('public/assets/admin-module/img/icons/badge.png') }}"
                             class="dark-support leader-board-badge" alt="">
                    </div>
                    <div class="leader-board-column first bg-primary mt-3">
                        <span class="leader-board-position text-primary mx-auto"> 1 </span>
                        <h6 class="fs-12 mt-10 text-center text-white">
                            {{ substr($leadCustomer[0]->customer?->first_name, 0, 16) }}</h6>
                    </div>
                </div>
                @else
                    <div class="leader-board d-flex flex-column align-items-center">
                        <div class="mb-1"><i class="bi bi-star-fill text-primary"></i>
                        </div>
                        <h5 class="text-primary">{{ 0 }}</h5>
                        <h6 class="fs-12 fw-semibold my-1 text-primary">{{ 0 }}
                            {{ translate('ratings') }}</h6>
                        <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">

                            <img src="{{ asset('public/assets/admin-module/img/user.png') }}"
                                 alt="" class="fit-object rounded-circle dark-support">
                            <img width="36" src="{{ asset('public/assets/admin-module/img/icons/badge.png') }}"
                                 class="dark-support leader-board-badge" alt="">
                        </div>
                        <div class="leader-board-column first bg-primary mt-3">
                            <span class="leader-board-position text-primary mx-auto"> 1 </span>
                            <h6 class="fs-12 mt-10 text-center text-white">
                                {{ translate('no_Customer') }}</h6>
                        </div>
                    </div>
                @endif
            @if ($leadCustomer->has(2))
                <div class="leader-board d-flex flex-column align-items-center">
                    <div class="mb-1"><i class="bi bi-star-fill"></i></div>
                    @php($rating = \Modules\ReviewModule\Entities\Review::query()->where('received_by', $leadCustomer[2]?->customer?->id))
                    <h5>{{ number_format($rating->avg('rating'), 1)  ?? 0 }}</h5>
                    <h6 class="fs-12 fw-semibold my-1">{{ $rating->count('rating') }} {{ translate('ratings') }}
                    </h6>
                    <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                        <img src="{{ onErrorImage(
                            $leadCustomer[2]?->customer?->profile_image,
                            asset('storage/app/public/customer/profile') . '/' . $leadCustomer[2]?->customer?->profile_image,
                            asset('public/assets/admin-module/img/avatar/avatar.png'),
                            'customer/profile/',
                        ) }}"
                             alt="" class="fit-object rounded-circle dark-support">
                    </div>
                    <div class="leader-board-column mt-3">
                        <span class="leader-board-position text-warning mx-auto"> 3 </span>
                        <h6 class="fs-12 mt-10 text-center">{{ substr($leadCustomer[2]->customer?->first_name, 0, 16) }}
                        </h6>
                    </div>
                </div>
            @else
                    <div class="leader-board d-flex flex-column align-items-center">
                        <div class="mb-1"><i class="bi bi-star-fill"></i></div>
                        <h5>{{ 0 }}</h5>
                        <h6 class="fs-12 fw-semibold my-1">{{ 0 }} {{ translate('ratings') }}
                        </h6>
                        <div class="custom-box-size rounded-circle position-relative" style="--size: 65px">
                            <img src="{{ asset('public/assets/admin-module/img/user.png') }}"
                                 alt="" class="fit-object rounded-circle dark-support">
                        </div>
                        <div class="leader-board-column mt-3">
                            <span class="leader-board-position text-warning mx-auto"> 3 </span>
                            <h6 class="fs-12 mt-10 text-center">{{ translate('no_Customer') }}
                            </h6>
                        </div>
                    </div>
            @endif
        </div>
        <div class="flex-grow-1 ps-xxl-5">
            <ol class="leader-board-list max-h-340px mb-0 overflow-y-auto">
                @forelse($leadCustomer as $key => $lc)
                    <li>
                        <div class="d-flex align-items-center gap-3">
                            <div>{{ ++$key }}.</div>
                            <div class="media align-items-center gap-3">
                                <div class="avatar avatar-sm rounded-circle">
                                    <img src="{{ onErrorImage(
                                        $lc?->customer?->profile_image,
                                        asset('storage/app/public/customer/profile') . '/' . $lc?->customer?->profile_image,
                                        asset('public/assets/admin-module/img/avatar/avatar.png'),
                                        'customer/profile/',
                                    ) }}"
                                         alt="" class="dark-support rounded-circle custom-box-size"
                                         style="--size: 28px">
                                </div>
                                <div class="media-body">
                                    {{ $lc->customer?->first_name . ' ' . $lc->customer?->last_name }}</div>
                            </div>
                        </div>
                        <h6 class="fs-12">{{ $lc->total_records }}</h6>
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
                <img src="{{ asset('public/assets/admin-module/img/user-c.png') }}" alt="" width="100">
                <p class="text-center">{{translate('no_Customer_Found')}}</p>
            </div>
        </div>
    @endif
</div>
