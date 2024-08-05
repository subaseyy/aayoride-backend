"use strict";

$('.update-information').on('click', function () {
    $('#id').val($(this).data('id'))
    $('#redirect_link').val($(this).data('link'))
    $('#social_media_name').val($(this).data('name')).change()
    $('#update-btn').removeClass('d-none')
    $('#save-btn').addClass('d-none')
})
