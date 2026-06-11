<?php

use App\Http\Controllers\ProfileController;
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
| Authenticated - Generic Redirect
|--------------------------------------------------------------------------
| After login, send users to their role-specific dashboard.
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        return redirect()->route($user->dashboardRoute());
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

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.student');
        })->name('dashboard');

        // Week 3: application form, document upload, status tracker
    });

/*
|--------------------------------------------------------------------------
| Department Officer Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:officer'])
    ->prefix('department')
    ->name('department.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboards.department');
        })->name('dashboard');

        // Week 4: pending requests, approve/reject
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
        Route::get('/dashboard', function () {
            return view('dashboards.registrar');
        })->name('dashboard');

        // Week 5: validate eligibility, overrides, analytics
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
        Route::get('/dashboard', function () {
            return view('dashboards.admin');
        })->name('dashboard');

        // Week 5: manage users, departments, settings
    });

require __DIR__.'/auth.php';
