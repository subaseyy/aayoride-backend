"use strict";

$(document).ready(function () {
    if ($("input[name='category_wise_different_fare']:checked").val() == 0) {
        $('#different-fare-div').addClass('d-none')
        $('#different-fare-div input').attr("disabled",true)
    } else {
        $('#different-fare-div').removeClass('d-none')
        $('#different-fare-div input').attr("disabled",false)
    }

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

$('.copy-value').on('change',function () {
    $('.' + $(this).attr('id') + '_default').val($(this).val());
})
$('.copy-value').keyup(function () {
    $('.' + $(this).attr('id') + '_default').val($(this).val());
})

$(".use_category_wise").click(function () {
    if ($(this).val() == 0) {
        $('#different-fare-div').addClass('d-none')
        $('#different-fare-div input').attr("disabled",true)

    } else if ($(this).val() == 1) {
        $('#different-fare-div').removeClass('d-none')
        $('#different-fare-div input').attr("disabled",false)
    }
});
