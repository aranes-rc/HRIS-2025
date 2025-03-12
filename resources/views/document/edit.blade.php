@php
    use App\Enums\DocumentType;
    use App\Enums\RequestStatus;
@endphp

@extends('components.layout.auth')

@section('title') Edit Document #{{ $document->id }} @endsection

@section('content')
<div class="py-8 px-6 sm:px-8 lg:px-10 max-w-3xl mx-auto">
    <div class="flex flex-col space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Document</h1>
                <p class="mt-2 text-gray-600">Update document information or upload a new file</p>
            </div>
            
            <div>
                <a href="{{ route('document.index') }}">
                    <x-button text="Back to Documents" containerColor="gray" contentColor="gray-800" />
                </a>
            </div>
        </div>
        
        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
            <form action="{{ route('document.update', $document) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Document ID -->
                <x-form.input type="text" name="document_id" label="Document ID" value="{{ $document->id }}" disabled readonly />
                
                <!-- Document Type -->
                <x-form.select name="document_type" label="Document Type" required>
                    @foreach(DocumentType::options() as $key => $value)
                        <option value="{{ $key }}" {{ $document->document_type->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
                
                @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                <!-- Status (Only for Admin and HR) -->
                <x-form.select name="status" label="Status" required>
                    @foreach(RequestStatus::options() as $key => $value)
                        <option value="{{ $key }}" {{ $document->status->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
                @endif
                
                <!-- Current File -->
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="font-medium text-gray-700">Current File</h3>
                    <div class="mt-2 flex items-center">
                        <svg class="size-6 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm">{{ basename($document->file_path) }}</span>
                    </div>
                </div>
                
                <!-- Upload New File -->
                <x-form.file label="Upload New File (Optional)" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                
                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('document.index') }}">
                        <x-button text="Cancel" containerColor="gray" contentColor="gray-800" />
                    </a>
                    <x-button type="submit" text="Update Document" />
                </div>
            </form>
        </div>
        
        <!-- Document History -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Document Details</h3>
            
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="flex flex-col">
                    <span class="text-gray-500">Uploaded By</span>
                    <span class="font-medium">{{ $document->employee->getFullName() }}</span>
                </div>
                
                <div class="flex flex-col">
                    <span class="text-gray-500">Created At</span>
                    <span class="font-medium">{{ $document->created_at->format('M d, Y h:i A') }}</span>
                </div>
                
                <div class="flex flex-col">
                    <span class="text-gray-500">Last Updated By</span>
                    <span class="font-medium">{{ $document->updatedBy->getFullName() }}</span>
                </div>
                
                <div class="flex flex-col">
                    <span class="text-gray-500">Last Updated</span>
                    <span class="font-medium">{{ $document->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection