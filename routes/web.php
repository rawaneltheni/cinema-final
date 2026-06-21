<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ShowtimeController;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// This route shows the public home page before the admin logs in.
Route::get('/', function (Request $request) {
    $search = $request->string('search')->trim()->toString();
    $movieGenres = Showtime::query()
        ->select('genre')
        ->distinct()
        ->orderBy('genre')
        ->pluck('genre');

    $latestMovies = Showtime::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('movie_title', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%");
            });
        })
        ->latest('created_at')
        ->limit(10)
        ->get();

    // Output buffering lets Laravel return the plain root index.php file as a response.
    ob_start();
    include base_path('index.php');

    return response(ob_get_clean());
});

// Old PHP-style login URL redirects to the Laravel named login route.
Route::get('/login.php', function () {
    return redirect()->route('login');
});

// Old PHP-style dashboard URL redirects to the Laravel showtimes dashboard.
Route::get('/dashboard.php', function () {
    return redirect()->route('showtimes.index');
});

// This route shows the login form.
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// This route receives the login form data and checks the username/password.
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
// This route logs the user out and clears the session.
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// GOOGLE LOGIN ROUTES
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])
    ->name('login.google');

Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// These showtime CRUD routes are protected by the username session middleware.
Route::middleware('username.session')->group(function () {
    Route::get('/calendar', [ShowtimeController::class, 'calendar'])->name('calendar');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.status');
    // Resource routes create index, create, store, edit, update, and destroy routes.
    Route::resource('showtimes', ShowtimeController::class)->except(['show']);
});
