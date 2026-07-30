<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ClearanceCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function download(ClearanceCertificate $certificate)
    {
        $student = Auth::user()->student;

        abort_unless(
            $certificate->application->student_id === $student->id,
            403,
            'You are not authorised to download this certificate.'
        );

        // Regenerate if PDF doesn't exist yet (forces QR code inclusion)
        if ($certificate->pdf_path && Storage::disk('local')->exists($certificate->pdf_path)) {
            return Storage::disk('local')->download(
                $certificate->pdf_path,
                'Maseno-Clearance-' . $certificate->certificate_number . '.pdf'
            );
        }

        return $this->generateAndDownload($certificate);
    }

    private function generateAndDownload(ClearanceCertificate $certificate)
    {
        $certificate->load([
            'application.student',
            'application.departmentClearances.department',
        ]);

        $application = $certificate->application;
        $student     = $application->student;

        $pdf = Pdf::loadView('certificates.clearance', [
            'certificate' => $certificate,
            'application' => $application,
            'student'     => $student,
        ])
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'defaultFont'          => 'serif',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'dpi'                  => 150,
        ]);

        // Save PDF to storage
        $path = 'certificates/' . $certificate->certificate_number . '.pdf';
        Storage::disk('local')->put($path, $pdf->output());
        $certificate->update(['pdf_path' => $path]);

        return $pdf->download('Maseno-Clearance-' . $certificate->certificate_number . '.pdf');
    }
}