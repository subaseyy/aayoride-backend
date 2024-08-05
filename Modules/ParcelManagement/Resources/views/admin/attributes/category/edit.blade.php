@section('title', translate('edit_Parcel_Category'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <form action="{{ route('admin.parcel.attribute.category.update', ['id' => $category->id]) }}"
                        enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-primary text-uppercase mb-4">{{ translate('edit_parcel_category') }}
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="category_name"
                                                class="mb-2">{{ translate('category_name') }}</label>
                                            <input type="text" id="category_name" name="category_name"
                                                class="form-control" value="{{ $category->name }}"
                                                placeholder="Ex: Category name">
                                        </div>
                                        <div class="mb-4">
                                            <label for="short_desc"
                                                class="mb-2">{{ translate('short_description') }}</label>
                                            <textarea id="short_desc" rows="5" name="short_desc" class="form-control" placeholder="Ex: Description">{{ $category->description }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card-body d-flex flex-column gap-3">
                                            <h5 class="text-center text-capitalize">{{ translate('category_icon') }}
                                            </h5>

                                            <div class="d-flex justify-content-center">
                                                <div class="upload-file">
                                                    <input type="file" class="upload-file__input" accept=".png" name="category_icon">
                                                    <span class="edit-btn">
                                                        <i class="bi bi-pencil-square text-primary"></i>
                                                    </span>
                                                    <div class="upload-file__img w-auto h-auto">
                                                        <img  width="150" src="{{ onErrorImage(
                                                            $category?->image,
                                                            asset('storage/app/public/parcel/category') . '/' . $category?->image,
                                                            asset('public/assets/admin-module/img/media/upload-file.png'),
                                                            'parcel/category/',
                                                        ) }}"
                                                            alt="">
                                                    </div>
                                                </div>
                                            </div>

                                            <p class="opacity-75 mx-auto text-center max-w220">
                                                {{ translate('File Format - png | Image Size - Maximum Size 5 MB.') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
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
