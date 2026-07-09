<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClearanceCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Download the clearance certificate as a PDF.
     * If the PDF hasn't been generated yet, generate it first,
     * save it to storage, update the record, then stream it.
     */
    public function download(ClearanceCertificate $certificate)
    {
        $student = Auth::user()->student;

        // Security: student can only download their own certificate
        abort_unless(
            $certificate->application->student_id === $student->id,
            403,
            'You are not authorised to download this certificate.'
        );

        // If PDF already exists on disk, stream it directly
        if ($certificate->pdf_path && Storage::disk('local')->exists($certificate->pdf_path)) {
            return Storage::disk('local')->download(
                $certificate->pdf_path,
                'clearance-certificate-' . $certificate->certificate_number . '.pdf'
            );
        }

        // Generate the PDF fresh
        return $this->generateAndDownload($certificate);
    }

    /**
     * Registrar can also download/preview any certificate.
     */
    public function registrarDownload(ClearanceCertificate $certificate)
    {
        return $this->generateAndDownload($certificate);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function generateAndDownload(ClearanceCertificate $certificate): \Symfony\Component\HttpFoundation\Response
    {
        // Eager load everything the template needs
        $certificate->load([
            'application.student',
            'application.departmentClearances.department',
        ]);

        $application = $certificate->application;
        $student     = $application->student;

        // Generate PDF from the certificate blade template
        $pdf = Pdf::loadView('certificates.clearance', [
            'certificate' => $certificate,
            'application' => $application,
            'student'     => $student,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont'    => 'serif',
            'isRemoteEnabled'=> false,
            'isHtml5ParserEnabled' => true,
            'dpi'            => 150,
        ]);

        // Save to storage so future downloads skip regeneration
        $path = 'certificates/' . $certificate->certificate_number . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        // Update the record with the saved path
        $certificate->update(['pdf_path' => $path]);

        $filename = 'Maseno-Clearance-' . $certificate->certificate_number . '.pdf';

        return $pdf->download($filename);
    }
}