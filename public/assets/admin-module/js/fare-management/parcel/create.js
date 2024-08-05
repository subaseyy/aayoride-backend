"use strict";

$('#base_fare').keyup(function () {
    $('.base_fare').val($(this).val());
});
$('#base_fare').on('change',function () {
    $('.base_fare').val($(this).val());
});

$(document).ready(function () {
    $('input[type="checkbox"]').click(function () {
        var inputValue = $(this).attr("value");
        if ($(this).is(":checked")) {
            $("." + inputValue).removeClass('d-none');
            $("." + inputValue).removeAttr('disabled');
        } else {
            $("." + inputValue).addClass('d-none');
            $("." + inputValue).attr('disabled', 'disabled');
        }
    });
});
