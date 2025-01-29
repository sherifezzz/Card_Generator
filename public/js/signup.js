document.getElementById('signupForm').addEventListener('submit', function (event) {
    // Get input values
    const email = document.getElementById('email').value.trim();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();

    // Get Error Message Elements
    const emailError = document.getElementById('emailError');
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');
    const confirmPasswordError = document.getElementById('confirmPasswordError');

    // Reset Error Messages
    emailError.textContent = '';
    usernameError.textContent = '';
    passwordError.textContent = '';
    confirmPasswordError.textContent = '';

    // Validate email
    if (email === '') {
        event.preventDefault();
        emailError.textContent = 'Email is required.';
        return;
    } else if (!validateEmail(email)) {
        event.preventDefault();
        emailError.textContent = 'Please enter a valid email address';
        return;
    }

    // Validate username
    if (username === '') {
        event.preventDefault();
        usernameError.textContent = 'Username is required';
        return;
    }

    // Validate password
    if (password === '') {
        event.preventDefault();
        passwordError.textContent = 'Password is required';
        return;
    } else if (password.length < 8) {
        event.preventDefault();
        passwordError.textContent = 'Password must be at least 8 characters long.';
        return;
    }

    // Validate confirm password
    if (confirmPassword === '') {
        event.preventDefault();
        confirmPasswordError.textContent = 'Please confirm your password';
        return;
    } else if (password !== confirmPassword) {
        event.preventDefault();
        confirmPasswordError.textContent = 'Passwords do not match.';
        return;
    }
});

// Function to validate email format
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}