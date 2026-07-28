<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DepartmentClearance;
use App\Services\ClearanceService;
use App\Enums\DepartmentClearanceStatus;
use Illuminate\Http\Request;

class DepartmentClearanceController extends Controller
{
    public function __construct(protected ClearanceService $clearanceService) {}

    public function index(Request $request)
    {
        $officer      = $request->user();
        $departmentId = $officer->departmentOfficer?->department_id;

        if (!$departmentId) {
            return redirect()->back()
                ->with('error', 'Your account has no department assigned. Please contact the administrator.');
        }

        // Active tab — default to pending
        $tab = in_array($request->tab, ['pending', 'approved', 'rejected'])
            ? $request->tab
            : 'pending';

        // Stats for the tab badges
        $stats = [
            'pending'  => DepartmentClearance::where('department_id', $departmentId)
                ->where('status', DepartmentClearanceStatus::Pending->value)->count(),
            'approved' => DepartmentClearance::where('department_id', $departmentId)
                ->where('status', DepartmentClearanceStatus::Approved->value)->count(),
            'rejected' => DepartmentClearance::where('department_id', $departmentId)
                ->where('status', DepartmentClearanceStatus::Rejected->value)->count(),
        ];

        // Load clearances for the active tab
        $clearances = DepartmentClearance::with([
            'clearanceApplication.student',
            'clearanceApplication.departmentClearances',
            'department',
            'reviewer',
        ])
        ->where('department_id', $departmentId)
        ->where('status', $tab)
        ->orderBy('created_at', $tab === 'pending' ? 'asc' : 'desc')
        ->paginate(12)
        ->withQueryString();

        return view('dashboards.department', compact(
            'clearances',
            'departmentId',
            'tab',
            'stats',
            'officer'
        ));
    }

    public function review(Request $request, DepartmentClearance $checkpoint)
    {
        $validated = $request->validate([
            'action'  => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Rejection requires a reason
        if ($validated['action'] === 'reject' && empty($validated['remarks'])) {
            return back()->withErrors(['remarks' => 'Please provide a reason for rejection.']);
        }

        $this->clearanceService->reviewDepartmentCheckpoint(
            $checkpoint,
            $validated['action'],
            $request->user(),
            $validated['remarks'] ?? null
        );

        $action = $validated['action'] === 'approve' ? 'approved' : 'rejected';

        return redirect()
            ->route('department.dashboard', ['tab' => $validated['action'] === 'approve' ? 'pending' : 'pending'])
            ->with('success', "Student clearance {$action} successfully.");
    }
}