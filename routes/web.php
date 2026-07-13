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

/*
|--------------------------------------------------------------------------
| Mail Diagnostic / Troubleshooting Route
|--------------------------------------------------------------------------
*/

Route::get('/debug-mail', function() {
    $host = config('mail.mailers.smtp.host');
    $port = config('mail.mailers.smtp.port');
    
    $diagnostics = [
        'laravel_configs' => [
            'mailer'     => config('mail.default'),
            'host'       => $host,
            'port'       => $port,
            'username'   => config('mail.mailers.smtp.username'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'from'       => config('mail.from'),
        ],
        'system_checks' => [
            'openssl_loaded' => extension_loaded('openssl'),
        ],
        'network_test' => []
    ];

    // Attempt a socket handshake with a strict 5-second timeout threshold
    if ($host && $port) {
        $connection = @fsockopen($host, $port, $errno, $errstr, 5);
        if (is_resource($connection)) {
            $diagnostics['network_test'] = [
                'status'  => '✅ SUCCESS',
                'message' => "Physical outbound network path to {$host}:{$port} is wide open."
            ];
            fclose($connection);
        } else {
            $diagnostics['network_test'] = [
                'status'        => '❌ BLOCKED / TIMEOUT',
                'error_number'  => $errno,
                'error_message' => $errstr,
                'suggestion'    => "Render infrastructure is dropping outgoing traffic on port {$port}. Try switching your environment parameters to port 465 (with SSL) or port 587 (with TLS)."
            ];
        }
    } else {
        $diagnostics['network_test'] = [
            'status'  => '❌ INVALID CONFIGS',
            'message' => 'Host or Port settings are completely missing from environment readings.'
        ];
    }

    return $diagnostics;
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';