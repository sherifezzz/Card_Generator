<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/signins.css') }}">
</head>

<body>
    <div class="signin-container">
        <h1>Sign In</h1>
        <form id="signinForm" method="POST" action="{{ url('signin') }}">
            @csrf
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
                <label>
                    <input type="checkbox" name="rememberMe" id="rememberMe"> Remember Me
                </label>
            </div>
            <button type="submit" id="submitBtn">Login</button>
            <p class="signup-link">Don't have an account? <a href="{{ url('signup') }}">Sign Up</a></p>
        </form>
    </div>
    <script src="{{ asset('js/signin.js') }}"></script>
</body>

</html>