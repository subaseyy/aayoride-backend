"use strict";

let confPassword = document.getElementById('confirm_password');
let confPasswordIcon = document.getElementById('conf-password-eye');
let password = document.getElementById('password');
let passwordIcon = document.getElementById('password-eye');
confPasswordIcon.onclick = function () {
    if (confPassword.getAttribute('type') === 'text') {
        confPassword.setAttribute('type', 'password');
        confPasswordIcon.removeAttribute('class');
        confPasswordIcon.setAttribute('class', 'mt-3 bi bi-eye-slash-fill text-primary tooltip-icon');
    } else {
        confPassword.setAttribute('type', 'text');
        confPasswordIcon.removeAttribute('class');
        confPasswordIcon.setAttribute('class', 'mt-3 bi bi-eye-fill text-primary tooltip-icon');
    }
}

passwordIcon.onclick = function () {
    if (password.getAttribute('type') === 'text') {
        password.setAttribute('type', 'password');
        passwordIcon.removeAttribute('class');
        passwordIcon.setAttribute('class', 'mt-3 bi bi-eye-slash-fill text-primary tooltip-icon');
    } else {
        password.setAttribute('type', 'text');
        passwordIcon.removeAttribute('class');
        passwordIcon.setAttribute('class', 'mt-3 bi bi-eye-fill text-primary tooltip-icon');
    }
}
