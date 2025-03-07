<?php

use App\Models\WorkRequest;
use App\Models\Employee;
use App\Enums\RequestStatus;
use App\Enums\ShiftRequest;
use App\Enums\WorkType;

describe('WorkRequest Model', function () {
    it('has expected fillable attributes', function () {
        $workRequest = new WorkRequest();
        
        $expectedFillable = [
            'employee_id',
            'updated_by',
            'request_date',
            'work_type',
            'shift_request',
            'reason',
            'status',
            'proof_of_team_leader_approval',
            'proof_of_group_leader_approval',
            'proof_of_school_approval'
        ];
        
        expect($workRequest->getFillable())->toBe($expectedFillable);
    });
    
    it('casts attributes correctly', function () {
        $workRequest = new WorkRequest();
        $casts = $workRequest->getCasts();
        
        expect($casts)->toHaveKey('request_date', 'date');
        expect($casts)->toHaveKey('work_type', WorkType::class);
        expect($casts)->toHaveKey('status', RequestStatus::class);
        expect($casts)->toHaveKey('shift_request', ShiftRequest::class);
    });
    
    it('can create a work request with factory', function () {
        $employee = Employee::factory()->create();
        $updatedBy = Employee::factory()->create();
        
        $workRequest = WorkRequest::factory()->create([
            'employee_id' => $employee->id,
            'updated_by' => $updatedBy->id,
            'work_type' => WorkType::values()[0],
            'shift_request' => ShiftRequest::values()[0],
            'status' => RequestStatus::PENDING,
        ]);
        
        $this->assertInstanceOf(WorkRequest::class, $workRequest);
        $this->assertDatabaseHas('work_requests', [
            'id' => $workRequest->id,
            'employee_id' => $employee->id,
            'updated_by' => $updatedBy->id,
        ]);
    });
    
    describe('Relationships', function () {
        it('belongs to an employee', function () {
            $employee = Employee::factory()->create();
            $workRequest = WorkRequest::factory()->create([
                'employee_id' => $employee->id
            ]);
            
            expect($workRequest->employee)->toBeInstanceOf(Employee::class);
            expect($workRequest->employee->id)->toBe($employee->id);
        });
        
        it('belongs to an updated_by employee', function () {
            $employee = Employee::factory()->create();
            $updatedBy = Employee::factory()->create();
            
            $workRequest = WorkRequest::factory()->create([
                'employee_id' => $employee->id,
                'updated_by' => $updatedBy->id
            ]);
            
            expect($workRequest->updatedBy)->toBeInstanceOf(Employee::class);
            expect($workRequest->updatedBy->id)->toBe($updatedBy->id);
        });
    });
    
    describe('Status Check Methods', function () {
        it('can check if request is pending', function () {
            $pendingRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::PENDING
            ]);
            $approvedRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::APPROVED
            ]);
            
            expect($pendingRequest->isPending())->toBeTrue();
            expect($approvedRequest->isPending())->toBeFalse();
        });
        
        it('can check if request is approved', function () {
            $approvedRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::APPROVED
            ]);
            $pendingRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::PENDING
            ]);
            
            expect($approvedRequest->isApproved())->toBeTrue();
            expect($pendingRequest->isApproved())->toBeFalse();
        });
        
        it('can check if request is rejected', function () {
            $rejectedRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::REJECTED
            ]);
            $pendingRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::PENDING
            ]);
            
            expect($rejectedRequest->isRejected())->toBeTrue();
            expect($pendingRequest->isRejected())->toBeFalse();
        });
    });
    
    describe('Filter Scope', function () {
        beforeEach(function () {
            // Create various work requests to test filtering
            $this->pendingRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::PENDING,
                'work_type' => WorkType::values()[0],
                'request_date' => now()->subDays(10)
            ]);
            
            $this->approvedRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::APPROVED,
                'work_type' => WorkType::values()[0],
                'request_date' => now()->subDays(5)
            ]);
            
            $this->rejectedRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::REJECTED,
                'work_type' => WorkType::values()[1],
                'request_date' => now()
            ]);
        });
        
        it('can filter by status', function () {
            $filteredRequests = WorkRequest::filter(['status' => RequestStatus::PENDING])->get();
            
            expect($filteredRequests)->toHaveCount(1);
            expect($filteredRequests->first()->id)->toBe($this->pendingRequest->id);
        });
        
        it('can filter by work type', function () {
            $filteredRequests = WorkRequest::filter(['work_type' => WorkType::values()[1]])->get();
            
            expect($filteredRequests)->toHaveCount(1);
            expect($filteredRequests->first()->id)->toBe($this->rejectedRequest->id);
        });
        
        it('can filter by date range', function () {
            $filteredRequests = WorkRequest::filter([
                'date_from' => now()->subDays(7)->format('Y-m-d'),
                'date_to' => now()->addDay()->format('Y-m-d')
            ])->get();
            
            expect($filteredRequests)->toHaveCount(2);
            expect($filteredRequests->pluck('id'))->toContain($this->approvedRequest->id);
            expect($filteredRequests->pluck('id'))->toContain($this->rejectedRequest->id);
            expect($filteredRequests->pluck('id'))->not->toContain($this->pendingRequest->id);
        });
        
        it('can combine multiple filters', function () {
            $filteredRequests = WorkRequest::filter([
                'status' => RequestStatus::APPROVED,
                'work_type' => WorkType::values()[0],
                'date_from' => now()->subDays(7)->format('Y-m-d')
            ])->get();
            
            expect($filteredRequests)->toHaveCount(1);
            expect($filteredRequests->first()->id)->toBe($this->approvedRequest->id);
        });
        
        it('returns all requests when no filters applied', function () {
            $filteredRequests = WorkRequest::filter([])->get();
            
            expect($filteredRequests)->toHaveCount(3);
        });
    });
    
    describe('Enum Usage', function () {
        it('uses WorkType enum correctly', function () {
            $workRequest = WorkRequest::factory()->create([
                'work_type' => WorkType::values()[0]
            ]);
            
            expect($workRequest->work_type)->toBeInstanceOf(WorkType::class);
            expect($workRequest->work_type->value)->toBe(WorkType::values()[0]);
        });
        
        it('uses ShiftRequest enum correctly', function () {
            $workRequest = WorkRequest::factory()->create([
                'shift_request' => ShiftRequest::values()[0]
            ]);
            
            expect($workRequest->shift_request)->toBeInstanceOf(ShiftRequest::class);
            expect($workRequest->shift_request->value)->toBe(ShiftRequest::values()[0]);
        });
        
        it('uses RequestStatus enum correctly', function () {
            $workRequest = WorkRequest::factory()->create([
                'status' => RequestStatus::APPROVED
            ]);
            
            expect($workRequest->status)->toBeInstanceOf(RequestStatus::class);
            expect($workRequest->status->value)->toBe(RequestStatus::APPROVED->value);
        });
    });
});