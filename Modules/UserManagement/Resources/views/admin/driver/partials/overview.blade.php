<div class="tab-pane fade active show" role="tabpanel">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="d-flex align-items-center gap-2 text-primary text-capitalize">
                        <i class="bi bi-person-fill-gear"></i>
                        {{translate('driver_details')}}
                    </h5>

                    <div class=" my-4">
                        <ul class="nav nav--tabs justify-content-around bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#trip-tab-pane"
                                        aria-selected="true"
                                        role="tab">{{translate('trip')}}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link text-capitalize" data-bs-toggle="tab"
                                        data-bs-target="#duty_review-tab-pane" aria-selected="false"
                                        role="tab" tabindex="-1">{{translate('duty_&_review')}}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#wallet-tab-pane"
                                        aria-selected="false" role="tab"
                                        tabindex="-1">{{translate('wallet')}}</button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="trip-tab-pane" role="tabpanel">
                            <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{translate('total_completed_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['completed_trips']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{translate('total_cancel_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['cancelled_trips']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{translate('lowest_price_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{getCurrencyFormat($otherData['driver_lowest_fare'])}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{translate('highest_price_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{getCurrencyFormat($otherData['driver_highest_fare'])}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="duty_review-tab-pane" role="tabpanel">
                            <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{ translate('Total Review Given') }}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['driver']->givenReviews()->count()}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{ translate('Total Active Hour') }}</div>
                                        <span class="badge badge-primary fs-14">
                                            {{ $otherData['total_active_hours'] }}h
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="wallet-tab-pane" role="tabpanel">
                            <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{ translate('Total Level Point') }} <span class="text-muted">( {{$commonData['driver']?->level?->name}} - {{$otherData['targeted_review_point'] + $otherData['targeted_cancel_point'] + $otherData['targeted_amount_point'] + $otherData['targeted_ride_point']}}/{{$otherData['driver_level_point_goal'] ?? 0}} )</span>
                                        </div>
                                        <span
                                            class="badge badge-primary fs-14">{{$otherData['targeted_review_point'] + $otherData['targeted_cancel_point'] + $otherData['targeted_amount_point'] + $otherData['targeted_ride_point']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">{{translate('Wallet Money')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{getSession('currency_symbol')}} {{$commonData['driver']->userAccount()->value('wallet_balance')}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="text-primary mb-3 d-flex align-items-center gap-2"><i class="bi bi-paperclip"></i>
                        Attached Documents
                    </h5>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @forelse($driver->other_documents ?? [] as $doc)
                            <a download="{{$doc}}" href="{{asset('storage/app/public/driver/document')}}/{{$doc}}"
                               class="border rounded p-3 d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-filetype-pdf fs-22"></i>
                                    <h6 class="fs-12">{{$doc}}</h6>
                                </div>
                                <i class="bi bi-arrow-down-circle-fill fs-16 text-primary"></i>
                            </a>
                        @empty
                            <p class="text-capitalize">{{translate('no_documents_found')}}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
