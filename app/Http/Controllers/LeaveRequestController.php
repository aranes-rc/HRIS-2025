<?php

namespace App\Http\Controllers;

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\ShiftType;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;
        
        return view('leave.create', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        //
    }
    
    /**
     * Display a listing of the leave requests.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'leave_type' => 'sometimes|in:' . implode(',', LeaveType::values()),
            'status' => 'sometimes|in:' . implode(',', LeaveStatus::values()),
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'employee_id' => 'sometimes|exists:employees,id'
        ]);

        // Admin sees all leave requests with full access.
        if ($user->isAdmin()) {
            $leaveRequests = LeaveRequest::with('Employee')
                ->filter($validated)
                ->latest();
        }
        // HR sees all requests so they can approve/reject,
        // but if they need to edit, they only edit their own details (excluding status).
        elseif ($user->isHr()) {
            $leaveRequests = LeaveRequest::with('Employee')
            ->filter($validated)
                ->latest();
        }
        // Employees see only their own leave requests.
        else {
            $leaveRequests = LeaveRequest::where('employee_id', $user->employee->id)
                                         ->with('Employee')
                                         ->filter($validated)
                                         ->latest();
        }

        return view('leave.index', ['leave_requests' => $leaveRequests->paginate(15)]);
    }

    public function store(Request $request)
    {
        if ($request->user()->cannot('create', LeaveRequest::class)) abort(403);
        
        $validationRules = [
            'employee_id' => 'required|exists:employees,id',
            'leave_type' => 'required|in:' . implode(',', LeaveType::values()),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'shift_covered' => 'required|array|max:2|min:1',
            'shift_covered.*' => 'required|string|distinct|in:' . implode(',', ShiftType::values()),
            'proof_of_leader_approval' => 'required|file|mimes:jpeg,jpg,png',
            'proof_of_confirmed_designatory_tasks' => 'required|file|mimes:jpeg,jpg,png',
        ];

        
        // Add proof_of_leave validation only for academic leave type
        if ($request->leave_type === LeaveType::ACADEMIC->value) {
            $validationRules['proof_of_leave'] = 'required|file|mimes:jpeg,jpg,png';
        }
        
        $validated = $request->validate($validationRules);
        
        // Handle file uploads
        $employeeId = $validated['employee_id'];
        $now = now()->format('Y-d-M');
        $leaveType = $validated['leave_type'];
        $prefixPath = "leave_proofs/$employeeId/$leaveType/$now";
        $proofLeaderPath = $request->file('proof_of_leader_approval')->store($prefixPath, 'public');
        $proofTasksPath = $request->file('proof_of_confirmed_designatory_tasks')->store($prefixPath, 'public');
        
        $leaveData = [
            'employee_id' => $validated['employee_id'],
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'shift_covered' => $validated['shift_covered'],
            'proof_of_leader_approval' => $proofLeaderPath,
            'proof_of_confirmed_designatory_tasks' => $proofTasksPath,
            'status' => LeaveStatus::PENDING
        ];
        
        // Handle optional proof_of_leave file
        if ($request->leave_type === LeaveType::ACADEMIC->value && $request->hasFile('proof_of_leave')) {
            $proofLeavePath = $request->file('proof_of_leave')->store($leaveType, 'public');
            $leaveData['proof_of_leave'] = $proofLeavePath;
        }
        
        $leave = LeaveRequest::create($leaveData);
        
        $successMessage = "Your leave request is now soaring through the approval clouds.";
        
        return response(
            view('leave.create-success', [
                'success' => $successMessage,
                'successObject' => $leave,
                'redirectTo' => 'leave.index',
            ])
        );
    }

    public function show(Request $request, string $id)
    {
        $leave = LeaveRequest::where('id', '=', $id)->first() ?? abort(404);
        if ($request->user()->cannot('view', $leave)) abort(403);

        return response()->json($leave->load('user'));
    }

    public function update(Request $request, string $id)
    {
        if ($request->input('status') != null) abort(403);

        $leave = LeaveRequest::where('id', '=', $id)->first() ?? abort(404);
        if ($request->user()->cannot('update', $leave)) abort(403);

        $validated = $request->validate([
            'leave_type' => 'sometimes|in:' . implode(',', LeaveType::values()),
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'reason' => 'sometimes|string|max:500',
            'shift_covered' => 'sometimes|array',
            'shift_covered.*' => 'string|max:255'
        ]);

        $leave->update($validated);
        return response()->json($leave);
    }

    public function destroy(Request $request, string $id)
    {
        $leave = LeaveRequest::where('id', '=', $id)->first() ?? abort(404);
        if ($request->user()->cannot('delete', $leave)) abort(403);;

        $leave->delete();
        return response()->noContent();
    }

    public function updateStatus(Request $request, string $id)
    {
        $leave = LeaveRequest::where('id', '=', $id)->first() ?? abort(404);
        if ($request->user()->cannot('updateStatus', $leave)) abort(403);

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', LeaveStatus::values()),
            'rejection_reason' => 'required_if:status,'.LeaveStatus::REJECTED->value.'|string|max:255'
        ]);

        $leave->update([
            'status' => $validated['status'],
            'reason' => $validated['rejection_reason'] ?? $leave->reason
        ]);

        return response()->json($leave);
    }
}
