"use strict";

$(".update-lang").on('click', function () {
    let key = $(this).data('key')
    let count = $(this).data('count')
    let value = $(`#value-${count}`).val()
    update_lang(key, value)
})

$(".auto_translate").on('click', function () {
    let key = $(this).data('key')
    let id = $(this).data('id')
    auto_translate(key, id)
})
