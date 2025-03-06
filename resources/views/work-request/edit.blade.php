@php
    use App\Enums\WorkType;
    use App\Enums\ShiftRequest;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-8 px-6 max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Work Request</h1>
            <p class="mt-2 text-sm text-gray-600">Update your existing request</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('work-request.show', $workRequest) }}">
                <x-button text="View Details" containerColor="blue-100" contentColor="blue-700" />
            </a>
            <a href="{{ route('work-request.index') }}">
                <x-button text="Back to List" containerColor="gray-200" contentColor="gray-800" />
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8">
        <form action="{{ route('work-request.update', $workRequest) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PATCH')
            
            <!-- Employee Info (hidden) -->
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <!-- Request Info Section -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-3 mb-4">Request Information</h2>
                </div>
                
                <!-- Work Request ID (Read-only) -->
                <div>
                    <x-form.label name="work_request_id" label="Request ID" />
                    <div class="mt-2 p-3 bg-gray-50 rounded-md text-gray-700">
                        {{ $workRequest->id }}
                    </div>
                </div>
                
                <!-- Employee Name (Read-only) -->
                <div>
                    <x-form.label name="employee_name" label="Employee Name" />
                    <div class="mt-2 p-3 bg-gray-50 rounded-md text-gray-700">
                        {{ $employee->getFullName() }}
                    </div>
                </div>
                
                <!-- Request Date -->
                <x-form.input type="date" name="request_date" label="Request Date" value="{{ old('request_date', $workRequest->request_date->format('Y-m-d')) }}" required />
                
                <!-- Work Type -->
                <x-form.select name="work_type" label="Work Type" required>
                    <option value="" disabled>Select Work Type</option>
                    @foreach (WorkType::options() as $key => $value)
                        <option value="{{ $key }}" {{ old('work_type', $workRequest->work_type->value) == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
                
                <!-- Shift Request -->
                <x-form.select name="shift_request" label="Shift Request" required>
                    <option value="" selected disabled>Select Shift Request</option>
                    @foreach (ShiftRequest::options() as $key => $value)
                        <option value="{{ $key }}" {{ old('shift_request', $workRequest->shift_request->value) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                        >{{ old('reason', $workRequest->reason) }}</textarea>
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
                    @if($workRequest->proof_of_team_leader_approval)
                        <div class="mb-2 flex items-center space-x-2">
                            <span class="text-sm text-green-600">Document already uploaded</span>
                            <button 
                                type="button" 
                                class="text-sm text-primary hover:text-primary/80 font-medium"
                                @click="openImagePreview('{{ asset('storage/' . $workRequest->proof_of_team_leader_approval) }}', 'Team Leader Approval')"
                            >
                                Preview
                            </button>
                        </div>
                    @endif
                    <x-form.file 
                        label="Team Leader Approval" 
                        name="proof_of_team_leader_approval" 
                        accept=".jpeg,.jpg,.png,.pdf" 
                        :required="!$workRequest->proof_of_team_leader_approval" 
                    />
                    @if($workRequest->proof_of_team_leader_approval)
                        <p class="mt-2 text-sm text-gray-500">Upload a new file only if you want to replace the existing document.</p>
                    @endif
                </div>
                
                <!-- Group Leader Approval -->
                <div class="md:col-span-2">
                    @if($workRequest->proof_of_group_leader_approval)
                        <div class="mb-2 flex items-center space-x-2">
                            <span class="text-sm text-green-600">Document already uploaded</span>
                            <button 
                                type="button" 
                                class="text-sm text-primary hover:text-primary/80 font-medium"
                                @click="openImagePreview('{{ asset('storage/' . $workRequest->proof_of_group_leader_approval) }}', 'Group Leader Approval')"
                            >
                                Preview
                            </button>
                        </div>
                    @endif
                    <x-form.file 
                        label="Group Leader Approval" 
                        name="proof_of_group_leader_approval" 
                        accept=".jpeg,.jpg,.png,.pdf" 
                        :required="!$workRequest->proof_of_group_leader_approval" 
                    />
                    @if($workRequest->proof_of_group_leader_approval)
                        <p class="mt-2 text-sm text-gray-500">Upload a new file only if you want to replace the existing document.</p>
                    @endif
                </div>
                
                <!-- School Approval (if applicable) -->
                <div class="md:col-span-2">
                    @if($workRequest->proof_of_school_approval)
                        <div class="mb-2 flex items-center space-x-2">
                            <span class="text-sm text-green-600">Document already uploaded</span>
                            <button 
                                type="button" 
                                class="text-sm text-primary hover:text-primary/80 font-medium"
                                @click="openImagePreview('{{ asset('storage/' . $workRequest->proof_of_school_approval) }}', 'School Approval')"
                            >
                                Preview
                            </button>
                        </div>
                    @endif
                    <x-form.file 
                        label="School Approval (if applicable)" 
                        name="proof_of_school_approval" 
                        accept=".jpeg,.jpg,.png,.pdf"
                    />
                    <p class="mt-2 text-sm text-gray-500">
                        @if($workRequest->proof_of_school_approval)
                            Upload a new file only if you want to replace the existing document.
                        @else
                            Only required if you are currently enrolled in educational activities.
                        @endif
                    </p>
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
                            checked
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
                <a href="{{ route('work-request.show', $workRequest) }}">
                    <x-button type="button" text="Cancel" containerColor="gray-200" contentColor="gray-800" />
                </a>
                <x-button type="submit" text="Update Request" containerColor="primary" contentColor="white" />
            </div>
        </form>
    </div>
</div>

<!-- Image Preview Modal -->
<div x-data="{ showModal: false, imageUrl: '', title: '' }" x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-90" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4" x-cloak>
    <div @click.away="showModal = false" class="bg-white rounded-xl shadow-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-lg font-medium text-gray-900" x-text="title"></h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6 overflow-auto" style="max-height: calc(90vh - 80px);">
            <img :src="imageUrl" class="max-w-full h-auto mx-auto" />
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        window.openImagePreview = function(url, title) {
            // Check if the file is an image
            const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(url);
            
            if (isImage) {
                // Open in modal
                const modal = document.querySelector('[x-data="{ showModal: false, imageUrl: \'\', title: \'\' }"]').__x.$data;
                modal.imageUrl = url;
                modal.title = title;
                modal.showModal = true;
            } else {
                // Open in new tab for non-image files
                window.open(url, '_blank');
            }
        };
        
        // Add any client-side validation or dynamic behavior here
        const workTypeSelect = document.getElementById('work_type');
        const reasonField = document.getElementById('reason');
        
        if (workTypeSelect && reasonField) {
            workTypeSelect.addEventListener('change', function() {
                if (this.value) {
                    const workType = this.options[this.selectedIndex].text;
                    reasonField.placeholder = `Please provide detailed reason for requesting ${workType}`;
                }
            });
        }
    });
</script>
@endsection