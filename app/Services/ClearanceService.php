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
                'Application Submitted',
                "Your application for {$academicYear} has been submitted to " .
                $departments->count() . ' department(s) for review.'
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
                    "{$deptName} Cleared",
                    "The {$deptName} department has approved your application."
                );
            } else {
                $this->notify(
                    $student->user,
                    'department_rejected',
                    "{$deptName} Rejected",
                    "The {$deptName} department has rejected your application. " .
                    'Reason: ' . ($remarks ?? 'No reason provided.')
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
                'Application Rejected by Registrar',
                'Your application was rejected by the Academic Registrar. ' .
                'Reason: ' . $remarks . ' Please contact the Registrar\'s office for more information.'
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
            'Certificate Ready 🎉',
            "Congratulations! Your clearance certificate ({$certificateNumber}) " .
            'has been issued and approved by the Registrar. You can now download it from your dashboard.'
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
                'Application Rejected',
                'Your application has been rejected by one or more departments. ' .
                'Please resolve any outstanding issues and reapply.'
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
                'Awaiting Registrar Approval',
                'All departments have cleared your application. ' .
                'It has been forwarded to the Academic Registrar for final approval.'
            );
        }
        // Otherwise still pending — some departments haven't reviewed yet
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

    private function notify(User $user, string $type, string $title, string $message): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'channel' => 'in_app',
            'is_read' => false,
        ]);
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