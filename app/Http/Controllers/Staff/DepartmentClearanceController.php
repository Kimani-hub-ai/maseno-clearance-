<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DepartmentClearance;
use App\Services\ClearanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartmentClearanceController extends Controller
{
    public function __construct(
        protected ClearanceService $clearanceService
    ) {}

    /**
     * Process an officer's review decision (Approve or Reject).
     * 
     * POST /api/staff/clearances/{checkpoint}/review
     */
    public function review(Request $request, DepartmentClearance $checkpoint): JsonResponse
    {
        // 1. Validate the incoming request payload
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            // 2. Fetch the authenticated officer user from the request context
            $officer = $request->user();

            // 3. Hand off execution to your new ClearanceService method
            $updatedCheckpoint = $this->clearanceService->reviewDepartmentCheckpoint(
                $checkpoint,
                $validated['action'],
                $officer,
                $validated['remarks'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => "Department checkpoint successfully " . $validated['action'] . "d.",
                'data' => [
                    'checkpoint_id' => $updatedCheckpoint->id,
                    'status' => $updatedCheckpoint->status,
                    'remarks' => $updatedCheckpoint->remarks,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the review.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}