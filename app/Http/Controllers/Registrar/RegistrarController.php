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

    /**
     * Registrar dashboard: overview stats + list of all applications.
     */
    public function index(Request $request)
    {
        $stats = [
            'total_students'     => Student::count(),
            'total_applications' => ClearanceApplication::count(),
            // FIX: use ->value to pass raw string to query builder, not enum object
            'pending'            => ClearanceApplication::where('status', ClearanceStatus::Pending->value)->count(),
            'approved'           => ClearanceApplication::where('status', ClearanceStatus::Approved->value)->count(),
            'rejected'           => ClearanceApplication::where('status', ClearanceStatus::Rejected->value)->count(),
        ];

        // Build query with optional filters
        $query = ClearanceApplication::with(['student', 'departmentClearances', 'certificate']);

        // Filter by status (from dashboard tab clicks)
        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Search by student name or reg number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('reg_number', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return view('dashboards.registrar', compact('stats', 'applications'));
    }

    /**
     * Show a single application in full detail.
     */
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
     * Registrar manually overrides and issues a certificate (edge cases).
     */
    public function issueCertificate(ClearanceApplication $application)
    {
        if ($application->certificate) {
            return back()->with('info', 'A certificate has already been issued for this application.');
        }

        $this->clearanceService->issueCertificate($application);

        return back()->with('success', 'Certificate issued successfully.');
    }
}