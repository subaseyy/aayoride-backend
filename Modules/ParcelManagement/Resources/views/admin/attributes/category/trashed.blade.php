@section('title', translate('parcel_Category'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <h2 class="fs-22 text-capitalize">{{ translate('deleted_parcel_category_list') }}</h2>

                    <div class="d-flex flex-wrap justify-content-between align-items-center my-3 gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-capitalize">{{ translate('total') }} : </span>
                            <span class="text-primary fs-16 fw-bold">{{ $categories->total() }}</span>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade active show" id="all-tab-pane" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-top d-flex flex-wrap gap-10 justify-content-between">
                                        <form action="{{url()->full()}}" class="search-form search-form_style-two"  method="GET">
                                            <div class="input-group search-form__input_group">
                                                <span class="search-form__icon">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="search" class="theme-input-style search-form__input" value="{{request()->get('search')}}" name="search" id="search"
                                                    placeholder="{{translate('search_here_by_Parcel_Category_Name')}}">
                                            </div>
                                            <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                                        </form>

                                        <div class="d-flex flex-wrap gap-3">
                                            <a href="{{route('admin.parcel.attribute.category.trashed')}}"
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>

                                            <a href="{{route('admin.parcel.attribute.category.log')}}"
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-clock-fill"></i>
                                            </a>

                                        </div>
                                    </div>

                                    <div class="tmodel/inable-responsive mt-3 text-center">
                                        <table class="table table-borderless align-middle">
                                            <thead class="table-light align-middle">
                                                <tr>
                                                    <th>{{ translate('SL') }}</th>
                                                    <th class="text-capitalize name">{{ translate('parcel_category_name') }}</th>
                                                    <th class="text-capitalize total-delivered">{{ translate('total_delivered') }}</th>
                                                    <th class="text-center action">{{ translate('action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($categories as $category)
                                                    <tr>
                                                        <td>{{ $loop->index + 1 }}</td>
                                                        <td class="name">{{ $category->name }}</td>
                                                        <td class="total-delivered">{{ $category->parcels->count() }}</td>
                                                        <td class="action">
                                                            <div class="d-flex justify-content-center gap-2 align-items-center">
                                                                <button
                                                                data-route="{{ route('admin.parcel.attribute.category.restore', ['id' => $category->id]) }}"
                                                                data-message="{{ translate('Want_to_recover_this_category?_') . translate('if_yes,_this_category_will_be_available_again_in_the_Category_List')}}"
                                                                class="btn btn-outline-primary btn-action restore-data">
                                                                <i class="bi bi-arrow-repeat"></i>
                                                            </button>
                                                                <button
                                                                    data-id="delete-{{ $category->id }}" data-message="{{ translate('want_to_permanent_delete_this_category?') }} {{translate('you_cannot_revert_this_action')}}"
                                                                    type="button" class="btn btn-outline-danger btn-action form-alert">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>

                                                                <form action="{{ route('admin.parcel.attribute.category.permanent-delete', ['id'=>$category->id]) }}" id="delete-{{ $category->id }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
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
                                        {!! $categories->links() !!}
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
