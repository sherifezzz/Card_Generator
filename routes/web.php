<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\ProfileController;

// Redirect authenticated users to the homepage
Route::get('/', function () {
    return \Illuminate\Support\Facades\Auth::check() ? redirect()->route('homepage') : redirect()->route('signin');
});

// Signin Routes
Route::get('/signin', [AuthController::class, 'showSignIn'])->name('signin')->middleware('guest');
Route::post('/signin', [AuthController::class, 'signIn'])->name('signin.post');

// Signup Routes
Route::get('/signup', [AuthController::class, 'showSignUp'])->name('signup')->middleware('guest');
Route::post('/signup', [AuthController::class, 'signUp'])->name('signup.post');

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Existing routes
    Route::get('/homepage', [HomeController::class, 'index'])->name('homepage');
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::match(['PUT', 'POST'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/card_creation', [CardController::class, 'showCardCreation'])->name('card_creation');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // New API routes for card management
    Route::middleware('auth')->group(function () {
        // Card routes
        Route::get('/card_creation', [CardController::class, 'showCardCreation'])->name('card_creation');
        Route::post('/card_creation', [CardController::class, 'store']);

        // Card API routes - update these routes
        Route::get('/api/cards', [CardController::class, 'index']);
        Route::post('/api/cards', [CardController::class, 'store']);
        Route::get('/api/cards/{id}', [CardController::class, 'show']);
        Route::put('/api/cards/{id}', [CardController::class, 'update']);
        Route::delete('/api/cards/{id}', [CardController::class, 'destroy']);
    });
});

// Admin Routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin_dashboard', [AdminController::class, 'dashboard'])->name('admin_dashboard');
    Route::get('/admin_users', [AdminController::class, 'userManagement'])->name('admin_users');
    Route::get('/admin_cards', [AdminController::class, 'cardManagement'])->name('admin_cards');
    Route::get('/admin_profile', [AdminController::class, 'adminProfile'])->name('admin_profile');

    // Add the new API endpoint for dashboard data
    Route::get('/api/admin/dashboard-data', [AdminController::class, 'getDashboardData'])->name('admin.dashboard.data');
    Route::prefix('api/admin')->group(function () {
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::get('/users/{id}', [AdminController::class, 'getUser']);
        Route::post('/users', [AdminController::class, 'addUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::post('/users/{id}/promote', [AdminController::class, 'promoteUser']);
    });

    Route::prefix('api/admin')->group(function () {
        Route::get('/cards', [AdminController::class, 'getCards']);
        Route::get('/cards/{id}', [AdminController::class, 'getCard']);
        Route::delete('/cards/{id}', [AdminController::class, 'deleteCard']);
    });

    // Profile routes
    Route::post('/api/admin/profile', [AdminController::class, 'updateProfile']);
    Route::post('/api/admin/profile/picture', [AdminController::class, 'updateProfilePicture']);
});