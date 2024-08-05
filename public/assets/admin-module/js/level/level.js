"use strict";

$(window).on("load", function () {
    if($(".instruction-carousel").length) {
        let slideCount = $(".instruction-carousel .swiper-slide").length;
        let swiperPaginationCustom = $('.instruction-pagination-custom');
        let swiperPaginationAll = $('.instruction-pagination-custom, .instruction-pagination');
        swiperPaginationCustom.html(`1 / ${slideCount}`);

        var swiper = new Swiper(".instruction-carousel", {
            autoHeight: true,
            pagination: {
                el: ".instruction-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            on: {
                slideChange: () => {
                    swiperPaginationCustom.html(`${swiper.realIndex + 1} / ${swiper.slidesGrid.length}`);
                },
            }
        });
    }
});

$(document).ready(function () {
    $('.check-toggle-item .custom-checkbox input').each(function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
            $(this).closest('.check-toggle-item').find('.check-toggle-content').show();
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').attr('required', 'required');
        } else {
            $(this).val(0);
            $(this).closest('.check-toggle-item').find('.check-toggle-content').hide();
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').removeAttr('required');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').removeAttr('step');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').removeAttr('min');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').val(null);
        }
    });
    $('.check-toggle-item .custom-checkbox input').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
            $(this).closest('.check-toggle-item').find('.check-toggle-content').show();
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').attr('required', 'required');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').val();
        } else {
            $(this).val(0);
            $(this).closest('.check-toggle-item').find('.check-toggle-content').hide();
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').removeAttr('required');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').removeAttr('step');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').removeAttr('min');
            $(this).closest('.check-toggle-item').find('.check-toggle-content').find('input[type="number"]').val(null);
        }
    });

    if ($('#rewardType').val() === 'no_rewards') {
        $('#rewardAmountDiv').addClass('d-none')
        hideReward()
    }
    $("#rewardType").on('change', function () {
        hideReward(this)
    })
});
