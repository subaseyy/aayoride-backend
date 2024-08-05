"use strict";

// Show the selected payment gateway form
$('#payment-gateway').change(function () {
    // Get the selected payment gateway value
    let selectedGateway = $(this).val();
    $('.payment-gateway-form').not('#' + selectedGateway + '-form').addClass('d-none');
    // Show the selected payment gateway form
    $('#' + selectedGateway + '-form').removeClass('d-none');
});

$('.myInput').change(function () {
    let input = $(this);
    let fileName = input.val().split('\\').pop();
    // Do something with the file name
    let reader = new FileReader();
    reader.onload = function (e) {
        input.parent().find('.viewer').empty().append($('<img>').attr('src', e.target.result).css({
            'width': '100px',
            'height': '100px',
            'border-radius': '5px'
        }));
    }
    reader.readAsDataURL(input[0].files[0]);
});
