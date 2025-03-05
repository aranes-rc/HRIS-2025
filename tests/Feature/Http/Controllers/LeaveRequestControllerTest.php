<?php

use App\Enums\LeaveStatus;
use App\Enums\LeaveType;
use App\Enums\ShiftType;
use App\Enums\UserRole;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->employeeUser = User::factory()->create(['role' => UserRole::EMPLOYEE]);
    $this->employee = Employee::factory()->create(['user_id' => $this->employeeUser->id]);
    
    $this->leaveData = [
        'employee_id' => $this->employee->id,
        'leave_type' => LeaveType::SICK->value,
        'start_date' => now()->addDay()->format('Y-m-d'),
        'end_date' => now()->addDays(3)->format('Y-m-d'),
        'reason' => 'Medical reasons',
        'shift_covered' => [ShiftType::MORNING->value],
        'proof_of_leader_approval' => UploadedFile::fake()->image('approval.jpg'),
        'proof_of_confirmed_designatory_tasks' => UploadedFile::fake()->image('tasks.jpg'),
    ];
});

describe('LeaveRequestController as Admin', function () {
    beforeEach(function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->adminEmployee = Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
    });

    it('can view all leave requests', function () {
        LeaveRequest::factory()->count(3)->create();
        $response = $this->get(route('leave.index'));
        $response->assertOk()->assertViewHas('leave_requests');
        expect($response->viewData('leave_requests'))->toHaveCount(3);
    });

    it('can view any leave request', function () {
        $leave = LeaveRequest::factory()->create();
        $response = $this->get(route('leave.show', $leave));
        $response->assertOk();
    });

    it('can update any leave request', function () {
        $leave = LeaveRequest::factory()->create();
        $response = $this->put(route('leave.update', $leave), [
            ...$this->leaveData,
            'status' => LeaveStatus::APPROVED->value
        ]);
        $response->assertRedirect(route('leave.index'));
    });

    it('can delete any leave request', function () {
        $leave = LeaveRequest::factory()->create();
        $response = $this->delete(route('leave.destroy', $leave));
        $response->assertRedirect(route('leave.index'));
        $this->assertModelMissing($leave);
    });

    it('can update status of any request', function () {
        $leave = LeaveRequest::factory()->create();
        $response = $this->patch(route('leave.update.status', $leave), [
            'status' => LeaveStatus::APPROVED->value
        ]);
        $response->assertOk();
        expect($leave->fresh()->status)->toBe(LeaveStatus::APPROVED);
    });
});

describe('LeaveRequestController as HR', function () {
    beforeEach(function () {
        $user = User::factory()->create(['role' => UserRole::HR]);
        $this->hrEmployee = Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
    });

    it('can view all leave requests', function () {
        LeaveRequest::factory()->count(3)->create();
        $response = $this->get(route('leave.index'));
        $response->assertOk()->assertViewHas('leave_requests');
        expect($response->viewData('leave_requests'))->toHaveCount(3);
    });

    it('can update status of requests', function () {
        $leave = LeaveRequest::factory()->create();
        $response = $this->patch(route('leave.update.status', $leave), [
            'status' => LeaveStatus::REJECTED->value,
            'rejection_reason' => 'Insufficient documentation'
        ]);
        $response->assertOk();
        expect($leave->fresh()->status)->toBe(LeaveStatus::REJECTED);
    });

    it('cannot delete leave requests', function () {
        $leave = LeaveRequest::factory()->create();
        $response = $this->delete(route('leave.destroy', $leave));
        $response->assertForbidden();
    });
});

describe('LeaveRequestController as Employee', function () {
    beforeEach(function () {
        $this->actingAs($this->employeeUser);
    });

    it('can view own leave requests', function () {
        LeaveRequest::factory()->create(['employee_id' => $this->employee->id]);
        $response = $this->get(route('leave.index'));
        $response->assertOk()->assertViewHas('leave_requests');
        expect($response->viewData('leave_requests'))->toHaveCount(1);
    });

    it('cannot view others leave requests', function () {
        $otherLeave = LeaveRequest::factory()->create();
        $response = $this->get(route('leave.show', $otherLeave));
        $response->assertForbidden();
    });

    it('can create leave requests', function () {
        $response = $this->post(route('leave.store'), $this->leaveData);
        $response->assertOk();
        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $this->employee->id,
            'status' => LeaveStatus::PENDING->value
        ]);
    });

    it('can update own pending requests', function () {
        $leave = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => LeaveStatus::PENDING
        ]);
        $response = $this->put(route('leave.update', $leave), [
            ...$this->leaveData,
            'reason' => 'Updated reason'
        ]);
        $response->assertRedirect();
        expect($leave->fresh()->reason)->toBe('Updated reason');
    });

    it('cannot update approved requests', function () {
        $leave = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => LeaveStatus::APPROVED
        ]);
        $response = $this->put(route('leave.update', $leave), $this->leaveData);
        $response->assertForbidden();
    });

    it('can delete own pending requests', function () {
        $leave = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => LeaveStatus::PENDING
        ]);
        $response = $this->delete(route('leave.destroy', $leave));
        $response->assertRedirect();
        $this->assertModelMissing($leave);
    });

    it('cannot delete approved requests', function () {
        $leave = LeaveRequest::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => LeaveStatus::APPROVED
        ]);
        $response = $this->delete(route('leave.destroy', $leave));
        $response->assertForbidden();
    });

    it('cannot update status', function () {
        $leave = LeaveRequest::factory()->create(['employee_id' => $this->employee->id]);
        $response = $this->patch(route('leave.update.status', $leave), [
            'status' => LeaveStatus::APPROVED->value
        ]);
        $response->assertForbidden();
    });
});

// Shared tests across roles
test('validation works for store method', function () {
    $this->actingAs($this->employeeUser);

    $invalidData = [...$this->leaveData, 'start_date' => now()->subDay()];
    $response = $this->post(route('leave.store'), $invalidData);
    $response->assertInvalid(['start_date']);
});

test('academic leave requires proof of leave', function () {
    $this->actingAs($this->employeeUser);

    $data = [...$this->leaveData, 'leave_type' => LeaveType::ACADEMIC->value];
    $response = $this->post(route('leave.store'), $data);
    $response->assertInvalid(['proof_of_leave']);

    $validData = [...$data, 'proof_of_leave' => UploadedFile::fake()->image('proof.jpg')];
    $response = $this->post(route('leave.store'), $validData);
    $response->assertValid();
});