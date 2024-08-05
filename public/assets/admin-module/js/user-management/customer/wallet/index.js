"use strict";

$(document).ready(function () {
    $('#addWallet').click(function () {
        document.getElementById("formSubmit").submit();
        $('#addWallet').attr('disabled', true);
        return true;
    });
    $('#dateRange').on('change', function () {
        //show 'from' & 'to' div
        if (this.value === 'custom_date') {
            $('#fromFilterDiv').removeClass('d-none');
            $('#toFilterDiv').removeClass('d-none');
        }

        //hide 'from' & 'to' div
        if (this.value !== 'custom_date') {
            $('#fromFilterDiv').addClass('d-none');
            $('#toFilterDiv').addClass('d-none');
        }
    });
});
