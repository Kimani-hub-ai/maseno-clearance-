<?php

use App\Http\Controllers\Public\CertificateVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Certificate Verification Routes
| No authentication required — anyone can verify a certificate
|--------------------------------------------------------------------------
*/

Route::prefix('verify')->name('public.')->group(function () {

    // Lookup form
    Route::get('/certificate', [CertificateVerificationController::class, 'index'])
        ->name('certificate.lookup');

    // Form submission — search by number or token
    Route::post('/certificate', [CertificateVerificationController::class, 'search'])
        ->name('certificate.search');

    // Verify by token (used by QR code on PDF)
    Route::get('/certificate/{token}', [CertificateVerificationController::class, 'verify'])
        ->name('certificate.verify');
});