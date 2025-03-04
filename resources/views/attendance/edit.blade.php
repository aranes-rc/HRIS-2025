@php
    use App\Enums\ShiftType;
    use App\Enums\AttendanceType;
    use App\Enums\WorkMode;
@endphp

@extends('components.layout.root')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Edit Attendance</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('attendance.update', $attendance->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Information (read-only) -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2" for="employee">Employee</label>
                    <input type="text" id="employee" value="{{ $attendance->employee->getFullName() ?? 'N/A' }}" disabled class="w-full border border-gray-300 rounded px-4 py-2 bg-gray-100">
                </div>
                <!-- Shift Type -->
                <x-form.select name="shift_type" label="Shift Type" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach (ShiftType::options() as $key => $value)
                        <option value="{{ $key }}" {{ $attendance->shift_type->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>

                <!-- Type -->
                <x-form.select name="type" label="Attendance Type" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach (AttendanceType::options() as $key => $value)
                        <option value="{{ $key }}" {{ $attendance->type->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>

                <!-- Work Mode -->
                <x-form.select name="work_mode" label="Attendance Type" class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach (WorkMode::options() as $key => $value)
                        <option value="{{ $key }}" {{ $attendance->type->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
            </div>
            <!-- Current Proof Screenshots Preview -->
            <div class="mt-6">
                <h2 class="text-xl font-semibold mb-4">Current Proof Screenshots</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <img src="{{ asset($attendance->screenshot_workstation_selfie) }}" alt="Selfie" class="w-full h-40 object-cover rounded">
                        <p class="text-center mt-2">Selfie</p>
                    </div>
                    <div>
                        <img src="{{ asset($attendance->screenshot_cgc_chat) }}" alt="CGC Chat" class="w-full h-40 object-cover rounded">
                        <p class="text-center mt-2">CGC Chat</p>
                    </div>
                    <div>
                        <img src="{{ asset($attendance->screenshot_department_chat) }}" alt="Department Chat" class="w-full h-40 object-cover rounded">
                        <p class="text-center mt-2">Department Chat</p>
                    </div>
                    <div>
                        <img src="{{ asset($attendance->screenshot_team_chat) }}" alt="Team Chat" class="w-full h-40 object-cover rounded">
                        <p class="text-center mt-2">Team Chat</p>
                    </div>
                    <div>
                        <img src="{{ asset($attendance->screenshot_group_chat) }}" alt="Group Chat" class="w-full h-40 object-cover rounded">
                        <p class="text-center mt-2">Group Chat</p>
                    </div>
                </div>
            </div>
            <!-- Submit & Cancel Buttons -->
            <div class="mt-6 flex gap-4">
                <x-button text="Update Attendance" type="submit"/>
                <a href="{{ url()->previous() }}" class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
