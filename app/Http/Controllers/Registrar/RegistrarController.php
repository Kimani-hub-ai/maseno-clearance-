<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Enums\ClearanceStatus;
use App\Models\ClearanceApplication;
use App\Models\Student;
use App\Services\ClearanceService;
use Illuminate\Http\Request;

class RegistrarController extends Controller
{
    public function __construct(protected ClearanceService $clearanceService) {}

    public function index(Request $request)
    {
        $stats = [
            'total_students'      => Student::count(),
            'total_applications'  => ClearanceApplication::count(),
            'pending'             => ClearanceApplication::where('status', ClearanceStatus::Pending->value)->count(),
            'awaiting_registrar'  => ClearanceApplication::where('status', ClearanceStatus::AwaitingRegistrar->value)->count(),
            'approved'            => ClearanceApplication::where('status', ClearanceStatus::Approved->value)->count(),
            'rejected'            => ClearanceApplication::where('status', ClearanceStatus::Rejected->value)->count(),
        ];

        $query = ClearanceApplication::with(['student', 'departmentClearances', 'certificate']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'awaiting_registrar', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('reg_number', 'like', "%{$search}%");
            });
        }

        // Default: show awaiting_registrar first so they're never missed
        $applications = $query->orderByRaw("FIELD(status, 'awaiting_registrar', 'pending', 'rejected', 'approved')")
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('dashboards.registrar', compact('stats', 'applications'));
    }

    public function show(ClearanceApplication $application)
    {
        $application->load([
            'student',
            'departmentClearances.department',
            'departmentClearances.reviewer',
            'documents.department',
            'certificate',
        ]);

        return view('registrar.show', compact('application'));
    }

    /**
     * Registrar final approval — triggers certificate generation.
     */
    public function approve(ClearanceApplication $application, Request $request)
    {
        if ($application->status->value !== ClearanceStatus::AwaitingRegistrar->value) {
            return back()->with('error', 'This application is not ready for registrar approval.');
        }

        if ($application->certificate) {
            return back()->with('info', 'A certificate has already been issued for this application.');
        }

        $this->clearanceService->registrarApprove($application, $request->user());

        return back()->with('success', 'Application approved and certificate issued successfully.');
    }

    /**
     * Registrar rejects even after all departments approved.
     */
    public function reject(ClearanceApplication $application, Request $request)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        if (!in_array($application->status->value, [
            ClearanceStatus::AwaitingRegistrar->value,
            ClearanceStatus::Pending->value,
        ])) {
            return back()->with('error', 'This application cannot be rejected at this stage.');
        }

        $this->clearanceService->registrarReject($application, $request->user(), $request->remarks);

        return back()->with('success', 'Application has been rejected and the student notified.');
    }
}