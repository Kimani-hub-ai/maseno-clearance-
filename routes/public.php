<?php

use App\Http\Controllers\Public\CertificateVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Certificate Verification Routes
|--------------------------------------------------------------------------
| No authentication required — anyone (e.g. an employer) can verify
| a certificate either by scanning the QR code (token-based) or by
| manually typing the certificate number.
*/

Route::prefix('verify')->name('public.certificate.')->group(function () {
    Route::get('/', [CertificateVerificationController::class, 'lookup'])->name('lookup');
    Route::post('/', [CertificateVerificationController::class, 'search'])->name('search');
    Route::get('/{token}', [CertificateVerificationController::class, 'verify'])->name('verify');
});
