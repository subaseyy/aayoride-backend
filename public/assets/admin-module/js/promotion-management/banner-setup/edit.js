"use strict";

$('#time_period').change(function () {
    let timePeriod = this.value;

    if (timePeriod === 'all_time') {
        $('.date-pick').addClass('d-none');
        $('#start_date').val(''); // Clear the start date value
        $('#end_date').val(''); // Clear the end date value
    } else {
        $('.date-pick').removeClass('d-none');
    }
});
