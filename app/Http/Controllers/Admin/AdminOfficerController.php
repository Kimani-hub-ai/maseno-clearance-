<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentOfficer;
use App\Models\User;
use App\Models\Student;
use App\Models\ClearanceApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminOfficerController extends Controller
{
    /**
     * Admin dashboard — system overview + officer management.
     */
    public function index()
    {
        $stats = [
            'total_students'     => Student::count(),
            'total_officers'     => User::where('role', 'officer')->count(),
            'total_applications' => ClearanceApplication::count(),
            'cleared'            => ClearanceApplication::where('status', 'cleared')->count(),
        ];

        $officers = User::where('role', 'officer')
            ->with('departmentOfficer.department')
            ->orderBy('name')
            ->get();

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('dashboards.admin', compact('stats', 'officers', 'departments'));
    }

    /**
     * Create a new department officer account and assign them to a department.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'department_id' => 'required|exists:departments,id',
            'password'      => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'officer',
            'is_active' => true,
        ]);

        DepartmentOfficer::create([
            'user_id'       => $user->id,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', "Officer account created for {$user->name}.");
    }
}
