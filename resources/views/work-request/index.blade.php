@php
    use App\Enums\RequestStatus;
    use App\Enums\ShiftRequest;
    use App\Enums\WorkType;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-8 px-6 max-w-7xl mx-auto">
    <div class="flex flex-col space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Work Requests</h1>
            
            @can('create', App\Models\WorkRequest::class)
            <a href="{{ route('work-request.create') }}">
                <x-button text="New Request" size="lg" containerColor="primary" />
            </a>
            @endcan
        </div>
        
        <!-- Filters -->
        <div x-data="{ open: false }" class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-700">Filters</h2>
                <button @click="open = !open" class="text-primary hover:text-primary/80">
                    <span x-show="!open">Show Filters</span>
                    <span x-show="open">Hide Filters</span>
                </button>
            </div>
            
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform -translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-4"
                 class="mt-6">
                <form action="{{ route('work-request.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Status Filter -->
                    <x-form.select name="status" label="Status">
                        <option value="">All Statuses</option>
                        @foreach (RequestStatus::options() as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </x-form.select>
                    
                    <!-- Work Type Filter -->
                    <x-form.select name="work_type" label="Work Type">
                        <option value="">All Types</option>
                        @foreach (WorkType::options() as $key => $value)
                            <option value="{{ $key }}" {{ request('work_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </x-form.select>
                    
                    <!-- Date Range -->
                    <x-form.input type="date" name="date_from" label="From Date" value="{{ request('date_from') }}" />
                    <x-form.input type="date" name="date_to" label="To Date" value="{{ request('date_to') }}" />
                    
                    <div class="flex space-x-4 items-end md:col-span-2 lg:col-span-4">
                        <x-button type="submit" text="Apply Filters" containerColor="primary" />
                        <a href="{{ route('work-request.index') }}">
                            <x-button type="button" text="Clear" containerColor="gray-200" contentColor="gray-700" />
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            @if($workRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left text-gray-600">
                                @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                                    <th class="px-6 py-4 font-medium">Employee</th>
                                @endif
                                <th class="px-6 py-4 font-medium">Request Date</th>
                                <th class="px-6 py-4 font-medium">Work Type</th>
                                <th class="px-6 py-4 font-medium">Shift Request</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($workRequests as $workRequest)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                                        <td class="px-6 py-4">{{ $workRequest->employee->getFullName() }}</td>
                                    @endif
                                    <td class="px-6 py-4">{{ $workRequest->request_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4">{{ WorkType::getLabel($workRequest->work_type) }}</td>
                                    <td class="px-6 py-4">{{ ShiftRequest::getLabel($workRequest->shift_request) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($workRequest->isPending()) bg-yellow-100 text-yellow-800
                                            @elseif($workRequest->isApproved()) bg-green-100 text-green-800
                                            @elseif($workRequest->isRejected()) bg-red-100 text-red-800
                                            @endif">
                                            {{ RequestStatus::getLabel($workRequest->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 flex space-x-2">
                                        @can('view', $workRequest)
                                            <a href="{{ route('work-request.show', $workRequest) }}">
                                                <x-button text="View" size="xs" containerColor="blue-100" contentColor="blue-700" />
                                            </a>
                                        @endcan
                                        
                                        @can('update', $workRequest)
                                            <a href="{{ route('work-request.edit', $workRequest) }}">
                                                <x-button text="Edit" size="xs" containerColor="amber-100" contentColor="amber-700" />
                                            </a>
                                        @endcan
                                        
                                        @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                                            @if($workRequest->isPending())
                                                <form action="{{ route('work-request.update', $workRequest) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ RequestStatus::APPROVED->value }}">
                                                    <x-button type="submit" text="Approve" size="xs" containerColor="green-100" contentColor="green-700" />
                                                </form>
                                                
                                                <form action="{{ route('work-request.update', $workRequest) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="{{ RequestStatus::REJECTED->value }}">
                                                    <x-button type="submit" text="Reject" size="xs" containerColor="red-100" contentColor="red-700" />
                                                </form>
                                            @endif
                                        @endif
                                        
                                        @can('delete', $workRequest)
                                            <form action="{{ route('work-request.destroy', $workRequest) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                @csrf
                                                @method('DELETE')
                                                <x-button type="submit" text="Delete" size="xs" containerColor="red-100" contentColor="red-700" />
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $workRequests->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-gray-100 text-gray-500 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625a1.125 1.125 0 01-1.125-1.125V2.25" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No work requests found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(auth()->user()->isEmployee())
                            You haven't created any work requests yet.
                        @else
                            No work requests match your filters.
                        @endif
                    </p>
                    
                    @can('create', App\Models\WorkRequest::class)
                        <div class="mt-6">
                            <a href="{{ route('work-request.create') }}">
                                <x-button text="Create New Request" containerColor="primary" />
                            </a>
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('workRequestFilters', () => ({
            open: false,
            toggle() {
                this.open = !this.open;
            }
        }));
    });
</script>
@endsection