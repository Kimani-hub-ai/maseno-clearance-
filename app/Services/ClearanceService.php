<?php

namespace App\Services;

use App\Models\ClearanceApplication;
use App\Models\Department;
use App\Models\Student;
use App\Enums\ClearanceStatus;
use App\Enums\DepartmentClearanceStatus;
use Illuminate\Support\Facades\DB;

class ClearanceService
{
    public function __construct(
        protected CertificateService $certificateService,
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Create a new clearance application for a student and
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
     * Re-evaluate an application's overall status based on its
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
