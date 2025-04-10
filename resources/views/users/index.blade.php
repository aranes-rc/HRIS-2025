@php
    use App\Enums\UserRole;
@endphp

@extends('components.layout.auth')

@section('title') User Management @endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">User Management</h1>
            <p class="mt-2 text-lg text-gray-600">Manage all users in the system</p>
        </div>
        <a href="{{ route('users.create') }}" class="mt-4 sm:mt-0">
            <x-button text="Add New User" size="lg" />
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 bg-white p-6 rounded-lg shadow">
        <form action="{{ route('users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-form.input type="text" name="search" label="Search by Name or Email" 
                value="{{ request('search') }}" placeholder="Search users..." />
            
            <x-form.select name="role" label="Filter by Role">
                <option value="">All Roles</option>
                @foreach (UserRole::options() as $key => $value)
                    <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>{{ $value }}</option>
                @endforeach
            </x-form.select>

            <div class="flex items-end space-x-4">
                <x-button type="submit" text="Filter" size="md" class="mb-1" />
                <a href="{{ route('users.index') }}">
                    <x-button type="button" text="Reset" containerColor="gray-300" contentColor="gray-700" size="md" class="mb-1" />
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600">ID</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Roles</th>
                        <th scope="col" class="px-6 py-4 text-left text-sm font-semibold text-gray-600">Created</th>
                        <th scope="col" class="px-6 py-4 text-right text-sm font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if ($user->employee)
                                {{ $user->employee->getFullName() }}
                            @else
                                {{ $user->name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $role === UserRole::ADMIN->value ? 'bg-purple-100 text-purple-800' : 
                                           ($role === UserRole::HR->value ? 'bg-blue-100 text-blue-800' : 
                                            ($role === UserRole::TEAM_LEADER->value ? 'bg-green-100 text-green-800' : 
                                             ($role === UserRole::GROUP_LEADER->value ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ UserRole::getLabel(UserRole::from($role)) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="inline-block">
                                    <x-button text="Edit" size="xs" />
                                </a>
                                
                                <button type="button" @click="$dispatch('open-modal', 'reset-password-{{ $user->id }}')">
                                    <x-button text="Reset Password" size="xs" class="bg-accent3" containerColor="accent3" />
                                </button>
                                
                                <button type="button" @click="$dispatch('open-modal', 'delete-user-{{ $user->id }}')">
                                    <x-button text="Delete" size="xs" containerColor="red-500" />
                                </button>
                            </div>
                            
                            <!-- Reset Password Modal -->
                            <div
                                x-data="{ isOpen: false }"
                                x-show="isOpen"
                                @open-modal.window="if ($event.detail === 'reset-password-{{ $user->id }}') isOpen = true"
                                @close-modal.window="isOpen = false"
                                x-cloak
                                class="fixed inset-0 z-50 overflow-y-auto"
                                aria-labelledby="modal-title"
                                role="dialog"
                                aria-modal="true"
                            >
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="isOpen" @click="isOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                    
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                        <form action="{{-- route('users.password.reset', $user) --}}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <div>
                                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-5">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Reset Password for {{ $user->name }}
                                                    </h3>
                                                    <div class="mt-4 space-y-4">
                                                        <x-form.input type="password" name="password" label="New Password" required />
                                                        <x-form.input type="password" name="password_confirmation" label="Confirm New Password" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                                <x-button type="submit" text="Reset Password" class="w-full sm:col-start-2" />
                                                <x-button type="button" @click="isOpen = false" text="Cancel" containerColor="gray-300" contentColor="gray-700" class="w-full sm:col-start-1 mt-3 sm:mt-0" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete User Modal -->
                            <div
                                x-data="{ isOpen: false }"
                                x-show="isOpen"
                                @open-modal.window="if ($event.detail === 'delete-user-{{ $user->id }}') isOpen = true"
                                @close-modal.window="isOpen = false"
                                x-cloak
                                class="fixed inset-0 z-50 overflow-y-auto"
                                aria-labelledby="modal-title"
                                role="dialog"
                                aria-modal="true"
                            >
                                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="isOpen" @click="isOpen = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                                    
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    
                                    <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <div>
                                                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <div class="mt-3 text-center sm:mt-5">
                                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                        Delete User
                                                    </h3>
                                                    <div class="mt-2">
                                                        <p class="text-sm text-gray-500">
                                                            Are you sure you want to delete the user account for {{ $user->name }}? This action cannot be undone.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                                <x-button type="submit" text="Delete" containerColor="red-600" class="w-full sm:col-start-2" />
                                                <x-button type="button" @click="isOpen = false" text="Cancel" containerColor="gray-300" contentColor="gray-700" class="w-full sm:col-start-1 mt-3 sm:mt-0" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                            No users found. <a href="{{ route('users.create') }}" class="text-primary font-medium hover:underline">Create one?</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection