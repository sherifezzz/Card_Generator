<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/profiles.css') }}">
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">Card Generator</div>
            <nav>
                <a href="{{ url('homepage') }}">Home</a>
                <a href="{{ url('card_creation') }}">Card Creation</a>
                <a href="{{ url('profile') }}" class="active">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="profile-container">
        <!-- Profile Header -->
        <section class="profile-header">
            <div class="profile-picture">
                @php
                    $defaultImage = 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->full_name) . '&background=random';
                    $profileImage = auth()->user()->profile_picture ? asset('storage/' . auth()->user()->profile_picture) : $defaultImage;
                @endphp
                <img src="{{ $profileImage }}" alt="Profile Picture" id="profileImage">
                <button id="changeProfilePic">Change Picture</button>
            </div>
            <div class="profile-info">
                <h1 id="userName">{{ auth()->user()->full_name }}</h1> <!-- Changed from "name" -->
                <p id="userEmail">{{ auth()->user()->email }}</p>
            </div>
        </section>

        <!-- Profile Details -->
        <section class="profile-details">
            <h2>Profile Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <label data-field="full_name">Full Name</label>
                    <p id="fullName">{{ auth()->user()->full_name }}</p>
                </div>
                <div class="detail-item">
                    <label data-field="email">Email</label>
                    <p id="email">{{ auth()->user()->email }}</p>
                </div>
                <div class="detail-item">
                    <label data-field="phone">Phone</label>
                    <p id="phone">{{ auth()->user()->phone ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label data-field="location">Location</label>
                    <p id="location">{{ auth()->user()->location ?? 'N/A' }}</p>
                </div>
                <div class="detail-item">
                    <label>Joined</label>
                    <p id="joinedDate">
                        {{ auth()->user()->joined_date ? date('F Y', strtotime(auth()->user()->joined_date)) : 'Not available' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Edit Profile Button -->
        <button id="editProfile" class="edit-button">Edit Profile</button>
    </main>

    <footer>
        <p>&copy; 2024 Card Generator. All rights reserved.</p>
    </footer>
    <script src="{{ asset('js/profile.js') }}"></script>
</body>

</html>