<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/sign_up.css') }}">
</head>

<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <form id="signupForm" action="{{ url('signup') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter Email" required>
                <span class="error-message" id="emailError"></span>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Enter Username" required>
                <span class="error-message" id="usernameError"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter Password" required>
                <span class="error-message" id="passwordError"></span>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
                <span class="error-message" id="confirmPasswordError"></span>
            </div>
            <button type="submit" id="submitBtn">Sign Up</button>
            <p class="login-link">Already have an account? <a href="{{ url('signin') }}">Log In</a></p>
        </form>
    </div>
    <script src="{{ asset('js/signup.js') }}"></script>
</body>

</html>