<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirection racine
Route::get('/', fn() => redirect()->route('applications.index'));

// Dashboard Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Changement de mot de passe obligatoire (hors groupe pour éviter boucle)
Route::middleware(['auth'])->group(function () {
    Route::get('/password/change', [PasswordChangeController::class, 'show'])
        ->name('password.change');
    Route::post('/password/change', [PasswordChangeController::class, 'update'])
        ->name('password.change.update');
});

// Routes protégées avec force password change
Route::middleware(['auth', 'force.password.change'])->group(function () {

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Applications
    Route::resource('applications', ApplicationController::class);

    // Incidents
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::post('/incidents/{incident}/acknowledge', [IncidentController::class, 'acknowledge'])
        ->name('incidents.acknowledge');

    // Maintenances
    Route::resource('maintenances', MaintenanceController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::post('/maintenances/{maintenance}/finish', [MaintenanceController::class, 'finish'])
        ->name('maintenances.finish');

    // Rapports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Utilisateurs
    Route::resource('users', UserController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    // Paramètres
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-email', [App\Http\Controllers\SettingController::class, 'testEmail'])->name('settings.test-email');

    // Audit logs
    Route::get('/audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');

    // Alertes
    Route::get('/alerts', [App\Http\Controllers\AlertController::class, 'index'])->name('alerts.index');
});

// Réinitialisation autonome mot de passe
Route::get('/forgot-password-custom', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'show'])
    ->name('password.forgot')->middleware('guest');
Route::post('/forgot-password-custom', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'send'])
    ->name('password.forgot.send')->middleware('guest');




require __DIR__ . '/auth.php';
