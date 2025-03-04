<?php

namespace App\Http\Requests;

use App\Enums\AttendanceType;
use App\Enums\ShiftType;
use App\Enums\WorkMode;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'shift_type' => 'required|in:' . implode(',', ShiftType::values()),
            'type' => 'required|in:' . implode(',', AttendanceType::values()),
            'work_mode' => 'required|in:' . implode(',', WorkMode::values()),
            'screenshot_workstation_selfie' => 'required|image|mimes:jpeg,jpg,png|max:1502',
            'screenshot_cgc_chat' => 'required|image|mimes:jpeg,jpg,png|max:1502',
            'screenshot_department_chat' => 'required|image|mimes:jpeg,jpg,png|max:1502',
            'screenshot_team_chat' => 'required|image|mimes:jpeg,jpg,png|max:1502',
            'screenshot_group_chat' => 'required|image|mimes:jpeg,jpg,png|max:1502',
        ];
    }
}
