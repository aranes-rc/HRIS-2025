@php
    use App\Enums\LeaveStatus;
    use App\Enums\LeaveType;
@endphp

@extends('components.layout.root')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Leave Requests</h1>
        
        @if(auth()->user()->isEmployee() || auth()->user()->isHr() || auth()->user()->isAdmin())
            <a href="{{ route('leave.create') }}">
                <x-button
                    text="New Request"
                    containerColor="primary"
                    contentColor="white"
                    size="md"
                    roundness="md"
                />
            </a>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm rounded-lg p-4 mb-6">
        <form action="{{ route('leave.index') }}" method="GET" class="space-y-4 sm:flex sm:flex-wrap sm:space-y-0 sm:gap-3">
            <div class="sm:w-auto">
                <label for="leave_type" class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                <select id="leave_type" name="leave_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">All Types</option>
                    @foreach (LeaveType::options() as $key => $value)
                        <option value="{{ $key }}" {{ request('leave_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:w-auto">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                    @foreach (LeaveStatus::options() as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:w-auto">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>

            <div class="sm:w-auto">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>

            @if(auth()->user()->isAdmin() || auth()->user()->isHr())
            <div class="sm:w-auto">
                <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                <select id="employee_id" name="employee_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">All Employees</option>
                    @foreach (\App\Models\Employee::orderBy('id')->get() as $employee)
                        <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->getFullName() }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="self-end mt-4 sm:mt-0">
                <x-button
                    text="Filter"
                    containerColor="primary"
                    contentColor="white"
                    size="md"
                    roundness="md"
                    type="submit"
                />
            </div>
        </form>
    </div>

    <!-- Leave Requests Table -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden" x-data="{ showDetails: null }">
        <div class="min-w-full divide-y divide-gray-200">
            <div class="bg-gray-50">
                <div class="grid grid-cols-7 gap-2 px-4 py-3 text-xs sm:text-sm font-medium text-gray-500 uppercase tracking-wider">
                    <div>Employee</div>
                    <div>Leave Type</div>
                    <div>Period</div>
                    <div>Duration</div>
                    <div>Status</div>
                    <div>Created</div>
                    <div class="text-right">Actions</div>
                </div>
            </div>
            <div class="bg-white divide-y divide-gray-200">
                @forelse($leave_requests as $request)
                <div class="hover:bg-gray-50 transition-colors duration-150">
                    <div class="grid grid-cols-7 gap-2 px-4 py-3 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $request->employee->getFullName() }}</div>
                        <div class="text-sm text-gray-600">{{ LeaveType::getLabel($request->leave_type) }}</div>
                        <div class="text-sm text-gray-600">
                            {{ $request->start_date->format('M d, Y') }} - {{ $request->end_date->format('M d, Y') }}
                        </div>
                        <div class="text-sm text-gray-600">
                            {{ $request->start_date->diffInDays($request->end_date) + 1 }} days
                        </div>
                        <div>
                            <span @class([
                                'px-2 py-1 text-xs font-medium rounded-full',
                                'bg-yellow-100 text-yellow-800' => $request->status === LeaveStatus::PENDING,
                                'bg-green-100 text-green-800' => $request->status === LeaveStatus::APPROVED,
                                'bg-red-100 text-red-800' => $request->status === LeaveStatus::REJECTED,
                            ])>
                                {{ LeaveStatus::getLabel($request->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600">{{ $request->created_at->format('M d, Y') }}</div>
                        <div class="text-right space-x-2">
                            <button @click="showDetails = showDetails === {{ $request->id }} ? null : {{ $request->id }}" class="text-indigo-600 hover:text-indigo-900">
                                <span x-text="showDetails === {{ $request->id }} ? 'Hide' : 'View'"></span>
                            </button>

                            @if(auth()->user()->isAdmin() || 
                                (auth()->user()->isHr()) || 
                                (auth()->user()->isEmployee() && $request->employee_id === auth()->user()->employee->id && $request->status === LeaveStatus::PENDING))
                                <a href="{{ route('leave.edit', $request) }}" class="text-blue-600 hover:text-blue-900">
                                    Edit
                                </a>
                            @endif
                            
                            @if(auth()->user()->isAdmin() || auth()->user()->isHr())
                                @if($request->status === LeaveStatus::PENDING)
                                <form action="{{ route('leave.update.status', $request) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ LeaveStatus::APPROVED->value }}">
                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                        Approve
                                    </button>
                                </form>
                                
                                <form action="{{ route('leave.update.status', $request) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ LeaveStatus::REJECTED->value }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Reject
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </div>
                    
                    <!-- Expandable Details Section -->
                    <div x-show="showDetails === {{ $request->id }}" x-cloak x-transition class="px-4 py-3 bg-gray-50 text-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Request Details</h4>
                                <p class="mb-1"><span class="font-medium">Reason:</span> {{ $request->reason }}</p>
                                <p class="mb-1"><span class="font-medium">ID #:</span> {{ $request->id }}</p>
                                @if($request->shift_covered && count($request->shift_covered) > 0)
                                    <p class="mb-1">
                                        <span class="font-medium">Shift Coverage:</span>
                                        <ul class="list-disc list-inside ml-2 mt-1">
                                            @foreach($request->shift_covered as $shift)
                                                <li>{{ $shift }}</li>
                                            @endforeach
                                        </ul>
                                    </p>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-700 mb-2">Approval Information</h4>
                                @if($request->status !== LeaveStatus::PENDING)
                                    <p class="mb-1"><span class="font-medium">Updated:</span> {{ $request->updated_at->format('M d, Y h:i A') }}</p>
                                    <p class="mb-1"><span class="font-medium">Updated By:</span> {{ $request->updated_by ?? 'System' }}</p>
                                @else
                                    <p class="text-yellow-600">Awaiting approval</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-4 py-6 text-center text-gray-500">
                    <p>No leave requests found.</p>
                    <a href="{{ route('leave.create') }}" class="text-indigo-600 hover:text-indigo-900 mt-2 inline-block">
                        Create your first leave request
                    </a>
                </div>
                @endforelse
            </div>
        </div>
        
        <div class="px-4 py-4 bg-white border-t border-gray-200">
            {{ $leave_requests->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leaveRequestsDashboard', () => ({
            showDetails: null,
            toggleDetails(id) {
                this.showDetails = this.showDetails === id ? null : id;
            }
        }))
    })
</script>
@endsection