"use strict"

let dropdownItems = document.querySelectorAll('.withdraw-method');
let dropdownButton = document.getElementById('selectWithdrawMethod');

dropdownItems.forEach(function (item) {
    item.addEventListener('click', function (event) {
        event.preventDefault();
        let selectedText = this.innerText;
        let selectedValue = this.getAttribute('data-value');
        dropdownButton.innerHTML = selectedText + ' <i class="bi bi-caret-down-fill"></i>';

        // Get the URL from the button's data-url attribute
        let url = dropdownButton.getAttribute('data-url');

        // Create a new URL object
        let urlObj = new URL(url);

        // Append the selected value as a query parameter
        urlObj.searchParams.set('method', selectedValue);

        // Redirect to the new URL
        window.location.href = urlObj.toString();
    });
});

// multiselect table
$(document).ready(function () {
    $(".leading_checkbox").on("change", function () {
        $(this)
            .closest(".multiselect-table")
            .find('tbody input[type="checkbox"]')
            .prop("checked", this.checked);

        if (this.checked) {
            $(".settle-btn").removeClass('d-none');
            $(".reverse-btn").removeClass('d-none');
            $(".denied-btn").removeClass('d-none');
            $(".approve-btn").removeClass('d-none');
            $(".multiple-invoice").removeClass('d-none');
            $(".all-invoice").addClass('d-none');
        } else {
            $(".settle-btn").addClass('d-none');
            $(".reverse-btn").addClass('d-none');
            $(".denied-btn").addClass('d-none');
            $(".approve-btn").addClass('d-none');
            $(".multiple-invoice").addClass('d-none');
            $(".all-invoice").removeClass('d-none');

        }
    });

    // select all
    // $(".settlement-btn").hide();
    $('.multiselect-table tbody input[type="checkbox"]').on(
        "change",
        function () {
            let totalCheckbox = $(
                '.multiselect-table tbody input[type="checkbox"]'
            ).length;
            let totalCheckboxChecked = $(
                '.multiselect-table tbody input[type="checkbox"]:checked'
            ).length;

            let allChecked = totalCheckbox === totalCheckboxChecked;
            $(".leading_checkbox").prop("checked", allChecked);

            if ($('.multiselect-table input[type="checkbox"]:checked').length) {
                $(".settle-btn").removeClass('d-none');
                $(".reverse-btn").removeClass('d-none');
                $(".denied-btn").removeClass('d-none');
                $(".approve-btn").removeClass('d-none');
                $(".multiple-invoice").removeClass('d-none');
                $(".all-invoice").addClass('d-none');
            } else {
                $(".settle-btn").addClass('d-none');
                $(".reverse-btn").addClass('d-none');
                $(".denied-btn").addClass('d-none');
                $(".approve-btn").addClass('d-none');
                $(".multiple-invoice").addClass('d-none');
                $(".all-invoice").removeClass('d-none');

            }
        }
    );

    //withdraw-info-aside_close
    $(".withdraw-info-aside_close").on("click", function () {
        $(".withdraw-info-aside_wrap").removeClass("active");
    });

});


