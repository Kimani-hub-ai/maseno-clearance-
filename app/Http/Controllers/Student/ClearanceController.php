<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User; // 🌟 Added User model for notification targeting
use App\Notifications\ClearanceUpdateNotification; // 🌟 Added Notification Class
use App\Services\ClearanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClearanceController extends Controller
{
    public function __construct(protected ClearanceService $clearanceService) {}

    /**
     * Student dashboard / application status page.
     */
    public function index(): View
    {
        $student = Auth::user()->student;

        $application = $student->clearanceApplications()
            ->with(['departmentClearances.department', 'documents', 'certificate'])
            ->latest('created_at')
            ->first();

        return view('student.clearance.index', [
            'student'     => $student,
            'application' => $application,
        ]);
    }

    /**
     * Show the application form.
     */
    public function create(): View|RedirectResponse
    {
        $student      = Auth::user()->student;
        $academicYear = $this->currentAcademicYear();

        if ($this->clearanceService->hasActiveApplication($student, $academicYear)) {
            return redirect()->route('student.clearance.index')
                ->with('info', 'You already have an active application for this academic year.');
        }

        return view('student.clearance.create', [
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Submit a new application.
     * Accepts full_name, application_type, and optional remarks from the form.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name'        => 'required|string|max:255',
            'application_type' => 'required|in:graduation,deferral,transfer,withdrawal,other',
            'remarks'          => 'nullable|string|max:1000',
        ]);

        $user         = Auth::user(); // 🌟 Grab user instance for notification
        $student      = $user->student;
        $academicYear = $this->currentAcademicYear();

        if ($this->clearanceService->hasActiveApplication($student, $academicYear)) {
            return redirect()->route('student.clearance.index')
                ->with('info', 'You already have an active application for this academic year.');
        }

        // Update student's full_name if they corrected it on the form
        if ($student->full_name !== $validated['full_name']) {
            $student->update(['full_name' => $validated['full_name']]);
        }

        // Create the application via the service wrapper
        $application = $this->clearanceService->createApplication(
            $student,
            $academicYear,
            $validated['application_type'],
            $validated['remarks'] ?? null
        );

        // ═════════════════════════════════════════════════════════════════
        // 🌟 EMAIL TRIGGER: CONFIRMATION TO STUDENT
        // ═════════════════════════════════════════════════════════════════
        $user->notify(new ClearanceUpdateNotification([
            'subject'  => 'Clearance Application Submitted Successfully',
            'greeting' => "Hello {$validated['full_name']},",
            'lines'    => [
                "Your university clearance application for the Academic Year {$academicYear} has been captured successfully.",
                "Tracking Reference ID: #" . ($application->id ?? 'Pending'),
                "Status: Sent to all departmental desks for verification review.",
            ]
        ]));

        // ═════════════════════════════════════════════════════════════════
        // 🌟 EMAIL TRIGGER: NOTIFY ALL ASSIGNED DEPARTMENT DESK OFFICERS
        // ═════════════════════════════════════════════════════════════════
        // Grabs all system users assigned the 'officer' or 'staff' role
        $officers = User::where('role', 'officer')->get();
        
        foreach ($officers as $officer) {
            $officer->notify(new ClearanceUpdateNotification([
                'subject'  => 'Action Required: New Clearance Assignment',
                'greeting' => "Hello Officer,",
                'lines'    => [
                    "A new clearance workflow has been initiated by student: {$validated['full_name']}.",
                    "Please log into your administrative dashboard to review their status records.",
                ]
            ]));
        }

        return redirect()->route('student.clearance.index')
            ->with('success', 'Your application has been submitted to all departments for review.');
    }

    /**
     * Upload a supporting document.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $request->validate([
            'document'      => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $student = Auth::user()->student;

        $application = $student->clearanceApplications()
            ->whereIn('status', ['pending', 'approved'])
            ->latest('created_at')
            ->first();

        if (!$application) {
            return back()->with('error', 'No active application found. Please submit an application first.');
        }

        $file = $request->file('document');
        $path = $file->store('clearance-documents/' . $application->id, 'local');

        Document::create([
            'application_id' => $application->id,
            'department_id'  => $request->department_id,
            'original_name'  => $file->getClientOriginalName(),
            'stored_path'    => $path,
            'mime_type'      => $file->getClientMimeType(),
            'file_size'      => $file->getSize(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Download an uploaded document (student can only access their own).
     */
    public function downloadDocument(Document $document)
    {
        $student = Auth::user()->student;

        abort_unless($document->application->student_id === $student->id, 403);

        return Storage::disk('local')->download($document->stored_path, $document->original_name);
    }

    /**
     * Current academic year string, e.g. "2025/2026".
     */
    protected function currentAcademicYear(): string
    {
        $now  = now();
        $year = $now->month >= 9 ? $now->year : $now->year - 1;
        return $year . '/' . ($year + 1);
    }
}