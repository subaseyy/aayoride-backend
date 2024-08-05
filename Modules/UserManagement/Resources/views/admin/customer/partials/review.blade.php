<div class="tab-pane fade active show" id="review-pane" role="tabpanel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-30 mb-3 gap-3">
        <h2 class="fs-22"></h2>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted">{{translate('total_review')}} : </span>
            <span class="text-primary fs-16 fw-bold">
                {{$otherData['reviewed_by'] == 'customer' ?  $commonData['customer']->givenReviews()->count() :  $commonData['customer']->receivedReviews()->count()}}
            </span>
        </div>
    </div>

    <div class="d-flex mb-3">
        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="{{route('admin.customer.show', ['id' => $commonData['customer']->id, 'tab' => 'review', 'reviewed_by' => 'customer'])}}"
                   class="nav-link text-capitalize {{($commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'customer') || ($commonData['tab'] == 'review' && $otherData['reviewed_by'] != 'driver') ? 'active' : ''}}"
                   role="tab">{{translate('review_given_to_driver')}}</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{route('admin.customer.show', ['id' => $commonData['customer']->id, 'tab' => 'review', 'reviewed_by' => 'driver'])}}"
                   class="nav-link text-capitalize {{($commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'driver') ? 'active' : ''}}"
                   role="tab">{{translate('review_from_driver')}}</a>
            </li>
        </ul>
    </div>

    <div class="tab-content">
        <div
            class="tab-pane fade {{($commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'customer') || ($commonData['tab'] == 'review' && $otherData['reviewed_by'] != 'driver') ? 'active show' : ''}}"
            id="review_from_customer" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-end">

                        <div class="d-flex flex-wrap gap-3">
                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    {{translate('download')}}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><a class="dropdown-item"
                                           href="{{route('admin.customer.review.export', ['id' => $commonData['customer']->id, 'reviewed' => $otherData['reviewed_by'],'file' => 'excel', request()->getQueryString()])}}">{{translate('excel')}}</a>
                                    </li>
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
                                <th>{{translate('rating')}}</th>
                                <th>{{translate('review')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($otherData['customer_given_reviews'] as $key => $item)
                                <tr>
                                    <td>{{ $otherData['customer_given_reviews']->firstItem() + $key }}</td>
                                    <td>#{{ $item?->trip?->ref_id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            {{$item->rating}}<i
                                                class="bi bi-star-fill text-warning"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="max-w300 text-wrap">
                                            {{$item?->feedback}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
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

                    <div
                        class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                        {{$otherData['customer_given_reviews']->withQueryString()->links()}}
                    </div>
                </div>
            </div>
        </div>
        <div
            class="tab-pane fade {{$commonData['tab'] == 'review' && $otherData['reviewed_by'] == 'driver' ? 'active show' : ''}}"
            id="review_from_driver" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="table-top d-flex flex-wrap gap-10 justify-content-end">

                        <div class="d-flex flex-wrap gap-3">

                            <div class="dropdown">
                                <button type="button" class="btn btn-outline-primary"
                                        data-bs-toggle="dropdown">
                                    <i class="bi bi-download"></i>
                                    {{translate('download')}}
                                    <i class="bi bi-caret-down-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                    <li><a class="dropdown-item"
                                           href="{{route('admin.customer.review.export', ['id' => $commonData['customer']->id, 'reviewed' => $otherData['reviewed_by'],'file' => 'excel', request()->getQueryString()])}}">{{translate('excel')}}</a>
                                    </li>
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
                            @forelse ($otherData['driver_reviews'] as $key => $item)
                                <tr>
                                    <td>{{$key + 1 }}</td>
                                    <td>#{{ $item?->trip?->ref_id }}</td>
                                    <td>{{$item?->givenUser?->first_name . ' ' . $item?->givenUser?->last_name}}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            {{$item->rating}}<i
                                                class="bi bi-star-fill text-warning"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="max-w300 text-wrap">
                                            {{$item?->feedback}}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="text-center">
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

                    <div
                        class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                        {{$otherData['driver_reviews']->withQueryString()->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
