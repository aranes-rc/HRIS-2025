@php
    use App\Enums\DocumentType;
    use App\Enums\RequestStatus;
@endphp

@extends('components.layout.auth')

@section('title') Document #{{ $document->id }} @endsection

@section('content')
<div class="py-8 px-6 sm:px-8 lg:px-10 max-w-4xl mx-auto">
    <div class="flex flex-col space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">View Document</h1>
                <p class="mt-2 text-gray-600">
                    {{ DocumentType::getLabel($document->document_type) }} - 
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($document->status->value === RequestStatus::APPROVED->value)
                            bg-green-100 text-green-800
                        @elseif($document->status->value === RequestStatus::REJECTED->value)
                            bg-red-100 text-red-800
                        @elseif($document->status->value === RequestStatus::PENDING->value)
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-gray-100 text-gray-800
                        @endif
                    ">
                        {{ RequestStatus::getLabel($document->status) }}
                    </span>
                </p>
            </div>
            
            <div class="flex space-x-3">
                @can('update', $document)
                <a href="{{ route('document.edit', $document) }}">
                    <x-button text="Edit Document" containerColor="blue" />
                </a>
                @endcan
                
                <a href="{{ route('document.index') }}">
                    <x-button text="Back to Documents" containerColor="gray" contentColor="gray-800" />
                </a>
            </div>
        </div>
        
        <!-- Document Preview -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Document Preview</h2>
            
            <div class="aspect-[16/9] w-full bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                @php
                    $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                    $isPdf = strtolower($extension) === 'pdf';
                @endphp
                
                @if($isImage)
                    <img src="{{ Storage::url($document->file_path) }}" alt="Document Preview" class="max-w-full max-h-full object-contain" />
                @elseif($isPdf)
                    <div class="text-center p-8">
                        <svg class="size-20 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600">PDF document</p>
                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="mt-4 inline-block">
                            <x-button text="Open PDF" containerColor="blue" />
                        </a>
                    </div>
                @else
                    <div class="text-center p-8">
                        <svg class="size-20 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-4 text-gray-600">{{ strtoupper($extension) }} document</p>
                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="mt-4 inline-block">
                            <x-button text="Download Document" containerColor="blue" />
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="mt-4 text-right">
                <a href="{{ Storage::url($document->file_path) }}" download class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <svg class="size-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download Original File
                </a>
            </div>
        </div>
        
        <!-- Document Details -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Document Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Document Type</h3>
                    <p class="mt-1 text-gray-900">{{ DocumentType::getLabel($document->document_type) }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Document ID</h3>
                    <p class="mt-1 text-gray-900">{{ $document->id }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($document->status->value === RequestStatus::APPROVED->value)
                                bg-green-100 text-green-800
                            @elseif($document->status->value === RequestStatus::REJECTED->value)
                                bg-red-100 text-red-800
                            @elseif($document->status->value === RequestStatus::PENDING->value)
                                bg-yellow-100 text-yellow-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ RequestStatus::getLabel($document->status) }}
                        </span>
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">File Name</h3>
                    <p class="mt-1 text-gray-900">{{ basename($document->file_path) }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Uploaded By</h3>
                    <p class="mt-1 text-gray-900">{{ $document->employee->getFullName() }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Last Updated By</h3>
                    <p class="mt-1 text-gray-900">{{ $document->updatedBy->getFullName() }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                    <p class="mt-1 text-gray-900">{{ $document->created_at->format('M d, Y h:i A') }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                    <p class="mt-1 text-gray-900">{{ $document->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            @can('update', $document)
            <a href="{{ route('document.edit', $document) }}">
                <x-button text="Edit Document" containerColor="blue" />
            </a>
            @endcan
            
            @can('delete', $document)
            <form action="{{ route('document.destroy', $document) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this document? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <x-button type="submit" text="Delete Document" containerColor="red" />
            </form>
            @endcan
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('documentView', () => ({
            init() {
                // Initialize any Alpine.js functionality here
            }
        }))
    })
</script>
@endsection