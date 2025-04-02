@php
    use App\Enums\Gender;
    use App\Enums\Department;
    use App\Enums\EmploymentType;
    use App\Enums\EmployeeStatus;
    use App\Enums\AttendanceStatus;
    use App\Enums\DepartmentTeam;

    $user = auth()->user();
    $employee = $user->employee;
    $educationInformation = $employee->educationInformation;
    $characterReferences = $employee->characterReferences;
    $familyInformation = $employee->familyInformation;
    $ojtInformation = $employee->ojtInformation;
@endphp

@extends('components.layout.auth')

@section('title') Profile @endsection

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Profile header with avatar and name -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6">
            <div class="flex-shrink-0">
                <div class="h-32 w-32 rounded-full bg-primary/10 flex items-center justify-center text-4xl font-bold text-primary">
                    {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                </div>
            </div>
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $employee->getFullName() }}</h1>
                        <p class="text-lg text-gray-600">{{ Department::getLabel($employee->department) }}</p>
                        <p class="text-md text-gray-500">{{ DepartmentTeam::getLabel($employee->department_team) }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-sm font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                            {{ EmployeeStatus::getLabel($employee->status) }}
                        </span>
                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-sm font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">
                            {{ EmploymentType::getLabel($employee->employment_type) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation tabs -->
    <div x-data="{ activeTab: 'personal' }" class="mb-6">
        <div class="flex flex-wrap gap-2 border-b border-gray-200">
            <button 
                @click="activeTab = 'personal'" 
                :class="activeTab === 'personal' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'" 
                class="px-4 py-3 font-medium text-sm border-b-2 transition-all">
                Personal Information
            </button>
            <button 
                @click="activeTab = 'education'" 
                :class="activeTab === 'education' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'" 
                class="px-4 py-3 font-medium text-sm border-b-2 transition-all">
                Education
            </button>
            <button 
                @click="activeTab = 'family'" 
                :class="activeTab === 'family' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'" 
                class="px-4 py-3 font-medium text-sm border-b-2 transition-all">
                Family
            </button>
            <button 
                @click="activeTab = 'references'" 
                :class="activeTab === 'references' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'" 
                class="px-4 py-3 font-medium text-sm border-b-2 transition-all">
                References
            </button>
            <button 
                @click="activeTab = 'ojt'" 
                :class="activeTab === 'ojt' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'" 
                class="px-4 py-3 font-medium text-sm border-b-2 transition-all">
                OJT Information
            </button>
            <button 
                @click="activeTab = 'logs'" 
                :class="activeTab === 'logs' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'" 
                class="px-4 py-3 font-medium text-sm border-b-2 transition-all">
                Action Logs
            </button>
        </div>

        <!-- Content panels -->
        <div class="mt-6">
            <!-- Personal Information -->
            <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                        <a href="" class="group">
                            <x-button text="Edit" size="sm" />
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Basic Information</h3>
                            <div class="mt-2 space-y-4">
                                <div>
                                    <span class="text-sm text-gray-500">Full Name</span>
                                    <p class="text-base font-medium">{{ $employee->getFullName() }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Gender</span>
                                    <p class="text-base">{{ Gender::getLabel($employee->gender) }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Birthdate</span>
                                    <p class="text-base">{{ $employee->birthdate ? date('F d, Y', strtotime($employee->birthdate)) : 'Not provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Contact Number</span>
                                    <p class="text-base">{{ $employee->contact_number ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Address</span>
                                    <p class="text-base">{{ $employee->address ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Employment Details</h3>
                            <div class="mt-2 space-y-4">
                                <div>
                                    <span class="text-sm text-gray-500">Department</span>
                                    <p class="text-base">{{ Department::getLabel($employee->department) }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Team</span>
                                    <p class="text-base">{{ DepartmentTeam::getLabel($employee->department_team) }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Employment Type</span>
                                    <p class="text-base">{{ EmploymentType::getLabel($employee->employment_type) }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Hire Date</span>
                                    <p class="text-base">{{ $employee->hire_date ? date('F d, Y', strtotime($employee->hire_date)) : 'Not provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Group Number</span>
                                    <p class="text-base">{{ $employee->group_number ?? 'Not assigned' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Emergency Contact</h3>
                            <div class="mt-2 space-y-4">
                                <div>
                                    <span class="text-sm text-gray-500">Name</span>
                                    <p class="text-base">{{ $employee->emergency_contact_name ?? 'Not provided' }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Contact Number</span>
                                    <p class="text-base">{{ $employee->emergency_contact_number ?? 'Not provided' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Education Information -->
            <div x-show="activeTab === 'education'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Education Information</h2>
                        <a href="" class="group">
                            <x-button text="Add Education" size="sm" />
                        </a>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <div class="py-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ $educationInformation->course }}</h3>
                                    <p class="text-md text-gray-600">{{ $educationInformation->university_name }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $educationInformation->university_address }}, 
                                        {{ $educationInformation->university_city }}, 
                                        {{ $educationInformation->university_province }}
                                        {{ $educationInformation->university_zip }}
                                    </p>
                                    <p class="text-sm text-gray-500 mt-1">Required Hours: {{ $educationInformation->required_hours }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="" class="group">
                                        <x-button text="Edit" size="xs" />
                                    </a>
                                    <form action="" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button text="Delete" size="xs" containerColor="red" type="submit" onclick="return confirm('Are you sure you want to delete this education record?')" />
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Information -->
            <div x-show="activeTab === 'family'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Family Information</h2>
                        @if(!isset($familyInformation))
                            <a href="" class="group">
                                <x-button text="Add Family Info" size="sm" />
                            </a>
                        @else
                            <a href="" class="group">
                                <x-button text="Edit Family Info" size="sm" />
                            </a>
                        @endif
                    </div>
                    
                    @if(isset($familyInformation))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Parents</h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <span class="text-sm text-gray-500">Father's Name</span>
                                        <p class="text-base">{{ $familyInformation->father_name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Father's Occupation</span>
                                        <p class="text-base">{{ $familyInformation->father_occupation ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Mother's Name</span>
                                        <p class="text-base">{{ $familyInformation->mother_name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Mother's Occupation</span>
                                        <p class="text-base">{{ $familyInformation->mother_occupation ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Number of Siblings</span>
                                        <p class="text-base">{{ $familyInformation->number_of_siblings ?? '0' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Marital Status</h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <span class="text-sm text-gray-500">Status</span>
                                        <p class="text-base">{{ $familyInformation->marital_status ? App\Enums\MaritalStatus::getLabel($familyInformation->marital_status) : 'Not provided' }}</p>
                                    </div>
                                    
                                    @if($familyInformation->marital_status && $familyInformation->marital_status->value == 'married')
                                        <div>
                                            <span class="text-sm text-gray-500">Spouse's Name</span>
                                            <p class="text-base">{{ $familyInformation->spouse_name ?? 'Not provided' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Spouse's Occupation</span>
                                            <p class="text-base">{{ $familyInformation->spouse_occupation ?? 'Not provided' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm text-gray-500">Number of Children</span>
                                            <p class="text-base">{{ $familyInformation->number_of_children ?? '0' }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No family information available.</p>
                            <a href="" class="text-primary hover:text-primary-dark mt-2 inline-block">
                                Add family information
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Character References -->
            <div x-show="activeTab === 'references'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Character References</h2>
                        <a href="" class="group">
                            <x-button text="Add Reference" size="sm" />
                        </a>
                    </div>
                    
                    @if(isset($characterReferences) && count($characterReferences) > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            @foreach($characterReferences as $reference)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $reference->name }}</h3>
                                            <p class="text-md text-gray-600">{{ $reference->position }} at {{ $reference->name_of_employer }}</p>
                                            <p class="text-sm text-gray-500 mt-1">Relationship: {{ $reference->relationship }}</p>
                                            <div class="mt-2">
                                                <p class="text-sm text-gray-500">Email: {{ $reference->email }}</p>
                                                <p class="text-sm text-gray-500">Contact: {{ $reference->contact_number }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="" class="group">
                                                <x-button text="Edit" size="xs" />
                                            </a>
                                            <form action="" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-button text="Delete" size="xs" containerColor="red" type="submit" onclick="return confirm('Are you sure you want to delete this reference?')" />
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No character references available.</p>
                            <a href="" class="text-primary hover:text-primary-dark mt-2 inline-block">
                                Add character reference
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- OJT Information -->
            <div x-show="activeTab === 'ojt'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">OJT Information</h2>
                        @if(!isset($ojtInformation))
                            <a href="" class="group">
                                <x-button text="Add OJT Info" size="sm" />
                            </a>
                        @else
                            <a href="" class="group">
                                <x-button text="Edit OJT Info" size="sm" />
                            </a>
                        @endif
                    </div>
                    
                    @if(isset($ojtInformation))
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">OJT Coordinator Details</h3>
                                <div class="mt-2 space-y-4">
                                    <div>
                                        <span class="text-sm text-gray-500">Coordinator Name</span>
                                        <p class="text-base">{{ $ojtInformation->coordinator_name ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Coordinator Email</span>
                                        <p class="text-base">{{ $ojtInformation->coordinator_email ?? 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Coordinator Phone</span>
                                        <p class="text-base">{{ $ojtInformation->coordinator_phone ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No OJT information available.</p>
                            <a href="" class="text-primary hover:text-primary-dark mt-2 inline-block">
                                Add OJT information
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Action Logs -->
            <div x-show="activeTab === 'logs'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Action Logs</h2>
                    </div>
                    
                    <div class="mb-4">
                        <form action="" method="GET">
                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                            <div class="flex flex-wrap gap-4">
                                <div class="w-full md:w-auto">
                                    <x-form.input type="date" name="date_from" label="Date From" value="{{ request('date_from') }}" />
                                </div>
                                <div class="w-full md:w-auto">
                                    <x-form.input type="date" name="date_to" label="Date To" value="{{ request('date_to') }}" />
                                </div>
                                <div class="w-full md:w-auto">
                                    <x-form.select name="action_type" label="Action Type">
                                        <option value="">All Actions</option>
                                        <option value="created" {{ request('action_type') == 'created' ? 'selected' : '' }}>Created</option>
                                        <option value="updated" {{ request('action_type') == 'updated' ? 'selected' : '' }}>Updated</option>
                                        <option value="deleted" {{ request('action_type') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                                    </x-form.select>
                                </div>
                                <div class="flex items-end">
                                    <x-button type="submit" text="Filter" size="md" />
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @if(isset($actionLogs) && count($actionLogs) > 0)
                                    @foreach($actionLogs as $log)
                                        <tr>
                                            <td class="py-3 px-4 text-sm text-gray-500">{{ date('M d, Y h:i A', strtotime($log->created_at)) }}</td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                                    @if($log->action_type === 'created')
                                                        bg-green-50 text-green-700 ring-green-600/20
                                                    @elseif($log->action_type === 'updated')
                                                        bg-blue-50 text-blue-700 ring-blue-600/20
                                                    @else
                                                        bg-red-50 text-red-700 ring-red-600/20
                                                    @endif
                                                ">
                                                    {{ ucfirst($log->action_type) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-gray-500">{{ $log->description }}</td>
                                            <td class="py-3 px-4 text-sm text-gray-500">{{ $log->user ? $log->user->name : 'System' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500">No action logs found.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        
                        @if(isset($actionLogs) && count($actionLogs) > 0)
                            <div class="mt-4">
                                {{ $actionLogs->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection