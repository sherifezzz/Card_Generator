<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_cards.css') }}">
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">Card Generator</div>
            <div>
                <a href="{{ url('admin_dashboard') }}">Dashboard</a>
                <a href="{{ url('admin_users') }}">User Management</a>
                <a href="{{ url('admin_cards') }}" class="active">Card Management</a>
                <a href="{{ url('admin_profile') }}">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-card-management">
        <!-- Search bar -->
        <div class="search-bar">
            <input type="text" id="searchCard" placeholder="Search by card title, user, or date">
            <button id="searchButton">Search</button>
        </div>

        <div class="card-list">
            <table>
                <thead>
                    <tr>
                        <th>Card Title</th>
                        <th>Created By</th>
                        <th>Creation Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Table body will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Card Generator. All rights reserved.</p>
    </footer>
    <script src="{{ asset('js/Admins/admin_cards.js') }}"></script>
</body>

</html>