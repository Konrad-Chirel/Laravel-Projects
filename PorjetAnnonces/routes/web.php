<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ApplicationController;

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes pour l'admin
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Routes pour l'entreprise
Route::middleware(['auth', 'enterprise'])->group(function () {
    Route::get('/enterprise/dashboard', function () {
        return view('enterprise.dashboard');
    })->name('enterprise.dashboard');
});

// Routes pour les annonces
Route::middleware(['auth'])->group(function () {
    Route::resource('announcements', AnnouncementController::class);
    
    // Routes spécifiques pour la validation des annonces (admin uniquement)
    Route::middleware(['admin'])->group(function () {
        Route::post('/announcements/{announcement}/validate', [AnnouncementController::class, 'validate'])
            ->name('announcements.validate');
        Route::post('/announcements/{announcement}/reject', [AnnouncementController::class, 'reject'])
            ->name('announcements.reject');
    });

    // Routes pour les candidatures
    Route::prefix('applications')->group(function () {
        // Routes accessibles à tous les utilisateurs authentifiés
        Route::get('/', [ApplicationController::class, 'index'])->name('applications.index');
        
        // Routes pour les étudiants
        Route::middleware(['student'])->group(function () {
            Route::post('/announcements/{announcement}/apply', [ApplicationController::class, 'apply'])
                ->name('applications.apply');
            Route::delete('/announcements/{announcement}/withdraw', [ApplicationController::class, 'withdraw'])
                ->name('applications.withdraw');
        });

        // Routes pour les entreprises
        Route::middleware(['enterprise'])->group(function () {
            Route::get('/announcements/{announcement}', [ApplicationController::class, 'showForAnnouncement'])
                ->name('applications.show-for-announcement');
            Route::patch('/{application}/status', [ApplicationController::class, 'updateStatus'])
                ->name('applications.update-status');
        });
    });
});
