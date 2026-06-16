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
| Role-Specific Route Files
|--------------------------------------------------------------------------
| Student routes now live in routes/student.php (Developer A's track).
| Department/Registrar/Admin routes will live in their own files
| (Developer B's track) — require them here once built.
*/

require __DIR__.'/student.php';

// require __DIR__.'/department.php';   // Developer B
// require __DIR__.'/registrar.php';    // Developer B
// require __DIR__.'/admin.php';        // Developer B

require __DIR__.'/auth.php';
