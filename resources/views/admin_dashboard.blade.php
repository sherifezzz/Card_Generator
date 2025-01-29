<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/Admin/admin_global.css') }}">
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo">Card Generator</div>
            <div>
                <a href="{{ url('admin_dashboard') }}" class="active">Dashboard</a>
                <a href="{{ url('admin_users') }}">User Management</a>
                <a href="{{ url('admin_cards') }}">Card Management</a>
                <a href="{{ url('admin_profile') }}">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-dashboard">
        <!-- Overview Section -->
        <section class="overview">
            <h2>System Overview</h2>
            <div class="metrics-grid">
                <div class="metric-card">
                    <h3>Total Users</h3>
                    <p class="metric-value">{{ number_format($totalUsers) }}</p>
                </div>
                <div class="metric-card">
                    <h3>Total Cards Created</h3>
                    <p class="metric-value">{{ number_format($totalCards) }}</p>
                </div>
                <div class="metric-card">
                    <h3>Recent Activity</h3>
                    <p class="metric-value">{{ $todayNewCards }} New Cards (Today)</p>
                </div>
                <div class="metric-card">
                    <h3>System Status</h3>
                    <p class="status-online">Online</p>
                </div>
            </div>
        </section>

        <!-- Quick Actions Section -->
        <section class="quick-actions">
            <h2>Quick Actions</h2>
            <div class="action-grid">
                <a href="{{ url('admin_users') }}" class="action-card">
                    <h3>Manage Users</h3>
                    <p>View, edit, or delete user accounts.</p>
                </a>
                <a href="{{ url('admin_cards') }}" class="action-card">
                    <h3>Manage Cards</h3>
                    <p>View or delete user-created cards.</p>
                </a>
                <a href="{{ url('admin_settings') }}" class="action-card">
                    <h3>System Settings</h3>
                    <p>Configure system-wide options.</p>
                </a>
            </div>
        </section>

        <!-- Recent Activity Log Section -->
        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-log">
                <ul>
                    @forelse($recentActivity as $activity)
                        <li>
                            <strong>{{ $activity['user'] }}</strong>
                            {{ $activity['action'] }} -
                            <span class="timestamp">{{ $activity['timestamp'] }}</span>
                        </li>
                    @empty
                        <li>No recent activity</li>
                    @endforelse
                </ul>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Card Generator. All rights reserved.</p>
    </footer>
    <script src="{{ asset('js/Admins/admin_dashboard.js') }}"></script>
</body>

</html>