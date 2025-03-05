<?php

use App\Enums\AttendanceType;
use App\Enums\ShiftType;
use App\Enums\UserRole;
use App\Enums\WorkMode;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use App\Services\AttendancePhotoService;
use App\Services\EmployeeAttendanceStatusService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

describe('Attendance Controller as Admin', function () {
    beforeEach(function () {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);
        $this->employee = Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
    });

    it('can filter attendances by date and type', function () {
        Attendance::factory()->create([
            'employee_id' => $this->employee->id,
            'type' => AttendanceType::TIME_IN->value,
            'created_at' => '2023-01-01'
        ]);
        Attendance::factory()->create([
            'employee_id' => $this->employee->id,
            'type' => AttendanceType::TIME_OUT->value,
            'created_at' => '2023-01-01'
        ]);

        $response = $this->get("/attendance?date_from=2023-01-01&date_to=2023-01-01&type=" . AttendanceType::TIME_IN->value);
        
        $response->assertStatus(200);
        $response->assertViewIs('attendance.index');
        $attendances = $response->viewData('attendances');
        expect($attendances->total())->toBe(1);
    });

    it('can create new attendance record', function () {
        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->image('image.jpg');

        $this->mock(
            EmployeeAttendanceStatusService::class, 
            function (MockInterface $mock) {
                $mock->shouldReceive('updateAttendanceStatus')
                    ->once()
                    ->withArgs(function ($employee, $status) {
                        // Ensure $employee is an instance of Employee and the status is what you expect.
                        return $employee instanceof \App\Models\Employee 
                            && $status === \App\Enums\AttendanceStatus::PRESENT;
                    })
                    ->andReturnUsing(function ($employee, $status) {
                        return true;
                    });
            }
        );

        $this->mock(
            AttendancePhotoService::class, 
            function (MockInterface $mock) {
                $mock->shouldReceive('uploadProofs')
                    ->andReturnUsing(function () {
                        $fakeFile = UploadedFile::fake()->image('image.jpg');
                        return [
                            'screenshot_workstation_selfie' => $fakeFile->getPathName(),
                            'screenshot_cgc_chat' => $fakeFile->getPathName(),
                            'screenshot_department_chat' => $fakeFile->getPathName(),
                            'screenshot_team_chat' => $fakeFile->getPathName(),
                            'screenshot_group_chat' => $fakeFile->getPathName(),
                        ];
                    });
            }
        );

        $data = [
            'employee_id' => $this->employee->id,
            'shift_type' => ShiftType::MORNING->value,
            'type' => AttendanceType::TIME_IN->value,
            'work_mode' => WorkMode::REMOTE->value,
            'screenshot_workstation_selfie' => $fakeFile,
            'screenshot_cgc_chat' => $fakeFile,
            'screenshot_department_chat' => $fakeFile,
            'screenshot_team_chat' => $fakeFile,
            'screenshot_group_chat' => $fakeFile,
        ];

        $response = $this->post("/attendance", $data);

        $response->assertSee("Redirecting in");
        $this->assertDatabaseCount('attendances', 1);
    });

    it('can show attendance record', function () {
        $attendance = Attendance::factory()->create(['employee_id' => $this->employee->id]);
        
        $response = $this->get("/attendance/{$attendance->id}");
    
        $response->assertStatus(200);
        $response->assertViewIs('attendance.show');
        $response->assertViewHas('attendance', function ($viewAttendance) use ($attendance) {
            return $viewAttendance->id === $attendance->id;
        });
    });

    it('cannot show non-existing record', function () {
        $nonExistingId = Attendance::max('id') ? Attendance::max('id') + 1 : 999;
        $response = $this->get("/attendance/{$nonExistingId}");
    
        $response->assertStatus(404);
    });

    it('can update attendance record', function () {
        $attendance = Attendance::factory()->create(['type' => AttendanceType::TIME_IN->value]);

        $response = $this->patchJson("/attendance/{$attendance->id}", [
            'type' => AttendanceType::TIME_OUT->value
        ]);

        $response->assertStatus(200);
        expect($attendance->fresh()->type)->toBe(AttendanceType::TIME_OUT);
    });

    it('can delete attendance record', function () {
        $attendance = Attendance::factory()->create();
        
        $response = $this->delete("/attendance/{$attendance->id}");
        
        $response->assertRedirect(route('attendance.index'));
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);
    });

    // // I dont see a need to test out grouped data right now.
    // it('can group data by user', function () {
    //     Attendance::factory()->create([
    //         'employee_id' => $this->employee->id,
    //         'type' => AttendanceType::TIME_IN->value,
    //     ]);
    //     Attendance::factory()->create([
    //         'employee_id' => $this->employee->id,
    //         'type' => AttendanceType::TIME_OUT->value
    //     ]);

    //     $response = $this->get('/attendance?group_by=employee');
        
    //     $response->assertStatus(200);
    //     $response->assertViewIs('attendance.index');
    //     $attendances = $response->viewData('attendances');
    //     $firstGroup = $attendances->first();
    //     expect(isset($firstGroup->employee_id) || isset($firstGroup['employee_id']))->toBeTrue();
    //     expect(isset($firstGroup->total_entries) || isset($firstGroup['total_entries']))->toBeTrue();
    // });
});

describe('Attendance Controller as Employee', function () {
    beforeEach(function () {
        $users = User::factory()->count(2)->create(['role' => UserRole::EMPLOYEE]);
        $this->actingAs($users->first());

        foreach ($users as $user) {
            Employee::factory()->create(['user_id' => $user->id]);
        }

        $this->employee = Employee::first();
    });

    it('cannot show attendance record of another employee', function () {
        $attendance = Attendance::factory()->create(['employee_id' => $this->employee->id + 1]);
        
        $response = $this->get("/attendance/{$attendance->id}");
        
        $response->assertStatus(403);
    });

    it('cannot update attendance record of another employee', function () {
        $attendance = Attendance::factory()->create(['type' => AttendanceType::TIME_IN->value]);

        $response = $this->patchJson("/attendance/{$attendance->id}", [
            'type' => AttendanceType::TIME_OUT->value
        ]);

        $response->assertStatus(403);
    });

    it('cannot delete own attendance record', function () {
        $attendance = Attendance::factory()->create(['employee_id' => $this->employee->id]);
            
        $response = $this->delete("/attendance/{$attendance->id}");
        
        $response->assertStatus(403);
    });
});
