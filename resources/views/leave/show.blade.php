@php
use App\Enums\LeaveType;
use App\Enums\LeaveStatus;
use App\Enums\ShiftType;
use Illuminate\Support\Str;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto" x-cloak x-data="{ previewImage: null, isPdf: false }">
    <!-- Image Preview Modal -->
    <div x-show="previewImage" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         @click.self="previewImage = null">
        <div class="relative bg-white rounded-lg shadow-xl max-w-4xl max-h-[90vh] p-4">
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
            <h1 class="text-2xl font-bold text-gray-800">Leave Request Details</h1>
            <a href="{{ route('leave.index') }}">
                <x-button text="Back to List" containerColor="gray-500" />
            </a>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leave ID -->
                <div>
                    <x-form.label name="id" label="Leave Request ID" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $leave->id }}
                    </p>
                </div>

                <!-- Employee -->
                <div>
                    <x-form.label name="employee" label="Employee" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $leave->employee->getFullName() }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Leave Type -->
                <div>
                    <x-form.label name="leave_type" label="Leave Type" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ LeaveType::getLabel($leave->leave_type) }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <x-form.label name="status" label="Status" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ LeaveStatus::getLabel($leave->status) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dates -->
                <div>
                    <x-form.label name="start_date" label="Start Date" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $leave->start_date->format('M d, Y') }}
                    </p>
                </div>
                
                <div>
                    <x-form.label name="end_date" label="End Date" />
                    <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                        {{ $leave->end_date->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Reason -->
            <div>
                <x-form.label name="reason" label="Reason for Leave" />
                <p class="mt-2 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 whitespace-pre-line">
                    {{ $leave->reason }}
                </p>
            </div>

            <!-- Shift Covered -->
            <div class="mt-6">
                <x-form.label name="shift_covered" label="Shift Coverage" />
                <div class="space-y-5 mt-3">
                    @foreach (ShiftType::options() as $key => $value)
                        <div class="flex gap-3 items-center">
                            <div class="flex h-6 items-center">
                                <input type="checkbox" 
                                       class="w-4 h-4 text-primary border-gray-300 rounded" 
                                       disabled
                                       {{ in_array($key, $leave->shift_covered) ? 'checked' : '' }}>
                            </div>
                            <div class="text-sm/6">
                                <span class="font-medium text-gray-900">
                                    {{ $value }} shift
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Document Previews -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach (['proof_of_leader_approval', 'proof_of_confirmed_designatory_tasks', 'proof_of_leave'] as $doc)
                    @if($leave->$doc)
                        <div class="mt-4">
                            <x-form.label name="{{ $doc }}" label="{{ Str::title(str_replace('_', ' ', $doc)) }}" />
                            <div class="mt-2">
                                @if(Str::endsWith($leave->$doc, ['.jpg', '.jpeg', '.png']))
                                    <div class="group relative cursor-pointer" 
                                         @click="previewImage = '{{ Storage::url($leave->$doc) }}'; isPdf = false">
                                        <img src="{{ Storage::url($leave->$doc) }}" 
                                             class="h-32 w-full object-cover rounded-lg border-2 border-gray-200 group-hover:border-primary transition-colors">
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span class="text-white font-medium">Click to Preview</span>
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ Storage::url($leave->$doc) }}" 
                                       download
                                       class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download File
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection