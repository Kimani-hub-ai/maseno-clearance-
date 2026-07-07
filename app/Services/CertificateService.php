<?php

namespace App\Services;

use App\Models\ClearanceApplication;
use App\Models\ClearanceCertificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateService
{
    /**
     * Generate a clearance certificate for a fully-cleared application.
     * Creates the certificate record, the QR code image, and the PDF file.
     * Safe to call multiple times — returns the existing certificate if one
     * already exists for this application.
     */
    public function generateForApplication(ClearanceApplication $application): ClearanceCertificate
    {
        // Idempotency guard — don't generate twice
        if ($application->certificate) {
            return $application->certificate;
        }

        return DB::transaction(function () use ($application) {
            $certificateNumber = $this->generateCertificateNumber();
            $verificationToken = (string) Str::uuid();

            $certificate = ClearanceCertificate::create([
                'application_id' => $application->id,
                'certificate_number' => $certificateNumber,
                'verification_token' => $verificationToken,
                'issued_at' => now(),
            ]);

            $qrPath = $this->generateQrCode($certificate);
            $pdfPath = $this->generatePdf($application, $certificate, $qrPath);

            $certificate->update([
                'qr_code_path' => $qrPath,
                'pdf_path' => $pdfPath,
            ]);

            return $certificate;
        });
    }

    /**
     * Certificate number format: MAS-CLR-{YEAR}-{5-digit sequential number}
     * e.g. MAS-CLR-2026-00001
     * Sequence resets per calendar year based on count of certificates
     * already issued that year.
     */
    protected function generateCertificateNumber(): string
    {
        $year = now()->year;

        $countThisYear = ClearanceCertificate::whereYear('issued_at', $year)->count();

        // If issued_at hasn't been set yet for the current record (it's set
        // at create time above), this still works since we count *before*
        // creating the current row.
        $sequence = str_pad((string) ($countThisYear + 1), 5, '0', STR_PAD_LEFT);

        $candidate = "MAS-CLR-{$year}-{$sequence}";

        // Safety net in case of race conditions / gaps — ensure uniqueness
        while (ClearanceCertificate::where('certificate_number', $candidate)->exists()) {
            $countThisYear++;
            $sequence = str_pad((string) ($countThisYear + 1), 5, '0', STR_PAD_LEFT);
            $candidate = "MAS-CLR-{$year}-{$sequence}";
        }

        return $candidate;
    }

    /**
     * Generate a QR code image pointing to the public verification page,
     * store it on the local disk, and return the storage path.
     */
    protected function generateQrCode(ClearanceCertificate $certificate): string
    {
        $verificationUrl = route('public.certificate.verify', $certificate->verification_token);

        // Render the QR code directly to PNG using GD, bypassing both
        // Imagick and the simplesoftwareio wrapper's backend auto-detection
        // (which can require Imagick even when GD is available and preferred).
        $pngBinary = $this->renderQrPngWithGd($verificationUrl);

        $path = 'certificates/qr/' . $certificate->certificate_number . '.png';
        \Storage::disk('local')->put($path, $pngBinary);

        return $path;
    }

    /**
     * Render a QR code straight to PNG bytes using bacon/bacon-qr-code's
     * matrix output plus PHP's built-in GD functions. This sidesteps both
     * Imagick and the simplesoftwareio wrapper's backend auto-detection,
     * which can incorrectly require Imagick even when GD is available.
     */
    protected function renderQrPngWithGd(string $data): string
    {
        $qrCode = \BaconQrCode\Encoder\Encoder::encode(
            $data,
            \BaconQrCode\Common\ErrorCorrectionLevel::M()
        );

        $matrix = $qrCode->getMatrix();
        $moduleCount = $matrix->getWidth();
        $scale = 8; // pixels per module
        $imageSize = $moduleCount * $scale;

        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefilledrectangle($image, 0, 0, $imageSize, $imageSize, $white);

        for ($y = 0; $y < $moduleCount; $y++) {
            for ($x = 0; $x < $moduleCount; $x++) {
                if ($matrix->get($x, $y) === 1) {
                    imagefilledrectangle(
                        $image,
                        $x * $scale,
                        $y * $scale,
                        ($x + 1) * $scale - 1,
                        ($y + 1) * $scale - 1,
                        $black
                    );
                }
            }
        }

        ob_start();
        imagepng($image);
        $binary = ob_get_clean();
        imagedestroy($image);

        return $binary;
    }

    /**
     * Render the certificate Blade view to PDF and store it.
     */
    protected function generatePdf(ClearanceApplication $application, ClearanceCertificate $certificate, string $qrPath): string
    {
        $student = $application->student;
        $qrBinary = \Storage::disk('local')->get($qrPath);
        $qrDataUri = 'data:image/png;base64,' . base64_encode($qrBinary);

        $pdf = Pdf::loadView('certificates.clearance', [
            'application' => $application,
            'certificate' => $certificate,
            'student' => $student,
            'qrDataUri' => $qrDataUri,
        ]);

        $path = 'certificates/pdf/' . $certificate->certificate_number . '.pdf';
        \Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Registrar manually issues a certificate for a fully-cleared application.
     * Used as an override/fallback from the registrar dashboard.
     */
    public function issueCertificate(ClearanceApplication $application): ClearanceCertificate
    {
    // Force status to cleared so certificate generation proceeds
    if ($application->status !== \App\Enums\ClearanceStatus::Cleared) {
        $application->update([
            'status'       => \App\Enums\ClearanceStatus::Cleared,
            'completed_at' => now(),
        ]);
    }
    $certificate = $this->generateForApplication($application);

    if (property_exists($this, 'notificationService') && $this->notificationService) {
        $this->notificationService->notifyCertificateReady(
            $application->student->user
        );
    }

    return $certificate;
}

}