<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_users.css') }}">
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">Card Generator</div>
            <div>
                <a href="{{ url('admin_dashboard') }}">Dashboard</a>
                <a href="{{ url('admin_users') }}" class="active">User Management</a>
                <a href="{{ url('admin_cards') }}">Card Management</a>
                <a href="{{ url('admin_profile') }}">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-user-management">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchUser" placeholder="Search by name, email, or username">
            <button id="searchButton">Search</button>
        </div>

        <!-- User List Table -->
        <div class="user-list">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>john_doe</td>
                        <td>john.doe@example.com</td>
                        <td>User</td>
                        <td>2023-10-01</td>
                        <td>
                            <button class="edit-user">Edit</button>
                            <button class="delete-user">Delete</button>
                            <button class="promote-user">Promote to Admin</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add User button -->
        <div class="add-user-section">
            <button id="addUserButton">Add New User</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Card Generator. All rights reserved.</p>
    </footer>
    <script src="{{ asset('js/Admins/admin_users.js') }}"></script>
</body>

</html>