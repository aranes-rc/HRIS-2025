<?php

use App\Enums\AttendanceStatus;
use App\Enums\AttendanceType;
use App\Enums\ShiftType;
use App\Enums\WorkMode;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

describe('Attendance Model', function () {
    beforeEach(function () {
        $this->attendance = Attendance::factory()->create(['type' => AttendanceType::TIME_OUT]);
    });

    it('belongs to an employee', function () {
        expect($this->attendance->employee)->toBeInstanceOf(Employee::class);
    });

    it('has correct cast attributes', function () {
        expect($this->attendance->getCasts())
            ->toMatchArray([
                'shift_type' => ShiftType::class,
                'type' => AttendanceType::class,
                'work_mode' => WorkMode::class,
            ]);
    });

    it('applies filter scope correctly', function () {
        $attendance = Attendance::factory()->create([
            'type' => AttendanceType::TIME_IN
        ]);

        $request = new Request([
            'date_from' => '2023-01-01',
            'type' => AttendanceType::TIME_IN->value
        ]);
        $filtered = Attendance::filter($request->all())->get();

        expect($filtered)->toHaveCount(1)
            ->and($filtered->first()->id)->toBe($attendance->id);
    });

    it('applies grouped data scope correctly', function () {
        $employee = Employee::factory()->create();
        Attendance::factory()->count(3)->create(['employee_id' => $employee->id]);

        $grouped = Attendance::groupedData('employee')->get();

        expect($grouped)->toHaveCount(2)
            ->and($grouped->last()->total_entries)->toBe(3);
    });

    it('prevents duplicate entries for same user/date/type', function () {
        $data = [
            'id' => random_int(1000, 9506),
            'employee_id' => $this->attendance->employee()->get()->first()->id,
            'type' => AttendanceType::TIME_IN
        ];

        Attendance::factory()->create($data);

        expect(fn() => Attendance::factory()->create($data))
            ->toThrow(\Illuminate\Database\QueryException::class);
    });
});
