<?php

namespace App\Services;

use App\Models\ClearanceApplication;
use App\Models\Department;
use App\Models\DepartmentClearance;
use App\Models\Student;
use App\Models\User;
use App\Enums\ClearanceStatus;
use App\Enums\DepartmentClearanceStatus;
use Illuminate\Support\Facades\DB;

class ClearanceService
{
    /**
     * Dependency-inject your partner's existing services.
     */
    public function __construct(
        protected CertificateService $certificateService,
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Phase 1: Create a new clearance application for a student and
     * auto-generate a pending department_clearances row for
     * every active department.
     */
    public function createApplication(Student $student, string $academicYear): ClearanceApplication
    {
        return DB::transaction(function () use ($student, $academicYear) {
            $application = ClearanceApplication::create([
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'status' => ClearanceStatus::Submitted,
                'submitted_at' => now(),
            ]);

            $departments = Department::where('is_active', true)->get();

            foreach ($departments as $department) {
                $application->departmentClearances()->create([
                    'department_id' => $department->id,
                    'status' => DepartmentClearanceStatus::Pending,
                ]);
            }

            $application->update(['status' => ClearanceStatus::InProgress]);

            return $application;
        });
    }

    /**
     * Phase 2: Process an individual department officer's review decision.
     * Updates the checkpoint status, logs the auditing staff officer, 
     * triggers custom real-time student notifications, and refreshes the application.
     */
    public function reviewDepartmentCheckpoint(DepartmentClearance $checkpoint, string $action, User $officer, ?string $remarks = null): DepartmentClearance
    {
        return DB::transaction(function () use ($checkpoint, $action, $officer, $remarks) {
            // 1. Evaluate incoming string actions safely into framework Enums
            $newStatus = ($action === 'approve') 
                ? DepartmentClearanceStatus::Approved 
                : DepartmentClearanceStatus::Rejected;

            // 2. Persist the state update onto the specific checkpoint ledger line
            $checkpoint->update([
                'status' => $newStatus,
                'remarks' => $remarks,
                'officer_id' => $officer->id,
                'reviewed_at' => now(),
            ]);

            // 3. Pull relational profiles for automated transaction notifications
            $studentUser = $checkpoint->clearanceApplication->student->user;
            $departmentName = $checkpoint->department->name;

            // 4. Dispatch the corresponding real-time notification alerts
            if ($newStatus === DepartmentClearanceStatus::Approved) {
                $this->notificationService->notifyDepartmentApproved($studentUser, $departmentName);
            } else {
                $this->notificationService->notifyDepartmentRejected($studentUser, $departmentName, $remarks);
            }

            // 5. Force a cascade refresh across the primary system tracking hub
            $this->refreshApplicationStatus($checkpoint->clearanceApplication);

            return $checkpoint;
        });
    }

    /**
     * Phase 3: Re-evaluate an application's overall status based on its
     * department_clearances rows. Call this after every officer
     * approve/reject action.
     *
     * When the application becomes fully cleared, this automatically
     * triggers certificate generation and notifies the student.
     */
    public function refreshApplicationStatus(ClearanceApplication $application): void
    {
        if ($application->hasRejection()) {
            $application->update(['status' => ClearanceStatus::Rejected]);
            return;
        }

        if ($application->isFullyCleared()) {
            $wasAlreadyCleared = $application->status === ClearanceStatus::Cleared;

            $application->update([
                'status' => ClearanceStatus::Cleared,
                'completed_at' => $application->completed_at ?? now(),
            ]);

            // Only generate the certificate and notify once, the first
            // time the application transitions into Cleared.
            if (!$wasAlreadyCleared) {
                $certificate = $this->certificateService->generateForApplication($application);

                $this->notificationService->notifyCertificateReady(
                    $application->student->user
                );
            }

            return;
        }

        $application->update(['status' => ClearanceStatus::InProgress]);
    }

    /**
     * Check whether a student already has an active (non-terminal)
     * application for the given academic year.
     */
    public function hasActiveApplication(Student $student, string $academicYear): bool
    {
        return $student->clearanceApplications()
            ->where('academic_year', $academicYear)
            ->whereIn('status', ['submitted', 'in_progress'])
            ->exists();
    }
}