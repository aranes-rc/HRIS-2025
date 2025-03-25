<?php

namespace App\Http\Controllers;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use App\Enums\AttendanceType;
use App\Enums\Department;
use App\Enums\DepartmentTeam;
use App\Enums\EmploymentType;
use App\Enums\RequestStatus;
use App\Enums\ShiftType;
use App\Enums\WorkMode;
use App\Http\Requests\StoreAttendanceRequest;
use App\Services\AttendancePhotoService;
use App\Services\EmployeeAttendanceStatusService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendancePhotoService $photoService;
    protected EmployeeAttendanceStatusService $employeeService;

    public function __construct(
        AttendancePhotoService $photoService,
        EmployeeAttendanceStatusService $employeeService
    ) {
        $this->photoService = $photoService;
        $this->employeeService = $employeeService;
    }
    
    public function index(Request $request)
    {
        

        $validated = $request->validate([
            'date_from' => 'sometimes|date|before_or_equal:date_to',
            'date_to' => 'sometimes|date|after_or_equal:date_from',
            'employee_id' => 'sometimes|exists:employees,id',
            'shift_type' => 'sometimes|in:' . implode(',', ShiftType::values()),
            'type' => 'sometimes|in:' . implode(',', AttendanceType::values()),
            'work_mode' => 'sometimes|in:' . implode(',', WorkMode::values()),
            'group_by' => 'sometimes|in:employee,date,shift_type',
        ]);
        

        $query = Attendance::with('Employee')
            ->when($request->user()->isEmployee(), fn($q) => $q->where('employee_id', $request->user()->id))
            ->filter($validated)
            ->groupedData($validated['group_by'] ?? null)
            ->latest();
            
        $attendances = $query->paginate(15);
        $totalPresent = Attendance::where('status', AttendanceStatus::PRESENT)->count() ?? 0;
        $totalAbsent = Attendance::where('status', AttendanceStatus::ABSENT)->count() ?? 0;
        $totalLeave = Attendance::where('status', AttendanceStatus::LEAVE)->count() ?? 0;
        
        $attendances = Attendance::orderBy('created_at')->get();
        $dates = $attendances->pluck('created_at')->map(fn($date) => $date->format('Y-m-d'))->toArray();
        $counts = $attendances->pluck('attendance_count')->toArray();

        $role = $request->user()->getActiveRole();        
        $roleView = match ($role) {            
            UserRole::EMPLOYEE->value => view('attendance.index.employee', compact('attendances')),            
            UserRole::HR->value => view('attendance.index.hr', compact('attendances', 'totalPresent','totalAbsent','totalLeave')),          
            UserRole::ADMIN->value => view('attendance.index.admin', compact('attendances', 'totalPresent','totalAbsent','totalLeave','dates', 'counts')),            default => abort(403),        };            
            return $roleView;
        
    
    }

    public function create(Request $request)
    {
        $currentShiftType = ShiftType::getCurrentShiftType();
        $currentAttendanceType = AttendanceType::getCurrentAttendanceType();
        $employee = $request->user()->employee()->get()->first();

        // TODO: Improve code
        return view('attendance.create', [
            'employee' => $employee,
            'departments' => Department::options(),
            'departmentTeams' => DepartmentTeam::options(),
            'employmentTypes' => EmploymentType::options(),
            'workModes' => WorkMode::options(),
            'attendanceTypes' => AttendanceType::options(),
            'currentAttendanceType' => $currentAttendanceType == null ? null : $currentAttendanceType->value,
            'shiftTypes' => ShiftType::options(),
            'currentShiftType' => $currentShiftType == null ? null : $currentShiftType->value,
        ]);
    }

    public function store(StoreAttendanceRequest $request)
    {
        $validated = $request->validated();

        $employee = $request->user()->employee;
        $attendanceType = $validated['type'];
        $shiftType = $validated['shift_type'];
        
        $paths = $this->photoService->uploadProofs(
            $request->allFiles(),
            $employee->id,
            $attendanceType,
            $shiftType,
        );

        $attendance = Attendance::create([
            'updated_by' => $employee->id,
            ...$validated,
            ...$paths
        ]);

        $status = AttendanceStatus::PRESENT;
        $successMessage = "Checked Out and Hungry - Grab a Bite, Won't You?";
        if ($attendanceType == AttendanceType::TIME_IN->value) {
            $successMessage = "Checked In and Ready to Shine - Welcome Aboard!";
        } else if ($shiftType == ShiftType::AFTERNOON->value) {
            $successMessage = "Checked Out and Tired - Rest Up for Tomorrow!";
            $status = AttendanceStatus::ABSENT;
        }

        $statusUpdated = $this->employeeService->updateAttendanceStatus($employee, $status);

        if (!$statusUpdated) {
            // TODO
            // Log the failure but continue (don't fail the whole request)
            // Add this to the response if possible
        }

        return response(
            view('attendance.create-success', [
                'success' => $successMessage,
                'successObject' => $attendance,
                'redirectTo' => 'attendance.index',
            ])
        );
    }

    public function show(Request $request, string $id)
    {
        $attendance = Attendance::where('id', '=', $id)->first() ?? abort(404);

        if ($request->user()->cannot('view', $attendance)) abort(403);

        return view('attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $attendance = Attendance::where('id', '=', $id)->first() ?? abort(404);

        if ($request->user()->cannot('update', $attendance)) abort(403);

        return view("attendance.edit", compact('attendance'));
    }

    public function update(Request $request, string $id)
    {
        $attendance = Attendance::where('id', '=', $id)->first() ?? abort(404);

        if ($request->user()->cannot('update', $attendance)) abort(403);

        $validated = $request->validate([
            'shift_type' => 'sometimes|in:' . implode(',', ShiftType::values()),
            'type' => 'sometimes|in:' . implode(',', AttendanceType::values()),
            'work_mode' => 'sometimes|in:' . implode(',', WorkMode::values()),
            'selfie_path' => 'sometimes|string|max:255',
        ]);

        $attendance->update($validated);

        unset($attendance['user']);
        return response()->json($attendance);
    }

    public function destroy(Request $request, string $id)
    {
        $attendance = Attendance::where('id', '=', $id)->first() ?? abort(404);

        if ($request->user()->cannot('delete', $attendance)) abort(403);

        $attendance->delete();
        return redirect()->route('attendance.index');
    }

    public function export(Request $request)
    {
        if ($request->user()->cannot('export', Attendance::class)) abort(403);
        
        // TODO: Implement export logic (CSV, PDF, etc.)
        return response()->json(['message' => 'Export functionality']);
    }
}