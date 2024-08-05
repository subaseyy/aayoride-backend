"use strict";

(function ($) {
    $(document).ready(function () {
        $('.js-select').select2();
        auto_grow();
    });
    $('.js-select2').select2({
        dropdownParent: $("#activityLogModal")
    });
    // character count
    function initialCharacterCount(item){
        let str = item.val();
        let maxCharacterCount = item.data('max-character');
        let characterCount = str.length;
        if (characterCount > maxCharacterCount) {
            item.val(str.substring(0, maxCharacterCount));
            characterCount = maxCharacterCount;
        }
        item.closest('.character-count').find('span').text(characterCount + '/' + maxCharacterCount);
    }
    $('.character-count-field').on('keyup change', function () {
        initialCharacterCount($(this));
    });
    $('.character-count-field').each(function () {
        initialCharacterCount($(this));
    });


    function auto_grow() {
        let element = document.getElementById("coordinates");
        element.style.height = "5px";
        element.style.height = (element.scrollHeight) + "px";
    }

    function ajax_get(route, id) {
        $.get({
            url: route,
            dataType: 'json',
            data: {},
            beforeSend: function () {
            },
            success: function (response) {
                $('#' + id).html(response.template);
            },
            complete: function () {
            },
        });
    }
})(jQuery);


