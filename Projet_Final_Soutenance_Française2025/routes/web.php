<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/api-docs', function () {
    return view('api-docs');
});

// Routes protégées par authentification
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/etudiants', function () {
        return Inertia::render('Etudiants/Index');
    })->name('etudiants.index');

    Route::get('/classes', function () {
        return Inertia::render('Classes/Index');
    })->name('classes.index');

    Route::get('/presences', function () {
        return Inertia::render('Presences/Index');
    })->name('presences.index');

    Route::get('/notifications', function () {
        return Inertia::render('Notifications/Index');
    })->name('notifications.index');

    Route::get('/profile/edit', function () {
        return Inertia::render('Profile/Edit');
    })->name('profile.edit');
});

require __DIR__.'/auth.php';
