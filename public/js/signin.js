document.getElementById('signinForm').addEventListener('submit', function (event) {
    // Get input values
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();
    const usernameError = document.getElementById('usernameError');
    const passwordError = document.getElementById('passwordError');

    // Reset error messages
    usernameError.textContent = '';
    passwordError.textContent = '';

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
    }
});

// Remember Me functionality
const rememberMe = document.getElementById('rememberMe');
const usernameInput = document.getElementById('username');

// Check if username is saved in LocalStorage
if (localStorage.getItem('rememberMe') === 'true') {
    usernameInput.value = localStorage.getItem('username');
    rememberMe.checked = true;
}

// Save username if "Remember Me" is checked
rememberMe.addEventListener('change', function () {
    if (this.checked) {
        localStorage.setItem('username', usernameInput.value);
        localStorage.setItem('rememberMe', true);
    } else {
        localStorage.removeItem('username');
        localStorage.removeItem('rememberMe');
    }
});