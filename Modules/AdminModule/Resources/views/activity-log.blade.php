@extends('adminmodule::layouts.master')

@section('title', translate('activity_log'))

@section('content')

    <div class="main-content">
        <div class="container-fluid">
            <div class="modal-content">
                <div class="modal-header border-0 pb-4">
                    @php($name = explode('/', url()->current()))
                    <h3 class="text-capitalize modal-title fs-5" id="activityLogModalLabel">
                        {{ translate($name[count($name) - 2]) }} {{ translate('activity_log') }} <span
                            class="text-primary">{{ $logs->total() }}</span></h3>
                </div>
                <div class="modal-body">
                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="update-tab-pane" role="tabpanel">
                            <div class="card shadow-lg">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="javascript:;" method="GET"
                                            class="search-form search-form_style-two">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" name="search" id="search"
                                                    class="theme-input-style search-form__input"
                                                    placeholder="{{ translate('Search_Here_by_Edited_By') }}"
                                                    value="{{request()->get('search')}}">
                                            </div>
                                            <button type="submit" class="btn btn-primary search-submit"
                                                data-url="{{ url()->full() }}">{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-outline-primary"
                                                    data-bs-toggle="dropdown">
                                                    <i class="bi bi-download"></i>
                                                    {{ translate('download') }}
                                                    <i class="bi bi-caret-down-fill"></i>
                                                </button>
                                                @if (Request::has('id') || Request::has('page') || Request::has('search'))
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li><a class="dropdown-item"
                                                                href="{{ url()->full() }}&&file=excel">{{ translate('excel') }}</a>
                                                        </li>
                                                    </ul>
                                                @else
                                                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                        <li><a class="dropdown-item"
                                                                href="{{ url()->current() }}?file=excel">{{ translate('excel') }}</a>
                                                        </li>
                                                    </ul>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div id="log_data">
                                        <div class="table-responsive mt-3">

                                            <table class="table table-borderless align-middle">
                                                <thead class="table-light align-middle">
                                                    <tr>
                                                        <th class="text-capitalize">{{ translate('edited_date') }}
                                                        </th>
                                                        <th class="text-capitalize">{{ translate('edited_time') }}
                                                        </th>
                                                        <th class="text-capitalize">{{ translate('edited_by') }}</th>
                                                        <th class="text-capitalize">{{ translate('edited_object') }}
                                                        </th>
                                                        <th class="text-capitalize">
                                                            {{ translate('before_edit_status') }}</th>
                                                        <th class="text-capitalize">
                                                            {{ translate('after_edit_status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($logs as $log)
                                                        <tr>
                                                            <td>{{ date('Y-m-d', strtotime($log->created_at)) }}</td>
                                                            <td>{{ date('h:i A', strtotime($log->created_at)) }}</td>
                                                            <td>{{ $log->users?->email }}</td>
                                                            @php($objects = explode('\\', $log->logable_type))
                                                            <td>{{ end($objects) }}</td>
                                                            <td class="word-break">
                                                                @if ($log->before)
                                                                    @foreach ($log->before as $key => $before)
                                                                        <?php $before = gettype($before) == 'array' ? json_encode($before) : $before; ?>
                                                                        {{ $key . ' : ' . $before }} <br>
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                            <td class="word-break">
                                                                @if ($log->after)
                                                                    @foreach ($log->after as $key => $after)
                                                                        <?php $after = gettype($after) == 'array' ? json_encode($after) : $after; ?>
                                                                        {{ $key . ' : ' . $after }} <br>
                                                                    @endforeach
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <td colspan="6">
                                                            <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                                <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                                                                <p class="text-center">{{ translate('no_data_available')}}</p>
                                                            </div>
                                                        </td>
                                                    @endforelse

                                                </tbody>
                                            </table>

                                        </div>

                                        <div
                                            class="table-bottom d-flex flex-column flex-sm-row justify-content-sm-between align-items-center gap-2">
                                            <p class="mb-0"></p>

                                            <div
                                                class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-end gap-3 gap-sm-4">
                                                {!! $logs->links() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
