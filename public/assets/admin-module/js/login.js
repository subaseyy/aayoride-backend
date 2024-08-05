"use strict";

let password = document.getElementById('password');
let passwordIcon = document.getElementById('password-eye');

passwordIcon.onclick = function () {
    if (password.getAttribute('type') === 'text') {
        password.setAttribute('type', 'password');
        passwordIcon.removeAttribute('class');
        passwordIcon.setAttribute('class', 'mt-3 bi bi-eye-slash-fill text-muted tooltip-icon');
    } else {
        password.setAttribute('type', 'text');
        passwordIcon.removeAttribute('class');
        passwordIcon.setAttribute('class', 'mt-3 bi bi-eye-fill text-muted tooltip-icon');
    }
}
