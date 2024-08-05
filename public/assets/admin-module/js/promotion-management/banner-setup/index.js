"use strict";

$('#time_period').change(function () {
    let time_period = this.value;

    if (time_period == 'all_time') {
        $('.date-pick').removeClass('d-none');
    } else {
        $('.date-pick').removeClass('d-none');
    }
});
