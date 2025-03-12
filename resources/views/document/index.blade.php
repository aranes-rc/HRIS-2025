@php
    use App\Enums\DocumentType;
    use App\Enums\RequestStatus;
@endphp

@extends('components.layout.auth')

@section('title') Document Dashboard @endsection

@section('content')
<div class="py-8 px-6 sm:px-8 lg:px-10 max-w-7xl mx-auto">
    <div class="flex flex-col space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Documents</h1>
                <p class="mt-2 text-gray-600">Manage your document submissions and approvals</p>
            </div>
            
            @can('create', App\Models\Document::class)
            <div>
                <a href="{{ route('document.create') }}">
                    <x-button text="Upload New Document" size="lg" />
                </a>
            </div>
            @endcan
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <form action="{{ route('document.index') }}" method="GET" class="space-y-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Filters</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Document Type Filter -->
                    <x-form.select name="document_type" label="Document Type" selected="{{ request('document_type') }}">
                        <option value="">All Types</option>
                        @foreach(DocumentType::options() as $key => $value)
                            <option value="{{ $key }}" {{ request('document_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </x-form.select>
                    
                    <!-- Status Filter -->
                    <x-form.select name="status" label="Status" selected="{{ request('status') }}">
                        <option value="">All Statuses</option>
                        @foreach(RequestStatus::options() as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </x-form.select>
                </div>
                
                <div class="flex justify-end">
                    <x-button type="submit" text="Apply Filters" containerColor="primary" />
                </div>
            </form>
        </div>
        
        <!-- Documents List -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Employee</th>
                            @endif
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Document Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Last Updated</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Updated By</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($documents as $document)
                            <tr class="hover:bg-gray-50 transition duration-200">
                                @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $document->employee->getFullName() }}
                                </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ DocumentType::getLabel($document->document_type) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $document->updated_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $document->updatedBy->getFullName() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('document.show', $document) }}">
                                            <x-button text="View" size="xs" />
                                        </a>
                                        
                                        @can('update', $document)
                                        <a href="{{ route('document.edit', $document) }}">
                                            <x-button text="Edit" size="xs" class="bg-green-600" containerColor="green-600" />
                                        </a>
                                        @endcan
                                        
                                        @can('delete', $document)
                                        <form action="{{ route('document.destroy', $document) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this document?');">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" text="Delete" size="xs" class="bg-red-600" containerColor="red-600" />
                                        </form>
                                        @endcan
                                        
                                        @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                                            @if($document->isPending())
                                                <form action="{{ route('document.update', $document) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ RequestStatus::APPROVED->value }}">
                                                    <x-button type="submit" text="Approve" size="xs" containerColor="green-100" contentColor="green-700" />
                                                </form>
                                                
                                                <form action="{{ route('document.update', $document) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ RequestStatus::REJECTED->value }}">
                                                    <x-button type="submit" text="Reject" size="xs" containerColor="red-100" contentColor="red-700" />
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ (auth()->user()->isAdmin() || auth()->user()->isHr()) ? '6' : '5' }}" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="size-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-4 text-lg font-medium">No documents found</p>
                                        <p class="mt-1">Upload a new document to get started</p>
                                        
                                        @can('create', App\Models\Document::class)
                                        <a href="{{ route('document.create') }}" class="mt-4">
                                            <x-button text="Upload New Document" />
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('documentsPage', () => ({
            init() {
                // Initialize any Alpine.js functionality here
            }
        }))
    })
</script>
@endsection