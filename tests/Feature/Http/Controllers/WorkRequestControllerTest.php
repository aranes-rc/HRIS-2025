<?php

use App\Models\User;
use App\Models\Employee;
use App\Models\WorkRequest;
use App\Enums\RequestStatus;
use App\Enums\ShiftRequest;
use App\Enums\WorkType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Factory and setup helper functions
function createUserWithRole($role = 'admin')
{
    $user = User::factory()->create(['role' => $role]);
    $employee = Employee::factory()->create(['user_id' => $user->id]);
    $user->employee = $employee;
    return $user;
}

function createWorkRequest($employeeId = null, $status = null)
{
    return WorkRequest::factory()->create([
        'employee_id' => $employeeId ?? Employee::factory()->create()->id,
        'status' => $status ?? RequestStatus::PENDING,
        'work_type' => WorkType::values()[array_rand(WorkType::values())],
        'shift_request' => ShiftRequest::values()[array_rand(ShiftRequest::values())],
    ]);
}

describe('WorkRequestController as Admin', function () {
    beforeEach(function () {
        $this->admin = createUserWithRole('admin');
    });

    it('can view index page with all work requests', function () {
        $workRequests = WorkRequest::factory()->count(3)->create();
        
        $response = $this->actingAs($this->admin)
            ->get(route('work-request.index'));
            
        $response->assertStatus(200)
            ->assertViewIs('work-request.index')
            ->assertViewHas('workRequests');
    });

    it('can filter work requests', function () {
        $workRequestPending = createWorkRequest(null, RequestStatus::PENDING);
        $workRequestApproved = createWorkRequest(null, RequestStatus::APPROVED);
        
        $response = $this->actingAs($this->admin)
            ->get(route('work-request.index', ['status' => RequestStatus::PENDING]));
            
        $response->assertStatus(200)
            ->assertViewHas('workRequests', function ($workRequests) use ($workRequestPending) {
                return $workRequests->contains($workRequestPending) && 
                       $workRequests->count() === 1;
            });
    });

    it('can access create page', function () {
        $response = $this->actingAs($this->admin)
            ->get(route('work-request.create'));
            
        $response->assertStatus(200)
            ->assertViewIs('work-request.create')
            ->assertViewHas('employee');
    });

    it('can store new work request', function () {
        Storage::fake('public');
        
        $schoolApproval = UploadedFile::fake()->create('school_approval.pdf', 1000);
        $teamLeaderApproval = UploadedFile::fake()->create('team_approval.pdf', 1000);
        $groupLeaderApproval = UploadedFile::fake()->create('group_approval.pdf', 1000);
        
        $response = $this->actingAs($this->admin)
            ->post(route('work-request.store'), [
                'employee_id' => $this->admin->employee->id,
                'request_date' => now()->format('Y-m-d'),
                'work_type' => WorkType::values()[0],
                'shift_request' => ShiftRequest::values()[0],
                'reason' => 'Testing work request',
                'proof_of_team_leader_approval' => $teamLeaderApproval,
                'proof_of_group_leader_approval' => $groupLeaderApproval,
                'proof_of_school_approval' => $schoolApproval,
            ]);
            
        $response->assertRedirect(route('work-request.index'))
            ->assertSessionHas('success');
            
        $this->assertDatabaseHas('work_requests', [
            'employee_id' => $this->admin->employee->id,
            'work_type' => WorkType::values()[0],
            'shift_request' => ShiftRequest::values()[0],
            'reason' => 'Testing work request',
            'status' => RequestStatus::PENDING,
        ]);
        
        $workRequest = WorkRequest::latest('id')->first();
        
        Storage::disk('public')->assertExists($workRequest->proof_of_school_approval);
        Storage::disk('public')->assertExists($workRequest->proof_of_team_leader_approval);
        Storage::disk('public')->assertExists($workRequest->proof_of_group_leader_approval);
    });

    it('can view any work request', function () {
        $workRequest = createWorkRequest();
        
        $response = $this->actingAs($this->admin)
            ->get(route('work-request.show', $workRequest));
            
        $response->assertStatus(200)
            ->assertViewIs('work-request.show')
            ->assertViewHas('workRequest');
    });

    it('can edit any work request', function () {
        $workRequest = createWorkRequest();
        
        $response = $this->actingAs($this->admin)
            ->get(route('work-request.edit', $workRequest));
            
        $response->assertStatus(200)
            ->assertViewIs('work-request.edit')
            ->assertViewHas('workRequest')
            ->assertViewHas('employee');
    });

    it('can update any work request', function () {
        Storage::fake('public');
        
        $workRequest = createWorkRequest();
        $newFile = UploadedFile::fake()->create('updated_approval.pdf', 1000);
        
        $response = $this->actingAs($this->admin)
            ->put(route('work-request.update', $workRequest), [
                'reason' => 'Updated reason',
                'status' => RequestStatus::APPROVED->value,
                'proof_of_school_approval' => $newFile,
            ]);
            
        $response->assertRedirect(route('work-request.index'))
            ->assertSessionHas('success');
            
        $this->assertDatabaseHas('work_requests', [
            'id' => $workRequest->id,
            'reason' => 'Updated reason',
            'status' => RequestStatus::APPROVED,
        ]);
        
        $updatedWorkRequest = WorkRequest::find($workRequest->id);
        Storage::disk('public')->assertExists($updatedWorkRequest->proof_of_school_approval);
    });

    it('can delete any work request', function () {
        Storage::fake('public');
        
        $workRequest = createWorkRequest();
        
        // Mock file paths in the database
        $filePath = "work_requests/test_approval.pdf";
        $workRequest->update([
            'proof_of_team_leader_approval' => $filePath,
        ]);
        
        // Create the file to be deleted
        Storage::disk('public')->put($filePath, 'test content');
        
        $response = $this->actingAs($this->admin)
            ->delete(route('work-request.destroy', $workRequest));
            
        $response->assertRedirect(route('work-request.index'))
            ->assertSessionHas('success');
            
        $this->assertDatabaseMissing('work_requests', ['id' => $workRequest->id]);
        Storage::disk('public')->assertMissing($filePath);
    });
});

