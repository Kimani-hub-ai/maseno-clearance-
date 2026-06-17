<?php

use App\Http\Controllers\Student\ClearanceController;
use App\Http\Controllers\Student\CertificateController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        Route::get('/dashboard', [ClearanceController::class, 'index'])->name('dashboard');

        Route::prefix('clearance')->name('clearance.')->group(function () {
            Route::get('/', [ClearanceController::class, 'index'])->name('index');
            Route::get('/apply', [ClearanceController::class, 'create'])->name('create');
            Route::post('/apply', [ClearanceController::class, 'store'])->name('store');
            Route::post('/documents', [ClearanceController::class, 'uploadDocument'])->name('documents.upload');
            Route::get('/documents/{document}/download', [ClearanceController::class, 'downloadDocument'])->name('documents.download');
        });

        Route::get('/certificate/{certificate}/download', [CertificateController::class, 'download'])
            ->name('certificate.download');
    });
