<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\WorkType;
use App\Models\WorkRequest;
use Illuminate\Http\Request;

class WorkRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'work_type' => 'sometimes|in:' . implode(',', WorkType::values()),
            'status' => 'sometimes|in:' . implode(',', RequestStatus::values()),
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'employee_id' => 'sometimes|exists:employees,id'
        ]);

        $query = WorkRequest::with('Employee')
            ->when($request->user()->isEmployee(), fn($q) => $q->where('employee_id', $request->user()->employee->id))
            ->filter($validated)
            ->latest();

        return view('work-request.index', ['workRequests' => $query->paginate(15)]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('create', WorkRequest::class)) abort(403);

        // Get the authenticated user's employee record
        $employee = $request->user()->employee;
        
        return view('work-request.create', compact('employee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, WorkRequest $workRequest)
    {
        if ($request->user()->cannot('view', $workRequest)) abort(403);
    
        return view('work-request.show',  compact('workRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkRequest $workRequest)
    {
        if (request()->user()->cannot('update', $workRequest)) abort(403);
        
        $employee = $workRequest->employee;
        
        return view('work-request.edit', compact('workRequest', 'employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkRequest $workRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WorkRequest $workRequest)
    {
        //
    }
}
