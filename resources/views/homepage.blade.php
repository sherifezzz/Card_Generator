<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">Card Generator</div>
            <div>
                <a href="{{ url('homepage') }}" class="active">Home</a>
                <a href="{{ url('card_creation') }}">Card Creation</a>
                <a href="{{ url('profile') }}">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>
    <main>
        <div class="welcome-message">
            Welcome back, <strong>{{auth()->user()->full_name}}</strong>! Here are your saved projects.
        </div>
        <!-- Projects Grid -->
        <div class="projects-grid" id="projectsGrid">
            <!-- Saved Card will be dynamically added here -->
        </div>

        <!-- Create New Card Button -->
        <button class="create-new-card">Create New Card</button>
    </main>
    <footer>
        <p>&copy; 2024 Card Generator. All rights reserved.</p>
    </footer>

    <script src="{{ asset('js/homepage.js') }}"></script>
</body>

</html>