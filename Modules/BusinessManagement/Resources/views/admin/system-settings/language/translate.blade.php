@extends('adminmodule::layouts.master')

@section('title', translate('Languages'))


@section('content')
    <div class="content container-fluid">
        <div class="row __mt-20">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="search--button-wrapper justify-content-between">
                            <h5 class="m-0">{{translate('language_content_table')}}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="5">{{translate('SL')}}</th>
                                    <th width="">{{translate('key')}}</th>
                                    <th width="">{{translate('value')}}</th>
                                    <th width="10">{{translate('auto_translate')}}</th>
                                    <th width="10">{{translate('update')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @php($count=0)
                                @forelse($translateData as $key => $value)
                                    @php($count++)
                                    <tr id="lang-{{$count}}">
                                        <td>{{ $translateData->firstItem() + $count }}</td>
                                        <td>
                                            @php($key_view=str_replace( array("_"), ' ', $key))
                                            <input type="text" name="key[]"
                                                   value="{{$key}}" hidden>
                                            <label>{{$key_view}}</label>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="value[]"
                                                   id="value-{{$count}}"
                                                   value="{{$value}}">
                                        </td>
                                        <td class="__w-100px">
                                            <button type="button" data-key="{{ $key }}" data-id="{{ $count }}"
                                                    class="btn btn-secondary btn-block auto_translate">
                                                <i class="bi bi-globe"></i>
                                            </button>
                                        </td>
                                        <td class="__w-100px">
                                            <button type="button" data-key="{{ $key }}" data-count="{{ $count }}"
                                                    class="btn btn-primary btn-block update-lang"><i class="bi bi-sd-card-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14">
                                                <div class="d-flex flex-column justify-content-center align-items-center gap-2 py-3">
                                                    <img src="{{ asset('public/assets/admin-module/img/empty-icons/no-data-found.svg') }}" alt="" width="100">
                                                    <p class="text-center">{{translate('no_data_available')}}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            @if(count($translateData) !== 0)
                                <hr>
                            @endif
                            <div class="page-area">
                                {!! $translateData->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/business-management/language/translate.js') }}"></script>

    <script>
        function update_lang(key, value) {
            @if (env('APP_MODE')!='demo')
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business.languages.translate-submit',[$lang])}}",
                method: 'POST',
                data: {
                    key: key,
                    value: value
                },
                beforeSend: function () {
                    $('#loading').removeClass('d-none');
                },
                success: function (response) {
                    toastr.success('{{DEFAULT_UPDATE_200['message']}}');
                },
                complete: function () {
                    $('#loading').addClass('d-none');
                },
            });
            @else
            call_demo();
            @endif

        }

        function auto_translate(key, id) {
            @if (env('APP_MODE')!='demo')
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business.languages.auto-translate',[$lang])}}",
                method: 'POST',
                data: {
                    key: key
                },
                beforeSend: function () {
                    $('#loading').removeClass('d-none');
                },
                success: function (response) {
                    toastr.success('{{DEFAULT_UPDATE_200['message']}}');
                    $('#value-' + id).val(response.translated_data);
                },
                complete: function () {
                    $('#loading').addClass('d-none');
                },
            });
            @else
            call_demo();
            @endif
        }
    </script>

@endpush