describe('WorkRequestController as HR', function () {
    beforeEach(function () {
        $this->hr = createUserWithRole('hr');
    });

    it('can view index page with all work requests', function () {
        $workRequests = WorkRequest::factory()->count(3)->create();
        
        $response = $this->actingAs($this->hr)
            ->get(route('work-request.index'));
            
        $response->assertStatus(200)
            ->assertViewHas('workRequests');
    });
    
    it('can access create page', function () {
        $response = $this->actingAs($this->hr)
            ->get(route('work-request.create'));
            
        $response->assertStatus(200);
    });
    
    it('can view any work request', function () {
        $workRequest = createWorkRequest();
        
        $response = $this->actingAs($this->hr)
            ->get(route('work-request.show', $workRequest));
            
        $response->assertStatus(200);
    });
    
    it('can update work request status', function () {
        $workRequest = createWorkRequest();
        
        $response = $this->actingAs($this->hr)
            ->put(route('work-request.update', $workRequest), [
                'status' => RequestStatus::APPROVED->value,
            ]);
            
        $response->assertRedirect(route('work-request.index'));
        
        $this->assertDatabaseHas('work_requests', [
            'id' => $workRequest->id,
            'status' => RequestStatus::APPROVED,
        ]);
    });
});

describe('WorkRequestController as Employee', function () {
    beforeEach(function () {
        $this->employee = createUserWithRole('employee');
    });

    it('can only view own work requests in index', function () {
        // Create work requests for the employee
        $ownWorkRequest = createWorkRequest($this->employee->employee->id);
        
        // Create work requests for another employee
        $otherEmployee = createUserWithRole('employee');
        $otherWorkRequest = createWorkRequest($otherEmployee->employee->id);
        
        $response = $this->actingAs($this->employee)
            ->get(route('work-request.index'));
            
        $response->assertStatus(200)
            ->assertViewHas('workRequests', function ($workRequests) use ($ownWorkRequest, $otherWorkRequest) {
                return $workRequests->contains($ownWorkRequest) && 
                       !$workRequests->contains($otherWorkRequest);
            });
    });

    it('can create own work request', function () {
        Storage::fake('public');
        
        $tempFile = UploadedFile::fake()->create('mock.pdf', 1000);
        
        $response = $this->actingAs($this->employee)
            ->post(route('work-request.store'), [
                'employee_id' => $this->employee->employee->id,
                'request_date' => now()->format('Y-m-d'),
                'work_type' => WorkType::values()[0],
                'shift_request' => ShiftRequest::values()[0],
                'reason' => 'Employee test reason',
                'proof_of_team_leader_approval' => $tempFile,
                'proof_of_group_leader_approval' => $tempFile,
                'proof_of_school_approval' => $tempFile,
            ]);
            
        $response->assertRedirect(route('work-request.index'))
            ->assertSessionHas('success');
            
        $this->assertDatabaseHas('work_requests', [
            'employee_id' => $this->employee->employee->id,
            'reason' => 'Employee test reason',
        ]);
    });

    it('can view own work request', function () {
        $workRequest = createWorkRequest($this->employee->employee->id);
        
        $response = $this->actingAs($this->employee)
            ->get(route('work-request.show', $workRequest));
            
        $response->assertStatus(200);
    });

    it('cannot view other employee work request', function () {
        $otherEmployee = createUserWithRole('employee');
        $workRequest = createWorkRequest($otherEmployee->employee->id);
        
        $response = $this->actingAs($this->employee)
            ->get(route('work-request.show', $workRequest));
            
        $response->assertStatus(403);
    });

    it('can edit own pending work request', function () {
        $workRequest = createWorkRequest($this->employee->employee->id, RequestStatus::PENDING);
        
        $response = $this->actingAs($this->employee)
            ->get(route('work-request.edit', $workRequest));
            
        $response->assertStatus(200);
    });

    it('cannot edit own approved work request', function () {
        $workRequest = createWorkRequest($this->employee->employee->id, RequestStatus::APPROVED);
        
        // This will depend on your policy implementation, but typically employees
        // shouldn't be able to edit approved requests
        $response = $this->actingAs($this->employee)
            ->get(route('work-request.edit', $workRequest));
            
        $response->assertStatus(403);
    });

    it('cannot edit other employee work request', function () {
        $otherEmployee = createUserWithRole('employee');
        $workRequest = createWorkRequest($otherEmployee->employee->id);
        
        $response = $this->actingAs($this->employee)
            ->get(route('work-request.edit', $workRequest));
            
        $response->assertStatus(403);
    });

    it('can update own pending work request', function () {
        $workRequest = createWorkRequest($this->employee->employee->id, RequestStatus::PENDING);
        
        $response = $this->actingAs($this->employee)
            ->put(route('work-request.update', $workRequest), [
                'reason' => 'Updated by employee',
            ]);
            
        $response->assertRedirect(route('work-request.index'));
        
        $this->assertDatabaseHas('work_requests', [
            'id' => $workRequest->id,
            'reason' => 'Updated by employee',
        ]);
    });

    it('cannot change work request status', function () {
        $workRequest = createWorkRequest($this->employee->employee->id, RequestStatus::PENDING);
        
        $response = $this->actingAs($this->employee)
            ->put(route('work-request.update', $workRequest), [
                'status' => RequestStatus::APPROVED,
            ]);
            
        // This depends on your policy implementation
        // Either it should not update the status or should return 403
        $this->assertDatabaseHas('work_requests', [
            'id' => $workRequest->id,
            'status' => RequestStatus::PENDING, // Status should remain unchanged
        ]);
    });

    it('can delete own pending work request', function () {
        $workRequest = createWorkRequest($this->employee->employee->id, RequestStatus::PENDING);
        
        $response = $this->actingAs($this->employee)
            ->delete(route('work-request.destroy', $workRequest));
            
        $response->assertRedirect(route('work-request.index'));
        
        $this->assertDatabaseMissing('work_requests', ['id' => $workRequest->id]);
    });

    it('cannot delete own approved work request', function () {
        $workRequest = createWorkRequest($this->employee->employee->id, RequestStatus::APPROVED);
        
        $response = $this->actingAs($this->employee)
            ->delete(route('work-request.destroy', $workRequest));
            
        $response->assertStatus(403);
        
        $this->assertDatabaseHas('work_requests', ['id' => $workRequest->id]);
    });
});