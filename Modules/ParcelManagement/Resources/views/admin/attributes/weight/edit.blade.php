@section('title', 'Edit Parcel Weight')

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <form action="{{ route('admin.parcel.attribute.weight.update', ['id' => $weight->id]) }}"
                          enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('edit_weight_range') }}</h5>
                                <div class="row">
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="weight_unit"
                                                   class="mb-2">{{ translate('weight_unit') }}</label>
                                            <select class="js-select" id="weight_unit" name="weight_unit" disabled>
                                                <option value="0" selected
                                                        disabled>{{translate('select_weight_unit') }}</option>
                                                <option value="{{$weightUnit?->value}}"
                                                        selected='selected'>{{$weightUnit?->value == 'kg'?'Kilogram':''}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="min_weight"
                                                   class="mb-2">{{ translate('minimum_weight') }}</label>
                                            <input type="number" id="min_weight" name="min_weight" step=".01"
                                                   value="{{$weight->min_weight}}" class="form-control"
                                                   placeholder="Ex: Minimum Weight">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xl-4">
                                        <div class="mb-4 text-capitalize">
                                            <label for="max_weight"
                                                   class="mb-2">{{ translate('maximum_weight') }}</label>
                                            <input type="number" id="max_weight" name="max_weight" step=".01"
                                                   value="{{$weight->max_weight}}" class="form-control"
                                                   placeholder="Ex: Maximum Weight">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit"
                                            class="btn btn-primary">{{ translate('update') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Main Content -->
@endsection

@push('script')

@endpush
