"use strict";
//Sidebar Menu Search
let $menu = $('.main-nav > li');
$('#search-bar-input').keyup(function () {
    let search = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

    $menu.show().filter(function () {
        let text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
        return !~text.indexOf(search);
    }).hide();
});
