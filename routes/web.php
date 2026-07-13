<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\DepartmentClearanceController;
use App\Http\Controllers\Admin\AdminOfficerController;
use App\Http\Controllers\Student\ClearanceController;
use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Registrar\RegistrarController;
use App\Http\Controllers\Public\CertificateVerificationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated — Generic Redirect
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route(Auth::user()->dashboardRoute());
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/student.php';

/*
|--------------------------------------------------------------------------
| Public Certificate Verification Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/public.php';

/*
|--------------------------------------------------------------------------
| Department Officer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:officer'])
    ->prefix('department')
    ->name('department.')
    ->group(function () {
        Route::get('/dashboard', [DepartmentClearanceController::class, 'index'])
            ->name('dashboard');
        Route::post('/clearances/{checkpoint}/review', [DepartmentClearanceController::class, 'review'])
            ->name('clearance.review');
    });

/*
|--------------------------------------------------------------------------
| Registrar Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:registrar'])
    ->prefix('registrar')
    ->name('registrar.')
    ->group(function () {
        Route::get('/dashboard', [RegistrarController::class, 'index'])->name('dashboard');
        Route::get('/applications/{application}', [RegistrarController::class, 'show'])
            ->name('applications.show');
        Route::post('/applications/{application}/approve', [RegistrarController::class, 'approve'])
            ->name('applications.approve');
        Route::post('/applications/{application}/reject', [RegistrarController::class, 'reject'])
            ->name('applications.reject');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminOfficerController::class, 'index'])->name('dashboard');
        Route::post('/officers', [AdminOfficerController::class, 'store'])->name('officers.store');
    });

    Route::get('/debug-mail', function() {
    return [
        'mailer'   => config('mail.default'),
        'host'     => config('mail.mailers.smtp.host'),
        'port'     => config('mail.mailers.smtp.port'),
        'username' => config('mail.mailers.smtp.username'),
        'from'     => config('mail.from'),
    ];
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';