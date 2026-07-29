<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClearanceCertificate;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    /**
     * Show the public certificate lookup form.
     */
    public function index()
    {
        return view('public.certificate-lookup');
    }

    /**
     * Handle certificate number / token search form submission.
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3|max:100',
        ]);

        $query = trim($request->query);

        // Search by certificate number OR verification token
        $certificate = ClearanceCertificate::where('certificate_number', $query)
            ->orWhere('verification_token', $query)
            ->first();

        if (!$certificate) {
            return back()
                ->withInput()
                ->withErrors(['query' => 'No certificate found matching that number or token. Please check and try again.']);
        }

        // Redirect to the token-based verification URL
        return redirect()->route('public.certificate.verify', $certificate->verification_token);
    }

    /**
     * Verify a certificate by its unique token (also used by QR code).
     * This is what the QR code on the PDF links to.
     */
    public function verify(string $token)
    {
        $certificate = ClearanceCertificate::where('verification_token', $token)
            ->with([
                'application.student',
                'application.departmentClearances.department',
            ])
            ->first();

        if (!$certificate) {
            return view('public.certificate-verify', [
                'valid'       => false,
                'certificate' => null,
                'student'     => null,
                'application' => null,
            ]);
        }

        return view('public.certificate-verify', [
            'valid'       => true,
            'certificate' => $certificate,
            'student'     => $certificate->application->student,
            'application' => $certificate->application,
        ]);
    }
}