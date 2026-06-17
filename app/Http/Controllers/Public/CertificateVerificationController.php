<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClearanceCertificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CertificateVerificationController extends Controller
{
    /**
     * Show the public lookup form (no token provided yet).
     */
    public function lookup(): View
    {
        return view('public.certificate-lookup');
    }

    /**
     * Handle the manual certificate-number lookup form submission.
     */
    public function search(Request $request): View
    {
        $request->validate([
            'certificate_number' => 'required|string',
        ]);

        $certificate = ClearanceCertificate::where('certificate_number', $request->certificate_number)
            ->with('application.student')
            ->first();

        return view('public.certificate-lookup', [
            'certificate' => $certificate,
            'searched' => true,
        ]);
    }

    /**
     * Show verification result via QR code token (public, no login).
     */
    public function verify(string $token): View
    {
        $certificate = ClearanceCertificate::where('verification_token', $token)
            ->with('application.student')
            ->first();

        return view('public.certificate-verify', [
            'certificate' => $certificate,
        ]);
    }
}
