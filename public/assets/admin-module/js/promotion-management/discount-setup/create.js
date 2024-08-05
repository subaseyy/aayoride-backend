"use strict";
$(document).ready(function () {
    // Set up event listener to handle selection change
     $('.js-select').select2();
    let select = $('.js-select-2').select2({
        placeholder: $(this).data('placeholder')
    });
    select.on('select2:select', function (e) {
        let select = $(this);
        if (e.params.data.id === 'all') {
            select.find('option').prop('selected', false);
            select.val(['all']).trigger('change');
        } else {
            let selectedValues = select.val().filter(item => item !== 'all');
            select.find('option[value="all"]').prop('selected', false);
            select.val(selectedValues).trigger('change');
        }
    });

    select.on('select2:unselect', function (e) {
        let select = $(this);
        select.find('option[value="all"]').prop('selected', false);
    });
})

