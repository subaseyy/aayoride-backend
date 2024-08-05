"use strict";

let lpSwitch = $('#loyalty_point_switch')
let points = $('#equivalent_points')

if (lpSwitch.prop('checked') === true) {
    points.prop('disabled', false)
    points.attr('required', true)
} else {
    points.prop('disabled', true)
}
lpSwitch.on('change', function () {

    if ($(this).prop('checked') === false) {
        points.prop('disabled', true)
    } else {
        points.prop('disabled', false)
        points.attr('required', true)
    }
})

let lvSwitch = $('#loyal_level_switch')
let lv_points = $('#equivalent_lv_points')

if (lvSwitch.prop('checked') === true) {
    lv_points.prop('disabled', false)
    lv_points.attr('required', true)
} else {
    lv_points.prop('disabled', true)
}
lvSwitch.on('change', function () {

    if ($(this).prop('checked') === false) {
        lv_points.prop('disabled', true)
    } else {
        lv_points.prop('disabled', false)
        lv_points.attr('required', true)
    }
})


$('.bidding-btn').on('click', function () {
    $(this).attr('value', 1)
})
