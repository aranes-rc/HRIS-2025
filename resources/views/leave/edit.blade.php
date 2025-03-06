@php
    use App\Enums\LeaveType;
    use App\Enums\RequestStatus;
    use App\Enums\ShiftType;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto" x-cloak x-data="{ previewImage: null, isPdf: false, showDeleteDialog: false }">
    <!-- Image Preview Modal -->
    <div x-show="previewImage" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         @click.self="previewImage = null">
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl max-h-[90vh] p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100">
            <button @click="previewImage = null" 
                    class="absolute -top-3 -right-3 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <template x-if="!isPdf">
                <img :src="previewImage" class="max-w-full max-h-[80vh] rounded-lg" alt="Document preview">
            </template>
            <template x-if="isPdf">
                <embed :src="previewImage" 
                       type="application/pdf" 
                       class="w-full h-[80vh] rounded-lg"
                       frameborder="0">
            </template>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Edit Leave Request</h1>
            <a href="{{ route('leave.index') }}">
                <x-button text="Back to List" containerColor="gray-500" />
            </a>
        </div>

        <form id="edit-form" action="{{ route('leave.update', $leave) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leave ID (uneditable) -->
                <div>
                    <x-form.label name="id" label="Leave Request ID" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $leave->id }}
                    </p>
                </div>

                <!-- Employee (uneditable) -->
                <div>
                    <x-form.label name="employee" label="Employee" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $leave->employee->getFullName() }}
                    </p>
                    <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leave Type -->
                <x-form.select name="leave_type" label="Leave Type" required x-data x-on:change="$dispatch('leave-type-changed', {value: $event.target.value})">
                    @foreach (LeaveType::options() as $key => $value)
                        <option value="{{ $key }}" {{ $leave->leave_type->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>

                <!-- Status (only for HR/Admin) -->
                @if(auth()->user()->isHr() || auth()->user()->isAdmin())
                    <x-form.select name="status" label="Status">
                        @foreach (RequestStatus::options() as $key => $value)
                            <option value="{{ $key }}" {{ $leave->status->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </x-form.select>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <x-form.input type="date" name="start_date" label="Start Date" value="{{ $leave->start_date->format('Y-m-d') }}" required />

                <!-- End Date -->
                <x-form.input type="date" name="end_date" label="End Date" value="{{ $leave->end_date->format('Y-m-d') }}" required />
            </div>

            <!-- Reason -->
            <div>
                <x-form.label name="reason" label="Reason for Leave" />
                <textarea 
                    name="reason" 
                    id="reason" 
                    rows="4" 
                    class="mt-2 block w-full rounded-md bg-white py-1.5 px-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-primary/60 sm:text-sm/6"
                    required
                >{{ $leave->reason }}</textarea>
                <x-form.error name="reason" />
            </div>

            <!-- Shift Covered -->
            <div class="mt-6">
                <x-form.label name="shift_covered" label="Shift Coverage" />
                <fieldset>
                    <legend class="sr-only">
                        Shift Covered
                    </legend>
                    <div class="space-y-5 mt-3">
                        @foreach (ShiftType::options() as $key => $value)
                            <div class="flex gap-3">
                                <div class="flex h-6 shrink-0 items-center">
                                    <div class="group grid size-4 grid-cols-1">
                                        <input
                                            value="{{ $key }}"
                                            class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-primary checked:bg-primary indeterminate:border-primary indeterminate:bg-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 forced-colors:appearance-auto"
                                            id="shift_covered"
                                            name="shift_covered[{{ $loop->index }}]"
                                            type="checkbox"
                                            {{ in_array($key, $leave->shift_covered) ? 'checked' : '' }} />
                                        <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25"
                                            fill="none" viewbox="0 0 14 14">
                                            <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            </path>
                                            <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-sm/6">
                                    <label class="font-medium text-gray-900" for="shift_covered[{{ $loop->index }}]">
                                        {{ $value }} shift
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </fieldset>
               
                <x-form.error name="shift_covered" />
            </div>

            <!-- Modified document links -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Proof of Leader Approval -->
                <x-form.file 
                    label="Proof of Leader Approval" 
                    name="proof_of_leader_approval" 
                    accept=".jpeg,.jpg,.png,.pdf"
                />
                @if($leave->proof_of_leader_approval)
                    <div class="mt-1 text-sm text-gray-600">
                        Current file: 
                        <a href="#" 
                        @click.prevent="previewImage = '{{ Storage::url($leave->proof_of_leader_approval) }}'; isPdf = '{{ Str::endsWith($leave->proof_of_leader_approval, '.pdf') }}'"
                        class="text-blue-500 hover:underline cursor-pointer">
                            View
                        </a>
                    </div>
                @endif

                <!-- Proof of Confirmed Designatory Tasks -->
                <x-form.file 
                    label="Proof of Confirmed Designatory Tasks" 
                    name="proof_of_confirmed_designatory_tasks" 
                    accept=".jpeg,.jpg,.png,.pdf"
                />
                @if($leave->proof_of_confirmed_designatory_tasks)
                    <div class="mt-1 text-sm text-gray-600">
                        Current file: 
                        <a href="#" 
                        @click.prevent="previewImage = '{{ Storage::url($leave->proof_of_confirmed_designatory_tasks) }}'; isPdf = '{{ Str::endsWith($leave->proof_of_confirmed_designatory_tasks, '.pdf') }}'"
                        class="text-blue-500 hover:underline cursor-pointer">
                            View
                        </a>
                    </div>
                @endif
            </div>

            <!-- Proof of Leave (only visible and required when leave type is sick) -->
            <div 
                x-data="{ showProof: '{{ $leave->leave_type->value }}' === '{{ LeaveType::ACADEMIC->value }}' }" 
                @leave-type-changed.window="showProof = $event.detail.value === '{{ LeaveType::ACADEMIC->value }}'"
                x-show="showProof"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
                class="bg-blue-50 p-4 rounded-md border border-blue-100"
            >
                <div class="flex items-center mb-3">
                    <svg class="h-5 w-5 text-blue-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <h3 class="text-blue-800 font-medium">Proof Documentation Required</h3>
                </div>
                
                <x-form.file 
                    label="Proof of Leave (Medical certificate, academic letter, school memo, etc.)" 
                    name="proof_of_leave" 
                    accept=".jpeg,.jpg,.png,.pdf"
                    x-bind:required="showProof"
                />

                @if($leave->proof_of_leave)
                    <div class="mt-1 text-sm text-blue-600">
                        Current file: 
                        <a href="#" 
                        @click.prevent="previewImage = '{{ Storage::url($leave->proof_of_leave) }}'; isPdf = '{{ Str::endsWith($leave->proof_of_leave, '.pdf') }}'"
                        class="text-blue-700 font-medium hover:underline cursor-pointer">
                            View
                        </a>
                    </div>
                @endif
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4 mt-8">
                <x-button type="button" 
                         text="Delete Request" 
                         containerColor="red-600" 
                         @click="showDeleteDialog = true" />
                
                <a href="{{ route('leave.index') }}">
                    <x-button type="button" text="Cancel" containerColor="gray-500" />
                </a>
                <x-button type="submit" text="Update Leave Request" />
            </div>
        </form>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div class="relative z-50">
        <!-- Dialog Backdrop -->
        <div x-show="showDeleteDialog" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm">
        </div>

        <!-- Dialog Content -->
        <div x-show="showDeleteDialog"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            class="fixed inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-6">
                <h3 class="text-lg font-semibold text-red-600">Delete Leave Request</h3>
                <p class="mt-2 text-gray-600">Are you sure you want to delete this leave request? This action cannot be undone.</p>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <x-button type="button" 
                            text="Cancel" 
                            containerColor="gray-200" 
                            contentColor="gray-700"
                            @click="showDeleteDialog = false" />
                    
                    <form id="deleteForm" method="POST" action="{{ route('leave.destroy', $leave) }}">
                        @csrf
                        @method('DELETE')
                        <x-button type="submit" 
                                text="Delete Permanently" 
                                containerColor="red-600" 
                                contentColor="white" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById("edit-form").addEventListener("submit", function(event) {
        const checkboxes = document.querySelectorAll('input[id="shift_covered"]');
        const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
    
        if (!isChecked) {
            alert("You must check at least one shift coverage option.");
            event.preventDefault(); // Prevent form submission
        }
    });
</script>
@endsection