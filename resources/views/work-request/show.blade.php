@php
    use App\Enums\RequestStatus;
    use App\Enums\WorkType;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-8 px-6 max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Work Request Details</h1>
            <p class="mt-2 text-sm text-gray-600">Request ID: {{ $workRequest->id }}</p>
        </div>
        <div class="flex space-x-3">
            @can('update', $workRequest)
                <a href="{{ route('work-request.edit', $workRequest) }}">
                    <x-button text="Edit Request" containerColor="amber-100" contentColor="amber-700" />
                </a>
            @endcan
            <a href="{{ route('work-request.index') }}">
                <x-button text="Back to List" containerColor="gray-200" contentColor="gray-800" />
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-8">
        <div class="space-y-8">
            <!-- Status Banner -->
            <div class="rounded-lg p-4 flex items-center space-x-4
                @if($workRequest->isPending()) bg-yellow-50 border border-yellow-200
                @elseif($workRequest->isApproved()) bg-green-50 border border-green-200
                @elseif($workRequest->isRejected()) bg-red-50 border border-red-200
                @endif">
                <div class="flex-shrink-0">
                    @if($workRequest->isPending())
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif($workRequest->isApproved())
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @elseif($workRequest->isRejected())
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    @endif
                </div>
                <div>
                    <h3 class="font-medium
                        @if($workRequest->isPending()) text-yellow-800
                        @elseif($workRequest->isApproved()) text-green-800
                        @elseif($workRequest->isRejected()) text-red-800
                        @endif">
                        Status: {{ RequestStatus::getLabel($workRequest->status) }}
                    </h3>
                    <p class="text-sm
                        @if($workRequest->isPending()) text-yellow-700
                        @elseif($workRequest->isApproved()) text-green-700
                        @elseif($workRequest->isRejected()) text-red-700
                        @endif">
                        Last updated: {{ $workRequest->updated_at->format('M d, Y h:i A') }}
                        @if($workRequest->updatedBy)
                            by {{ $workRequest->updatedBy->getFullName() }}
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Request Details -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 border-b pb-3 mb-4">Request Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Employee Name</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $workRequest->employee->getFullName() }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Request Date</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $workRequest->request_date->format('M d, Y') }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Work Type</h3>
                        <p class="mt-1 text-base text-gray-900">{{ WorkType::getLabel($workRequest->work_type) }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Shift Request</h3>
                        <p class="mt-1 text-base text-gray-900">{{ $workRequest->shift_request }}</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500">Reason</h3>
                        <p class="mt-1 text-base text-gray-900 whitespace-pre-line">{{ $workRequest->reason }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Approval Documents -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 border-b pb-3 mb-4">Approval Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Team Leader Approval</h3>
                        @if($workRequest->proof_of_team_leader_approval)
                            <div class="mt-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-700">Document uploaded</span>
                                <button 
                                    type="button" 
                                    class="ml-2 text-sm text-primary hover:text-primary/80 font-medium"
                                    @click="openImagePreview('{{ asset('storage/' . $workRequest->proof_of_team_leader_approval) }}', 'Team Leader Approval')"
                                >
                                    Preview
                                </button>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-500">No document uploaded</p>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Group Leader Approval</h3>
                        @if($workRequest->proof_of_group_leader_approval)
                            <div class="mt-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-700">Document uploaded</span>
                                <button 
                                    type="button" 
                                    class="ml-2 text-sm text-primary hover:text-primary/80 font-medium"
                                    @click="openImagePreview('{{ asset('storage/' . $workRequest->proof_of_group_leader_approval) }}', 'Group Leader Approval')"
                                >
                                    Preview
                                </button>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-500">No document uploaded</p>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">School Approval</h3>
                        @if($workRequest->proof_of_school_approval)
                            <div class="mt-2 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-700">Document uploaded</span>
                                <button 
                                    type="button" 
                                    class="ml-2 text-sm text-primary hover:text-primary/80 font-medium"
                                    @click="openImagePreview('{{ asset('storage/' . $workRequest->proof_of_school_approval) }}', 'School Approval')"
                                >
                                    Preview
                                </button>
                            </div>
                        @else
                            <p class="mt-1 text-sm text-gray-500">Not applicable</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Admin Actions -->
            @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                @if($workRequest->isPending())
                    <div class="pt-6 border-t">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Administrator Actions</h2>
                        <div class="flex space-x-4">
                            <form action="{{ route('work-request.update', $workRequest) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ RequestStatus::APPROVED->value }}">
                                <x-button type="submit" text="Approve Request" containerColor="green-600" contentColor="white" />
                            </form>
                            
                            <form action="{{ route('work-request.update', $workRequest) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ RequestStatus::REJECTED->value }}">
                                <x-button type="submit" text="Reject Request" containerColor="red-600" contentColor="white" />
                            </form>
                        </div>
                    </div>
                @endif
            @endif
        </div>
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
    });
</script>
@endsection