@php
    use App\Enums\WorkType;
    use App\Enums\ShiftRequest;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-8 px-6 max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create Work Request</h1>
            <p class="mt-2 text-sm text-gray-600">Submit a request for Saturday or Holiday work.</p>
        </div>
        <a href="{{ route('work-request.index') }}">
            <x-button text="Back to List" containerColor="gray-200" contentColor="gray-800" />
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8">
        <form action="{{ route('work-request.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Employee Info (hidden) -->
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Request Info Section -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-3 mb-4">Request Information</h2>
                </div>
                
                <!-- Employee Name (Read-only) -->
                <div>
                    <x-form.label name="employee_name" label="Employee Name" />
                    <div class="mt-2 p-3 bg-gray-50 rounded-md text-gray-700">
                        {{ $employee->getFullName() }}
                    </div>
                </div>
                
                <!-- Request Date -->
                <x-form.input type="date" name="request_date" label="Request Date" value="{{ old('request_date', now()->format('Y-m-d')) }}" required />
                
                <!-- Work Type -->
                <x-form.select name="work_type" label="Work Type" required>
                    <option value="" selected disabled>Select Work Type</option>
                    @foreach (WorkType::options() as $key => $value)
                        <option value="{{ $key }}" {{ old('work_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
                
                <!-- Shift Request -->
                <x-form.select name="shift_request" label="Shift Request" required>
                    <option value="" selected disabled>Select Shift Request</option>
                    @foreach (ShiftRequest::options() as $key => $value)
                        <option value="{{ $key }}" {{ old('shift_request') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
                
                <!-- Reason -->
                <div class="md:col-span-2">
                    <x-form.label name="reason" label="Detailed Reason" />
                    <div class="mt-2">
                        <textarea
                            id="reason"
                            name="reason"
                            rows="4"
                            class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-primary/60 sm:text-sm/6"
                            placeholder="Please provide a detailed reason for your work request"
                            required
                        >{{ old('reason') }}</textarea>
                        <x-form.error name="reason" />
                    </div>
                </div>
                
                <!-- Required Documents Section -->
                <div class="md:col-span-2 pt-4">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-3 mb-4">Required Approvals</h2>
                    <p class="text-sm text-gray-600 mb-4">Please attach proof of prior approvals from relevant authorities.</p>
                </div>
                
                <!-- Team Leader Approval -->
                <div class="md:col-span-2">
                    <x-form.file 
                        label="Team Leader Approval" 
                        name="proof_of_team_leader_approval" 
                        accept=".jpeg,.jpg,.png,.pdf" 
                        required 
                    />
                </div>
                
                <!-- Group Leader Approval -->
                <div class="md:col-span-2">
                    <x-form.file 
                        label="Group Leader Approval" 
                        name="proof_of_group_leader_approval" 
                        accept=".jpeg,.jpg,.png,.pdf" 
                        required 
                    />
                </div>
                
                <!-- School Approval (if applicable) -->
                <div class="md:col-span-2">
                    <x-form.file 
                        label="School Approval (if applicable)" 
                        name="proof_of_school_approval" 
                        accept=".jpeg,.jpg,.png,.pdf"
                    />
                    <p class="mt-2 text-sm text-gray-500">Only required if you are currently enrolled in educational activities.</p>
                </div>
            </div>
            
            <!-- Terms and Conditions -->
            <div class="mt-8 border-t pt-6">
                <div class="flex items-start">
                    <div class="flex h-6 items-center">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" 
                            required
                        >
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-sm text-gray-700">
                            I confirm that all information provided is accurate and complete. I understand that providing false information may result in disciplinary action.
                        </label>
                    </div>
                </div>
                <x-form.error name="terms" />
            </div>
            
            <!-- Form Actions -->
            <div class="pt-6 flex justify-end space-x-4">
                <a href="{{ route('work-request.index') }}">
                    <x-button type="button" text="Cancel" containerColor="gray-200" contentColor="gray-800" />
                </a>
                <x-button type="submit" text="Submit Request" containerColor="primary" contentColor="white" />
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any client-side validation or dynamic behavior here
        const workTypeSelect = document.getElementById('work_type');
        const reasonField = document.getElementById('reason');
        
        workTypeSelect.addEventListener('change', function() {
            if (this.value) {
                const workType = this.options[this.selectedIndex].text;
                reasonField.placeholder = `Please provide detailed reason for requesting ${workType}`;
            }
        });
    });
</script>
@endsection