@extends('Gateways::payment.layouts.master')

@push('script')
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@section('content')
    <center><h1>Please do not refresh this page...</h1></center>

<script type="text/javascript">
    "use strict";
    // Create an instance of the Stripe object with your publishable API key
    let stripe = Stripe('{{$config->published_key}}');
    document.addEventListener("DOMContentLoaded", function () {
        fetch("{{ url("payment/stripe/token/?payment_id={$data->id}") }}", {
            method: "GET",
        }).then(function (response) {
            return response.text();
        }).then(function (session) {
            return stripe.redirectToCheckout({sessionId: JSON.parse(session).id});
        }).then(function (result) {
            if (result.error) {
                alert(result.error.message);
            }
        }).catch(function (error) {
            console.error("error:", error);
        });
    });

</script>
@endsection
