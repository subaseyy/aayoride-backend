@extends('landing-page.layouts.master')
@section('title', 'Terms & Conditions')


@section('content')
    <!-- Page Header Start -->
    <div class="container pt-3">
        <section class="page-header bg__img"
                 data-img="{{$data?->value['image'] ? asset('storage/app/public/business/pages/'.$data?->value['image']) : asset('public/landing-page/assets/img/page-header.png')}}"
                 style="background-image: url({{$data?->value['image'] ? asset('storage/app/public/business/pages/'.$data?->value['image']) : asset('public/landing-page/assets/img/page-header.png')}});">
            <h3 class="title">{{ translate('Terms & Condition') }}</h3>
            <p class="mt-2">
                {{ $data?->value['short_description'] ?? "" }}
            </p>
        </section>
    </div>
    <!-- Page Header End -->
    <section class="terms-section py-5">
        <div class="container">
            {!! $data?->value['long_description'] !!}
        </div>
    </section>
@endsection
