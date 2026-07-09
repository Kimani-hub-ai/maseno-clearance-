<?php

namespace App\Services;

use App\Enums\ClearanceStatus;
use App\Enums\DepartmentClearanceStatus;
use App\Models\AuditLog;
use App\Models\ClearanceApplication;
use App\Models\ClearanceCertificate;
use App\Models\Department;
use App\Models\DepartmentClearance;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ClearanceUpdateNotification; // 🌟 Added Notification Class
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClearanceService
{
    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    public function hasActiveApplication(Student $student, string $academicYear): bool
    {
        return $student->clearanceApplications()
            ->where('academic_year', $academicYear)
            ->where('status', '!=', ClearanceStatus::Rejected->value)
            ->exists();
    }

    public function createApplication(
        Student $student,
        string $academicYear,
        string $applicationType = 'graduation',
        ?string $remarks = null
    ): ClearanceApplication {
        return DB::transaction(function () use ($student, $academicYear, $applicationType, $remarks) {

            $application = ClearanceApplication::create([
                'student_id'       => $student->id,
                'academic_year'    => $academicYear,
                'application_type' => $applicationType,
                'status'           => ClearanceStatus::Pending->value,
                'remarks'          => $remarks,
                'submitted_at'     => now(),
            ]);

            $departments = Department::where('is_active', true)->get();

            foreach ($departments as $department) {
                DepartmentClearance::create([
                    'application_id' => $application->id,
                    'department_id'  => $department->id,
                    'status'         => DepartmentClearanceStatus::Pending->value,
                ]);
            }

            $this->notify(
                $student->user,
                'application_submitted',
                'Application Submitted Successfully',
                "Your application for the Academic Year {$academicYear} has been captured. It has been routed to " .
                $departments->count() . ' department(s) for verification review.'
            );

            $this->audit(
                $student->user,
                'application_submitted',
                ClearanceApplication::class,
                $application->id,
                null,
                ['status' => ClearanceStatus::Pending->value, 'academic_year' => $academicYear]
            );

            return $application;
        });
    }

    /**
     * Department officer approves or rejects their checkpoint.
     * When ALL departments approve → status becomes 'awaiting_registrar'
     * Certificate is NOT issued here — registrar must sign off first.
     */
    public function reviewDepartmentCheckpoint(
        DepartmentClearance $checkpoint,
        string $action,
        User $reviewer,
        ?string $remarks = null
    ): void {
        DB::transaction(function () use ($checkpoint, $action, $reviewer, $remarks) {

            $oldStatus      = $this->statusValue($checkpoint->status);
            $newStatusValue = $action === 'approve'
                ? DepartmentClearanceStatus::Approved->value
                : DepartmentClearanceStatus::Rejected->value;

            $checkpoint->update([
                'status'      => $newStatusValue,
                'reviewed_by' => $reviewer->id,
                'remarks'     => $remarks,
                'reviewed_at' => now(),
            ]);

            $this->audit(
                $reviewer,
                "department_{$action}d",
                DepartmentClearance::class,
                $checkpoint->id,
                ['status' => $oldStatus],
                ['status' => $newStatusValue, 'remarks' => $remarks]
            );

            $application = $checkpoint->clearanceApplication()
                ->with('student.user')
                ->first();

            $student  = $application->student;
            $deptName = $checkpoint->department->name;

            if ($action === 'approve') {
                $this->notify(
                    $student->user,
                    'department_approved',
                    "Department Cleared: {$deptName}",
                    "Good news! The {$deptName} desk has verified your records and approved your status."
                );
            } else {
                $this->notify(
                    $student->user,
                    'department_rejected',
                    "Attention Required: Clearance Flagged by {$deptName}",
                    "Your clearance application was flagged by the {$deptName} department. Reason: " . ($remarks ?? 'No explicit reason provided.')
                );
            }

            // Check if all departments are done — move to awaiting_registrar if so
            $this->evaluateApplicationCompletion($application);
        });
    }

    /**
     * REGISTRAR FINAL APPROVAL
     * Called when the registrar clicks "Approve & Issue Certificate".
     * This is the ONLY place certificates are generated.
     */
    public function registrarApprove(ClearanceApplication $application, User $registrar): ClearanceCertificate
    {
        return DB::transaction(function () use ($application, $registrar) {

            // Mark as fully approved
            $application->update([
                'status'       => ClearanceStatus::Approved->value,
                'completed_at' => now(),
            ]);

            $this->audit(
                $registrar,
                'registrar_approved',
                ClearanceApplication::class,
                $application->id,
                ['status' => ClearanceStatus::AwaitingRegistrar->value],
                ['status' => ClearanceStatus::Approved->value]
            );

            // Now issue the certificate
            return $this->issueCertificate($application);
        });
    }

    /**
     * REGISTRAR REJECTION
     * Called when the registrar rejects an application even after all depts approved.
     */
    public function registrarReject(ClearanceApplication $application, User $registrar, string $remarks): void
    {
        DB::transaction(function () use ($application, $registrar, $remarks) {

            $application->update([
                'status'  => ClearanceStatus::Rejected->value,
                'remarks' => $remarks,
            ]);

            $this->audit(
                $registrar,
                'registrar_rejected',
                ClearanceApplication::class,
                $application->id,
                ['status' => ClearanceStatus::AwaitingRegistrar->value],
                ['status' => ClearanceStatus::Rejected->value, 'remarks' => $remarks]
            );

            $this->notify(
                $application->student->user,
                'application_rejected',
                'Clearance Application Rejected by Registrar',
                "Your application was rejected during the final Registrar review. Reason provided: {$remarks}"
            );
        });
    }

    /**
     * Issue a certificate — only called by registrarApprove().
     */
    public function issueCertificate(ClearanceApplication $application): ClearanceCertificate
    {
        // Guard: don't issue twice
        if ($application->certificate) {
            return $application->certificate;
        }

        $student           = $application->student;
        $certificateNumber = $this->generateCertificateNumber();
        $verificationToken = Str::uuid()->toString();

        $certificate = ClearanceCertificate::create([
            'application_id'     => $application->id,
            'certificate_number' => $certificateNumber,
            'verification_token' => $verificationToken,
            'issued_at'          => now(),
        ]);

        $this->notify(
            $student->user,
            'certificate_issued',
            '🎓 Official University Clearance Approved!',
            "Congratulations! Your final university clearance has been approved by the Academic Registrar. Your official clearance certificate has been generated safely. Certificate Number: {$certificateNumber}."
        );

        $this->audit(
            $student->user,
            'certificate_issued',
            ClearanceCertificate::class,
            $certificate->id,
            null,
            ['certificate_number' => $certificateNumber]
        );

        return $certificate;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * After each department review, check if ALL departments have approved.
     * If yes → move to 'awaiting_registrar' (NOT approved, NOT certificate yet).
     * If any rejected → mark whole application rejected.
     */
    private function evaluateApplicationCompletion(ClearanceApplication $application): void
    {
        $application->refresh();
        $application->load('departmentClearances', 'student.user');

        $clearances    = $application->departmentClearances;
        $total         = $clearances->count();
        $approvedCount = $clearances->filter(fn($c) => $this->statusValue($c->status) === 'approved')->count();
        $rejectedCount = $clearances->filter(fn($c) => $this->statusValue($c->status) === 'rejected')->count();

        if ($rejectedCount > 0) {
            $application->update(['status' => ClearanceStatus::Rejected->value]);

            $this->notify(
                $application->student->user,
                'application_rejected',
                'Clearance Application Flagged',
                'Your clearance request has been rejected or flagged by one or more structural departments. Please check your logs, resolve outstanding entries, and re-submit.'
            );

        } elseif ($approvedCount === $total && $total > 0) {
            // All departments cleared — now waiting for Registrar sign-off
            $application->update([
                'status' => ClearanceStatus::AwaitingRegistrar->value,
            ]);

            // Notify the student their application is with the Registrar
            $this->notify(
                $application->student->user,
                'awaiting_registrar',
                'Application Forwarded to Registrar',
                'Congratulations! All structural university departments have cleared your application. It has been automatically forwarded to the Academic Registrar for final sign-off.'
            );
        }
    }

    private function statusValue(mixed $status): string
    {
        if ($status instanceof DepartmentClearanceStatus) return $status->value;
        if ($status instanceof ClearanceStatus) return $status->value;
        return (string) $status;
    }

    private function generateCertificateNumber(): string
    {
        $year  = now()->year;
        $count = ClearanceCertificate::whereYear('issued_at', $year)->count() + 1;
        return sprintf('MAS-CLR-%d-%05d', $year, $count);
    }

    /**
     * Reusable System Notification Center
     * 🌟 AUTOMATIC SMTP ROUTER INCLUDED
     */
    private function notify(User $user, string $type, string $title, string $message): void
    {
        // 1. Write the fallback In-App dashboard entry row
        Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'channel' => 'in_app',
            'is_read' => false,
        ]);

        // 2. 🌟 FIRE THE REAL-TIME BACKGROUND SMTP QUEUED MAIL HANDLER
        try {
            $user->notify(new ClearanceUpdateNotification([
                'subject'  => $title,
                'greeting' => "Hello {$user->name},",
                'lines'    => [
                    $message,
                    "Log into your system dashboard portal terminal to track detailed real-time verification logs."
                ]
            ]));
        } catch (\Exception $e) {
            // Failsafe catch so if local network drops during testing, database actions still process cleanly.
            logger()->error("SMTP Outbound Failed: " . $e->getMessage());
        }
    }

    private function audit(
        User $user,
        string $action,
        string $modelType,
        int $modelId,
        ?array $oldValues,
        ?array $newValues
    ): void {
        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => $action,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
        ]);
    }
}