<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_profiles.css') }}">
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">Card Generator</div>
            <div>
                <a href="{{ url('admin_dashboard') }}">Dashboard</a>
                <a href="{{ url('admin_users') }}">User Management</a>
                <a href="{{ url('admin_cards') }}">Card Management</a>
                <a href="{{ url('admin_profile') }}" class="active">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-profile">
        <!-- Profile Header -->
        <section class="profile-header">
            <div class="profile-picture">
                <img src="{{ $user->profile_picture ?? asset('default-profile.jpg') }}" alt="Profile Picture" id="profileImage">
                <button id="changeProfilePic">Change Picture</button>
            </div>
            <div class="profile-info">
                <h2 id="adminName">{{ $user->username }}</h2>
                <p id="adminEmail">{{ $user->email }}</p>
                <p id="adminRole">{{ ucfirst($user->role) }}</p>
            </div>
        </section>

        <!-- Profile Details -->
        <section class="profile-details">
            <h2>Profile Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label>Full Name</label>
                    <p id="fullName">{{ $user->full_name }}</p>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <p id="email">{{ $user->email }}</p>
                </div>
                <div class="detail-item">
                    <label>Phone</label>
                    <p id="phone">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Location</label>
                    <p id="location">{{ $user->location ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Joined</label>
                    <p id="joinedDate">{{ $user->joined_date ? date('F Y', strtotime($user->joined_date)) : 'Not available' }}</p>
                </div>
            </div>
        </section>

        <!-- Edit Profile Button -->
        <div class="edit-profile">
            <button id="editProfileButton">Edit Profile</button>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Card Generator. All rights reserved.</p>
    </footer>

    <script src="{{ asset('js/Admins/admin_profiles.js') }}"></script>
</body>

</html>