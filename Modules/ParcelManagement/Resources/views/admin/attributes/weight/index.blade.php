@section('title', translate('parcel_Weights'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                @can('parcel_add')
                <div class="col-12">
                    <form action="{{ route('admin.parcel.attribute.weight.store') }}" enctype="multipart/form-data" method="POST">
                        @csrf

                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('add_weight_range') }}</h5>

                                <div class="row">
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="weight_unit" class="mb-2">{{ translate('weight_unit') }}</label>
                                            <select class="js-select" id="weight_unit" name="weight_unit" disabled required>
                                                <option selected disabled>{{translate('select_weight_unit') }}</option>
                                                <option value="{{$weightUnit?->value ?? 'kg'}}" selected='selected'>{{'Kilogram'}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="min_weight" class="mb-2">{{ translate('minimum_weight') }}</label>
                                            <input required type="number" value="{{old('min_weight')}}" step=".01" id="min_weight" name="min_weight" class="form-control" placeholder="Ex: Minimum Weight">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="max_weight" class="mb-2">{{ translate('maximum_weight') }}</label>
                                            <input required type="number" value="{{old('max_weight')}}" step=".01" id="max_weight" name="max_weight" class="form-control" placeholder="Ex: Maximum Weight">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @endcan

                <div class="col-12">
                    <h2 class="fs-22 text-capitalize">{{ translate('weight_range_list') }}</h2>

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <ul class="nav nav--tabs p-1 rounded bg-white" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{!request()->has('status') || request()->get('status')==='all'?'active':''}}"
                                   href="{{url()->current()}}?status=all">
                                    {{ translate('all') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{request()->get('status')=='active'?'active':''}}"
                                   href="{{url()->current()}}?status=active">
                                    {{ translate('active') }}
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{request()->get('status')=='inactive'?'active':''}}"
                                   href="{{url()->current()}}?status=inactive">
                                    {{ translate('inactive') }}
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total_parcel_weight_ranges') }} : </span>
                            <span class="text-primary fs-16 fw-bold" id="total_record_count">{{ $weights->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="javascript:;" class="search-form search-form_style-two"  method="GET">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" class="theme-input-style search-form__input" value="{{request()->get('search')}}" name="search" id="search"
                                                    placeholder="{{translate('search_here_by_Parcel_Weight_Name')}}">
                                            </div>
                                            <button type="button" class="btn btn-primary search-submit" data-url="{{url()->full()}}" >{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            @can('super-admin')
                                            <a href="{{ route('admin.parcel.attribute.weight.index', ['status' => request('status')]) }}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('refresh') }}">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>

                                            <a href="{{ route('admin.parcel.attribute.weight.trashed') }}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('manage_Trashed_Data') }}">
                                                <i class="bi bi-recycle"></i>
                                            </a>
                                            @endcan
                                            @can('parcel_log')
                                            <a href="{{route('admin.parcel.attribute.weight.log')}}"
                                               class="btn btn-outline-primary px-3" data-bs-toggle="tooltip" data-bs-title="{{ translate('view_Log') }}">
                                                <i class="bi bi-clock-fill"></i>
                                            </a>
                                            @endcan

                                            @can('parcel_export')
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="dropdown">
                                                    <i class="bi bi-download"></i>
                                                    {{ translate('download') }}
                                                    <i class="bi bi-caret-down-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                    <li><a class="dropdown-item" href="{{ route('admin.parcel.attribute.weight.download') }}?file=excel&status={{request()->get('status') ?? "all"}}&search={{request()->get('search')}}">{{ translate('excel') }}</a></li>
                                                </ul>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>

                                    <div class="tmodel/inable-responsive mt-3 text-center">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>{{ translate('SL') }}</th>
                                                    <th class="text-capitalize name">{{ translate('parcel_weight_range') }}</th>

                                                    @can('parcel_edit')
                                                    <th class="status">{{ translate('status') }}</th>
                                                    @endcan

                                                    <th class="text-center action">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($weights as $key => $weight)
                                                    <tr id="hide-row-{{$weight->id}}" class="record-row">
                                                        <td>{{ $weights->firstItem() + $key }}</td>
                                                        <td class="name">{{ ($weight->min_weight+0).'-'.($weight->max_weight+0).' '. 'Kg'}}</td>
                                                        @can('parcel_edit')
                                                        <td class="status">
                                                            <label class="switcher mx-auto">
                                                                <input class="switcher_input status-change"  data-url={{ route('admin.parcel.attribute.weight.status') }} id="{{ $weight->id }}" type="checkbox" {{$weight->is_active?'checked':''}}>
                                                                <span class="switcher_control"></span>
                                                            </label>
                                                        </td>
                                                        @endcan
                                                        <td class="action">
                                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                                @can('parcel_log')
                                                                <a href="{{route('admin.parcel.attribute.weight.log')}}?id={{$weight->id}}" class="btn btn-outline-primary btn-action">
                                                                    <i class="bi bi-clock-fill"></i>
                                                                </a>
                                                                @endcan

                                                                @can('parcel_edit')
                                                                <a href="{{route('admin.parcel.attribute.weight.edit', ['id'=>$weight->id])}}" class="btn btn-outline-info btn-action">
                                                                    <i class="bi bi-pencil-fill"></i>
                                                                </a>
                                                                @endcan

                                                                @can('parcel_delete')
                                                                <button
                                                                    data-id="delete-{{ $weight->id }}" data-message="{{ translate('want_to_delete_this_weight?') }}"
                                                                    type="button" class="btn btn-outline-danger btn-action form-alert">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>

                                                                <form action="{{ route('admin.parcel.attribute.weight.delete', ['id'=>$weight->id]) }}" id="delete-{{ $weight->id }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
                                                                @endcan

                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="">
                                                        <td colspan="4">
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
                                    <div class="d-flex justify-content-end">
                                        {!! $weights->links() !!}
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

@push('script')

@endpush
