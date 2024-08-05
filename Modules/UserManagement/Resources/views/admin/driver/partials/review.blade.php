<div class="tab-pane fade active show" id="review-pane" role="tabpanel">
    <div class="card mb-30">
        <div class="card-body p-30">
            <h5 class="mb-2 text-primary">{{ translate('Reviews Overview') }}</h5>
            <div class="row">
                <div class="col-lg-5 mb-30 mb-lg-0 d-flex justify-content-center">

                    <div class="rating-review">
                        <h2 class="rating-review__title"><span class="rating-review__out-of">{{number_format($otherData['avg_rating'], 1) ?? 0}}</span>/5</h2>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="rating-review__info d-flex flex-wrap gap-3 mt-1">
                            <span>{{$otherData['total_rating']}} {{translate('Ratings')}}</span>
                            <span>{{$otherData['reviews_count']}} {{ translate('Reviews') }}</span>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <ul class="list-rating gap-10">
                        <li>
                            <span class="review-name">{{ translate('Excellent') }}</span>

                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{$otherData['total_review_count']==0?0:($otherData['five_star']/$otherData['total_review_count'])*100}}%;" aria-valuenow="{{$otherData['total_review_count']==0?0:($otherData['five_star']/$otherData['total_review_count'])*100}}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <span class="review-count">{{$otherData['five_star']}}</span>
                        </li>
                        <li>
                            <span class="review-name">{{ translate('Good') }}</span>

                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{$otherData['total_review_count']==0?0:($otherData['four_star']/$otherData['total_review_count'])*100}}%;" aria-valuenow="{{$otherData['total_review_count']==0?0:($otherData['four_star']/$otherData['total_review_count'])*100}}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <span class="review-count">{{$otherData['four_star']}}</span>
                        </li>
                        <li>
                            <span class="review-name">{{ translate('Average') }}</span>

                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{$otherData['total_review_count']==0?0:($otherData['three_star']/$otherData['total_review_count'])*100}}%;" aria-valuenow="{{$otherData['total_review_count']==0?0:($otherData['three_star']/$otherData['total_review_count'])*100}}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <span class="review-count">{{$otherData['three_star']}}</span>
                        </li>
                        <li>
                            <span class="review-name">{{ translate('Below Average') }}</span>

                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{$otherData['total_review_count']==0?0:($otherData['two_star']/$otherData['total_review_count'])*100}}%;" aria-valuenow="{{$otherData['total_review_count']==0?0:($otherData['two_star']/$otherData['total_review_count'])*100}}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <span class="review-count">{{$otherData['two_star']}}</span>
                        </li>
                        <li>
                            <span class="review-name">{{ translate('Poor') }}</span>

                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{$otherData['total_review_count']==0?0:($otherData['one_star']/$otherData['total_review_count'])*100}}%;" aria-valuenow="{{$otherData['total_review_count']==0?0:($otherData['one_star']/$otherData['total_review_count'])*100}}"
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            <span class="review-count">{{$otherData['one_star']}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mt-30 mb-3 gap-3">
        <h2 class="fs-22">{{translate('All Reviews')}}</h2>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted">{{translate('Total Review')}} : </span>
            <span class="text-primary fs-16 fw-bold">{{$otherData['total_review_count']}}</span>
        </div>
    </div>

    <div class="d-flex mb-3">
        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'review', 'reviewed_by' => 'customer'])}}" class="nav-link {{($commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'customer') || ($commonData['tab'] == 'review' && $otherData['reviewed_by'] != 'driver') ? 'active' : ''}}"
                   role="tab">{{translate('Review From Customer')}}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{route('admin.driver.show', ['id' => $commonData['driver']->id, 'tab' => 'review', 'reviewed_by' => 'driver'])}}" class="nav-link {{($commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'driver') ? 'active' : ''}}"
                   role="tab">{{translate('Review Given To Customer')}}</a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade {{($commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'customer') || ($commonData['tab'] == 'review' && $otherData['reviewed_by'] != 'driver') ? 'active show' : ''}}" id="review_from_customer" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-end">
                        <div class="d-flex flex-wrap gap-3">

                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    {{translate('download')}}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{route('admin.driver.review.export', ['id' => $commonData['driver']->id, 'reviewed' => $otherData['reviewed_by'],'file' => 'excel', request()->getQueryString()])}}">{{translate('excel')}}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-borderless align-middle table-hover">
                            <thead class="table-light align-middle text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('trip_ID')}}</th>
                                <th>{{translate('reviewer')}}</th>
                                <th>{{translate('rating')}}</th>
                                <th>{{translate('review')}}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @forelse ($otherData['customer_reviews'] as $key => $item)
                                <tr>
                                    <td>{{$key + 1 }}</td>
                                    <td>#{{ $item?->trip?->ref_id ?? '-' }}</td>
                                    <td>{{ ($item?->givenUser?->first_name . ' ' . $item?->givenUser?->last_name) ?? 'Not Found' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            {{$item->rating}}<i class="bi bi-star-fill text-warning"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="max-w300 text-wrap">
                                            {{$item?->feedback}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15">
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

                    <div class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                        {{$otherData['customer_reviews']->withQueryString()->links()}}
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade {{$commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'driver' ? 'active show' : ''}}" id="review_from_driver" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-end">

                        <div class="d-flex flex-wrap gap-3">

                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    {{translate('download')}}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{route('admin.driver.review.export', ['id' => $commonData['driver']->id, 'reviewed' => $otherData['reviewed_by'],'file' => 'excel', request()->getQueryString()])}}">{{translate('excel')}}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-borderless align-middle table-hover">
                            <thead class="table-light align-middle text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('Trip ID')}}</th>
                                <th>{{translate('Rating')}}</th>
                                <th>{{translate('Review')}}</th>

                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($otherData['driver_reviews'] as $key => $item)
                                <tr>
                                    <td>{{$key + 1 }}</td>
                                    <td>#{{ $item?->trip?->ref_id ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            {{$item->rating}}<i class="bi bi-star-fill text-warning"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="max-w300 text-wrap">
                                            {{$item?->feedback}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15">
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

                    <div class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                        {{$otherData['driver_reviews']->withQueryString()->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
