<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Card Generator</title>
    <link rel="stylesheet" href="{{ asset('css/card_creation.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="siteLogo">Card Generator</div>
            <div>
                <a href="{{ url('homepage') }}">Home</a>
                <a href="{{ url('card_creation') }}" class="active">Card Creation</a>
                <a href="{{ url('profile') }}">Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>
    <main class="card-creator">
        <!-- Sidebar - Card Details (Collapsible) -->
        <aside class="sidebar" id="sidebar">
            <div class="input-panel">
                <h2>Card Details</h2>
                <form id="cardDetailForm">
                @csrf
                    <!-- Card Name -->
                    <div class="form-group">
                        <label for="cardName">Card Name</label>
                        <input type="text" name="cardName" id="cardName" required>
                    </div>

                    <!-- Personal Information Section -->
                    <div class="form-section personal-info">
                        <h3>Personal Information</h3>
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" name="fullName" id="fullName" required>
                        </div>
                        <div class="form-group">
                            <label for="jobTitle">Job Title</label>
                            <input type="text" name="jobTitle" id="jobTitle" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" name="phone" id="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="Address">Address</label>
                            <textarea name="Address" id="address" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- QR Code URL Section -->
                    <div class="form-section">
                        <h3>QR Code URL</h3>
                        <div class="form-group">
                            <label for="qrUrl">QR Code URL</label>
                            <input type="url" name="qrUrl" id="qrUrl" placeholder="Enter URL for QR Code">
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Main Content - Card Preview -->
        <section class="preview-panel">
            <h2>Live Preview</h2>
            <div class="preview-controls">
                <button type="button" id="zoomIn">Zoom In</button>
                <button type="button" id="zoomOut">Zoom Out</button>
                <button type="button" id="resetZoom">Reset</button>
                <button type="button" id="saveCard">Save Card</button>
            </div>

            <div id="cardPreview" class="preview-area">
                <!-- Updated Card Design Structure -->
                <div class="card">
                    <div class="left-side">
                        <div class="name" id="previewFullName">Full Name</div>
                        <div class="title" id="previewJobTitle">Job Title</div>
                        <ul class="contact-info">
                            <li id="previewPhone">Phone</li>
                            <li id="previewEmail">Email</li>
                            <li id="previewAddress">Address</li>
                        </ul>
                    </div>
                    <div class="right-side">
                        <div id="qrCodeContainer">
                            <div id="qrCode"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Card Generator. All rights reserved.</p>
    </footer>
    <script src="{{ asset('js/card_creation.js') }}"></script>
</body>

</html>