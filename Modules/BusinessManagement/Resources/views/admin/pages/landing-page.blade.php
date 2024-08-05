@extends('adminmodule::layouts.master')

@section('title', translate($type ?? 'about_us'))

@push('css_or_js')
    <link rel="stylesheet" href="{{asset('public/assets/admin-module/plugins/summernote/summernote-lite.min.css')}}"/>
@endpush

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="fs-22 mb-4">{{translate('Business_Pages')}}</h2>

            <form action="{{route('admin.business.pages-media.business-page.update')}}" id="business_pages_form"
                  method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
                        <li class="nav-item">
                            <a href="{{url()->current()}}?type=about_us"
                               class="nav-link text-capitalize {{$type == 'about_us'? 'active' : ''}}">{{translate('about_us')}}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url()->current()}}?type=privacy_policy"
                               class="nav-link text-capitalize {{$type == 'privacy_policy'? 'active' : ''}}">{{translate('privacy_policy')}}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url()->current()}}?type=terms_and_conditions"
                               class="nav-link text-capitalize {{$type == 'terms_and_conditions'? 'active' : ''}}">{{translate('terms_and_conditions')}}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url()->current()}}?type=legal"
                               class="nav-link text-capitalize {{$type == 'legal'? 'active' : ''}}">{{translate('legal')}}</a>
                        </li>
                    </ul>
                </div>

            </form>
        </div>
    </div>
    <!-- End Main Content -->

@endsection

@push('script')
    <script src="{{asset('public/assets/admin-module/plugins/summernote/summernote-lite.min.js')}}"></script>

    <script>
        @can('business_edit')
        let permission = true;
        @else
        let permission = false;
        @endcan
    </script>

    <script>
        $('#business_pages_form').on('submit', function (e) {
            if (!permission) {
                toastr.error('{{ translate('you_do_not_have_enough_permission_to_update_this_settings') }}');
                e.preventDefault();
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#summernote').summernote({
                placeholder: '{{translate('describe_about_this_page')}}',
                tabsize: 2,
                height: 200,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
@endpush
