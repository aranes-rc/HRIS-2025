@php
    use App\Enums\DocumentType;
@endphp

@extends('components.layout.auth')

@section('title') Create Document @endsection

@section('content')
<div class="py-8 px-6 sm:px-8 lg:px-10 max-w-3xl mx-auto">
    <div class="flex flex-col space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Upload New Document</h1>
                <p class="mt-2 text-gray-600">Submit a new document for approval</p>
            </div>
            
            <div>
                <a href="{{ route('document.index') }}">
                    <x-button text="Back to Documents" containerColor="gray" contentColor="gray-800" />
                </a>
            </div>
        </div>
        
        <!-- Upload Form -->
        <div class="bg-white rounded-xl shadow-sm p-8 border border-gray-100">
            <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Document Type -->
                <x-form.select name="document_type" label="Document Type" required>
                    <option value="" selected disabled hidden>Select document type</option>
                    @foreach(DocumentType::options() as $key => $value)
                        <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </x-form.select>
                
                <!-- Document File -->
                <x-form.file label="Document File" name="document_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required />
                
                <div class="pt-4 flex justify-end space-x-3">
                    <a href="{{ route('document.index') }}">
                        <x-button text="Cancel" containerColor="gray" contentColor="gray-800" />
                    </a>
                    <x-button type="submit" text="Upload Document" />
                </div>
            </form>
        </div>
        
        <!-- Notes -->
        <div class="bg-amber-50 rounded-xl p-6 border border-amber-100">
            <h3 class="text-lg font-semibold text-amber-800">Important Notes</h3>
            <ul class="mt-2 space-y-2 text-amber-700 list-disc list-inside">
                <li>Maximum file size: 10MB</li>
                <li>Supported file formats: PDF, DOC, DOCX, JPG, JPEG, PNG</li>
                <li>All documents require approval before they are considered valid</li>
                <li>Please ensure all information in your documents is accurate and legible</li>
            </ul>
        </div>
    </div>
</div>
@endsection