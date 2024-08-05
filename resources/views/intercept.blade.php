@section('title', translate('hit count'))

@extends('adminmodule::layouts.master')


@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">

                <div class="col-12">
                    <h2 class="fs-22 text-capitalize pb-2">{{ translate('function wise routes api hit count') }}</h2>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">

                                        <div class="d-flex flex-wrap gap-3">

                                            <div class="dropdown">
                                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{translate('SL')}}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" type="checkbox"
                                                                       checked="checked" name="sl_no">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{ translate('area_name') }}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" name="area_name" type="checkbox"
                                                                       checked="checked">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{ translate('trip_request_volume') }}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" name="trip_request_volume" type="checkbox"
                                                                       checked="checked">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{ translate('running_promotion') }}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" name="running_promotion" type="checkbox"
                                                                       checked="checked">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{ translate('total_customer') }}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" name="total_customer" type="checkbox"
                                                                       checked="checked">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{ translate('status') }}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" name="status" type="checkbox"
                                                                       checked="checked">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                    <li class="dropdown-item py-2">
                                                        <div
                                                            class="d-flex align-items-center gap-4 justify-content-between">
                                                            <span>{{ translate('action') }}</span>
                                                            <label class="switcher">
                                                                <input class="switcher_input table-column" name="action" type="checkbox"
                                                                       checked="checked">
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="table-responsive mt-3">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                            <tr>
                                                <th class="sl_no">{{ translate('SL') }}</th>
                                                <th class="sl_no">{{ translate('function_name') }}</th>
                                                <th class="text-capitalize area_name">{{ translate('count') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($count as $area)

                                                <tr>
                                                    <td class="sl_no">{{ $loop->index+1 }}</td>
                                                    <td class="area_name">{{ $area->function_name }}</td>
                                                    <td class="area_name">{{ $area->count }}</td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="14"><p class="text-center">{{translate('no_data_available')}}</p></td>
                                                </tr>
                                            @endforelse

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        {!! $count->links() !!}
                                    </div>
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
