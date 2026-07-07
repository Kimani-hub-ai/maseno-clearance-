<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DepartmentClearance;
use App\Services\ClearanceService;
use App\Enums\DepartmentClearanceStatus;
use Illuminate\Http\Request;

class DepartmentClearanceController extends Controller
{
    public function __construct(
        protected ClearanceService $clearanceService
    ) {}

    /**
     * Show all pending clearance requests for the logged-in officer's department.
     */
    public function index(Request $request)
    {
        $officer = $request->user();
        $departmentId = $officer->departmentOfficer?->department_id;

        if (!$departmentId) {
            return redirect()->back()->with('error', 'No department assigned to your account. Contact the administrator.');
        }

        $pendingClearances = DepartmentClearance::with(['application.student', 'department'])
            ->where('department_id', $departmentId)
            ->where('status', DepartmentClearanceStatus::Pending)
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('dashboards.department', compact('pendingClearances', 'departmentId'));
    }

    /**
     * Process an officer's approve or reject decision.
     */
    public function review(Request $request, DepartmentClearance $checkpoint)
    {
        $validated = $request->validate([
            'action'  => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:500',
        ]);

        $officer = $request->user();

        $departmentId = $officer->departmentOfficer?->department_id;
        if ($departmentId !== $checkpoint->department_id) {
            abort(403, 'You are not authorized to review this clearance.');
        }

        $this->clearanceService->reviewDepartmentCheckpoint(
            $checkpoint,
            $validated['action'],
            $officer,
            $validated['remarks'] ?? null
        );

        return redirect()->route('department.dashboard')
            ->with('success', 'Clearance ' . $validated['action'] . 'd successfully.');
    }
}
