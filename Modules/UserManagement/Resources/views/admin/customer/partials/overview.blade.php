<div class="tab-pane fade active show" id="overview-pane" role="tabpanel">
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="d-flex align-items-center gap-2 text-primary">
                        <i class="bi bi-person-fill-gear"></i>
                        {{translate('customer_details')}}
                    </h5>

                    <div class=" my-4">
                        <ul class="nav nav--tabs justify-content-around bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab"
                                        data-bs-target="#trip-tab-pane" aria-selected="true"
                                        role="tab">{{translate('trip')}}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#duty_review-tab-pane" aria-selected="false"
                                        role="tab"
                                        tabindex="-1">{{translate('duty_&_review')}}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"
                                        data-bs-target="#wallet-tab-pane" aria-selected="false"
                                        role="tab"
                                        tabindex="-1">{{translate('wallet')}}</button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="trip-tab-pane" role="tabpanel">
                            <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div
                                            class="text-capitalize">{{translate('total_completed_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['total_success_request']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div
                                            class="text-capitalize">{{translate('total_cancel_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['total_cancel_request']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div
                                            class="text-capitalize">{{translate('lowest_price_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{getSession('currency_symbol')}}{{$commonData['customer_lowest_fare']??0}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div
                                            class="text-capitalize">{{translate('highest_price_trip')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{getSession('currency_symbol')}}{{$commonData['customer_highest_fare'] ?? 0}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="duty_review-tab-pane" role="tabpanel">
                            <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div
                                            class="text-capitalize">{{translate('total_review_given')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['customer_total_review_count']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div
                                            class="text-capitalize">{{translate('total_review_received')}}</div>
                                        <span
                                            class="badge badge-primary fs-14">{{$commonData['customer_total_received_review_count']}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="wallet-tab-pane" role="tabpanel">
                            <ul class="list-unstyled d-flex flex-column gap-3 text-dark mb-0">
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">Total Level Point <span
                                                class="text-muted">( {{$commonData['customer']?->level?->name}} - {{$otherData['targeted_review_point'] + $otherData['targeted_cancel_point'] + $otherData['targeted_amount_point'] + $otherData['targeted_ride_point']}}/{{$otherData['customer_level_point_goal'] ?? 0}} )</span>
                                        </div>
                                        <span
                                            class="badge badge-primary fs-14">{{$otherData['targeted_review_point'] + $otherData['targeted_cancel_point'] + $otherData['targeted_amount_point'] + $otherData['targeted_ride_point']}}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex gap-3 justify-content-between">
                                        <div class="text-capitalize">Wallet Money</div>
                                        <span
                                            class="badge badge-primary fs-14">{{getSession('currency_symbol')}}{{$commonData['customer']->userAccount()->value('wallet_balance') ?? 0}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="text-capitalize text-primary mb-3 d-flex align-items-center gap-2"><i
                            class="bi bi-paperclip"></i> {{translate('attached_documents')}}
                    </h5>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @forelse($customer->other_documents ?? [] as $doc)
                            <a download="{{$doc}}"
                               href="{{asset('storage/app/public/customer/document')}}/{{$doc}}"
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
