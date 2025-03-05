// Description: This file contains all the utility functions that are used in the application.

// Function to toggle the visibility of the PASSWORD field
let passwordInput = document.getElementById('password');
let passwordIcon = document.getElementById('password-icon');

passwordIcon.addEventListener('click', () => {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.src="/build/assets/icons/visibility-on.svg";
    } else {
        passwordInput.type = 'password';
        passwordIcon.src="/build/assets/icons/visibility-off.svg";
    }
});


// Function to toggle the visibility of the CONFIRM PASSWORD field
let confirmPasswordInput = document.getElementById('password-confirm');
let confirmPasswordIcon = document.getElementById('confirm-password-icon');

confirmPasswordIcon.addEventListener('click', () => {
    if (confirmPasswordInput.type === 'password') {
        confirmPasswordInput.type = 'text';
        confirmPasswordIcon.src="/build/assets/icons/visibility-on.svg";
    } else {
        confirmPasswordInput.type = 'password';
        confirmPasswordIcon.src="/build/assets/icons/visibility-off.svg";
    }
});