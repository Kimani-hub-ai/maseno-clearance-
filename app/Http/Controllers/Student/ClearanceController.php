<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\ClearanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClearanceController extends Controller
{
    public function __construct(protected ClearanceService $clearanceService)
    {
    }

    /**
     * Show the student's dashboard: current application status,
     * or a prompt to apply if none exists yet.
     */
    public function index(): View
    {
        $student = Auth::user()->student;

        $application = $student->clearanceApplications()
            ->with(['departmentClearances.department', 'documents', 'certificate'])
            ->latest('created_at')
            ->first();

        return view('student.clearance.index', [
            'student' => $student,
            'application' => $application,
        ]);
    }

    /**
     * Show the "apply for clearance" form.
     */
    public function create(): View|RedirectResponse
    {
        $student = Auth::user()->student;
        $academicYear = $this->currentAcademicYear();

        if ($this->clearanceService->hasActiveApplication($student, $academicYear)) {
            return redirect()->route('student.clearance.index')
                ->with('info', 'You already have an active clearance application.');
        }

        return view('student.clearance.create', [
            'academicYear' => $academicYear,
        ]);
    }

    /**
     * Submit a new clearance application.
     */
    public function store(Request $request): RedirectResponse
    {
        $student = Auth::user()->student;
        $academicYear = $this->currentAcademicYear();

        if ($this->clearanceService->hasActiveApplication($student, $academicYear)) {
            return redirect()->route('student.clearance.index')
                ->with('info', 'You already have an active clearance application.');
        }

        $this->clearanceService->createApplication($student, $academicYear);

        return redirect()->route('student.clearance.index')
            ->with('success', 'Your clearance application has been submitted to all departments.');
    }

    /**
     * Upload a supporting document for the active application.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $student = Auth::user()->student;

        $application = $student->clearanceApplications()
            ->whereIn('status', ['submitted', 'in_progress'])
            ->latest('created_at')
            ->first();

        if (!$application) {
            return back()->with('error', 'You need an active clearance application before uploading documents.');
        }

        $file = $request->file('document');
        $path = $file->store('clearance-documents/' . $application->id, 'local');

        Document::create([
            'application_id' => $application->id,
            'department_id' => $request->department_id,
            'original_name' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Download/view an uploaded document (student can only access their own).
     */
    public function downloadDocument(Document $document)
    {
        $student = Auth::user()->student;

        abort_unless(
            $document->application->student_id === $student->id,
            403
        );

        return Storage::disk('local')->download($document->stored_path, $document->original_name);
    }

    /**
     * Determine the current academic year string, e.g. "2025/2026".
     * Kenyan academic years typically run Sept-Aug; adjust as needed.
     */
    protected function currentAcademicYear(): string
    {
        $now = now();
        $year = $now->month >= 9 ? $now->year : $now->year - 1;
        return $year . '/' . ($year + 1);
    }
}
