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

    /**
     * Check whether a student already has an active (non-rejected) application
     * for the given academic year.
     */
    public function hasActiveApplication(Student $student, string $academicYear): bool
    {
        return $student->clearanceApplications()
            ->where('academic_year', $academicYear)
            ->where('status', '!=', ClearanceStatus::Rejected->value)
            ->exists();
    }

    /**
     * Create a new clearance application for a student.
     * Fans out one DepartmentClearance row per active department.
     *
     * FIX: Pass enum backing VALUES (raw strings) to ::create(), not enum
     * objects. This prevents MariaDB from receiving a PHP object and also
     * avoids "is not a valid backing value" on the return read.
     */
    public function createApplication(Student $student, string $academicYear): ClearanceApplication
    {
        return DB::transaction(function () use ($student, $academicYear) {

            // 1. Create the master application
            $application = ClearanceApplication::create([
                'student_id'    => $student->id,
                'academic_year' => $academicYear,
                'status'        => ClearanceStatus::Pending->value,
                'submitted_at'  => now(),
            ]);

            // 2. Fan out one checkpoint per active department
            $departments = Department::where('is_active', true)->get();

            foreach ($departments as $department) {
                DepartmentClearance::create([
                    'application_id' => $application->id,
                    'department_id'  => $department->id,
                    'status'         => DepartmentClearanceStatus::Pending->value,
                ]);
            }

            // 3. In-app notification to student
            $this->notify(
                $student->user,
                'application_submitted',
                'Clearance Application Submitted',
                "Your clearance application for {$academicYear} has been submitted to " .
                $departments->count() . ' department(s) for review.'
            );

            // 4. Audit trail
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
     * Process a department officer's approve/reject decision.
     *
     * FIX: $checkpoint->status is cast to DepartmentClearanceStatus enum by
     * the model. We must call ->value to get the raw string for DB writes.
     * We also guard against rows that were inserted via DB::table() and come
     * back as plain strings instead of enum objects.
     */
    public function reviewDepartmentCheckpoint(
        DepartmentClearance $checkpoint,
        string $action,
        User $reviewer,
        ?string $remarks = null
    ): void {
        DB::transaction(function () use ($checkpoint, $action, $reviewer, $remarks) {

            // Safely read old status — handles both enum object and raw string
            $oldStatus = $this->statusValue($checkpoint->status);

            $newStatusValue = $action === 'approve'
                ? DepartmentClearanceStatus::Approved->value
                : DepartmentClearanceStatus::Rejected->value;

            // 1. Update the checkpoint with raw string value
            $checkpoint->update([
                'status'      => $newStatusValue,
                'reviewed_by' => $reviewer->id,
                'remarks'     => $remarks,
                'reviewed_at' => now(),
            ]);

            // 2. Audit
            $this->audit(
                $reviewer,
                "department_{$action}d",
                DepartmentClearance::class,
                $checkpoint->id,
                ['status' => $oldStatus],
                ['status' => $newStatusValue, 'remarks' => $remarks]
            );

            // 3. Notify the student — use aliased relation to avoid
            //    RelationNotFoundException (both 'application' and
            //    'clearanceApplication' are defined on the model)
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
                    "The {$deptName} department has approved your clearance application."
                );
            } else {
                $this->notify(
                    $student->user,
                    'department_rejected',
                    "{$deptName} Rejected",
                    "The {$deptName} department has rejected your clearance. " .
                    'Reason: ' . ($remarks ?? 'No reason provided.') .
                    ' Please resolve this and contact the department.'
                );
            }

            // 4. Re-evaluate overall application status
            $this->evaluateApplicationCompletion($application);
        });
    }

    /**
     * Manually issue a certificate (called by Registrar for edge cases).
     */
    public function issueCertificate(ClearanceApplication $application): ClearanceCertificate
    {
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
            'Clearance Certificate Ready',
            "Congratulations! Your clearance certificate ({$certificateNumber}) has been issued. " .
            'You can now download it from your dashboard.'
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
     * After each department review, re-check the overall application status.
     *
     * FIX: We refresh() the application from DB so stale in-memory enum
     * objects don't cause false reads. We then compare raw string values
     * via statusValue() instead of relying on enum equality.
     */
    private function evaluateApplicationCompletion(ClearanceApplication $application): void
    {
        // Reload fresh from DB — clears any stale casted enum objects
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
                'Clearance Application Rejected',
                'Your clearance application has been rejected by one or more departments. ' .
                'Please resolve any outstanding issues and reapply.'
            );

        } elseif ($approvedCount === $total && $total > 0) {
            $application->update([
                'status'       => ClearanceStatus::Approved->value,
                'completed_at' => now(),
            ]);

            $this->issueCertificate($application);
        }
        // Partial approvals — still pending, nothing to do
    }

    /**
     * Safely extract the string value from a status field.
     * Handles: enum object, raw string (DB::table() inserts), or null.
     */
    private function statusValue(mixed $status): string
    {
        if ($status instanceof DepartmentClearanceStatus) {
            return $status->value;
        }
        if ($status instanceof ClearanceStatus) {
            return $status->value;
        }
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