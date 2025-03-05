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