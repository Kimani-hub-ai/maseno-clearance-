<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClearanceCertificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Download the PDF certificate. Students can only access their own.
     */
    public function download(ClearanceCertificate $certificate)
    {
        $student = Auth::user()->student;

        abort_unless(
            $certificate->application->student_id === $student->id,
            403
        );

        abort_unless(
            $certificate->pdf_path && Storage::disk('local')->exists($certificate->pdf_path),
            404,
            'Certificate file not found.'
        );

        return Storage::disk('local')->download(
            $certificate->pdf_path,
            $certificate->certificate_number . '.pdf'
        );
    }
}
