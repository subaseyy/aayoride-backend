<div class="tab-pane fade active show" id="trips-pane" role="tabpanel">
    <div class="d-flex flex-wrap justify-content-between align-items-center mt-30 mb-3 gap-3">
        <h2 class="fs-22 text-capitalize">{{translate('all_trips')}}</h2>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted text-capitalize">{{translate('total_trips')}} : </span>
            <span class="text-primary fs-16 fw-bold">{{$otherData['trips']->total()}}</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                <form action="javascript:;" method="GET"
                      class="search-form search-form_style-two">
                    <div class="input-group search-form__input_group">
                        <span class="search-form__icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search" id="search" name="search"
                               class="theme-input-style search-form__input"
                               placeholder="{{translate('Search Here')}}">
                    </div>
                    <button type="submit"
                            class="btn btn-primary search-submit" data-url="{{ url()->full() }}">{{translate('search')}}</button>
                </form>

                <div class="d-flex flex-wrap gap-3">

                    {{-- <a href="{{route('admin.trip.log')}}" class="btn btn-outline-primary">
                        <i class="bi bi-clock-fill"></i>
                    </a> --}}
                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-primary h-100"
                                data-bs-toggle="dropdown">
                            <i class="bi bi-border-style fs-16"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right text-capitalize">
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                    <span>{{translate('SL')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="sl">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div class="d-flex align-items-center gap-4 justify-content-between">
                                    <span class="text-capitalize">{{translate('trip_id')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="trip-id">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                    <span class="text-capitalize">{{translate('date')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="date">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('trip_type')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="trip_type">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('driver')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="driver">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('trip_cost')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="trip-cost">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('trip_discount')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="trip-discount">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('coupon_discount')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="coupon-discount">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('additional_fee')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="additional-fee">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('total_trip_cost')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="total-trip-cost">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('admin_commission')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="admin-commission">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('trip_status')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="trip-status">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                            <li class="dropdown-item py-2">
                                <div
                                    class="d-flex align-items-center gap-4 justify-content-between">
                                                        <span
                                                            class="text-capitalize">{{translate('action')}}</span>
                                    <label class="switcher">
                                        <input class="switcher_input table-column" type="checkbox"
                                               checked="checked" name="action">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button type="button" class="btn btn-outline-primary"
                                data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i>
                            {{translate('download')}}
                            <i class="bi bi-caret-down-fill"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('admin.trip.export', ['id' => $commonData['customer']->id, 'user_type'=>'customer', 'file' => 'excel','search' => $otherData['search']]) }}">
                                    {{ translate('Excel') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('usermanagement::admin.customer.partials.trip-list')
        </div>
    </div>
</div>
