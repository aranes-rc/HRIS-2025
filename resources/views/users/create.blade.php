@php
    use App\Enums\UserRole;
@endphp

@extends('components.layout.auth')

@section('title') Create New User @endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
        <p class="mt-2 text-lg text-gray-600">Add a new user to the system</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('users.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h2>
                </div>
                
                <x-form.input type="text" name="name" label="Full Name" required autofocus />
                <x-form.input type="email" name="email" label="Email Address" required />
                
                <div class="col-span-2">
                    <x-form.label name="roles" label="User Roles" />
                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach (UserRole::values() as $role)
                            <div class="flex items-center">
                                <input id="role-{{ $role }}" name="roles[]" value="{{ $role }}" type="checkbox" 
                                    class="h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                                <label for="role-{{ $role }}" class="ml-2 text-gray-700">
                                    {{ UserRole::getLabel(UserRole::from($role)) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <x-form.error name="roles" />
                </div>
                
                <!-- Password Section -->
                <div class="col-span-2">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 mt-4">Set Password</h2>
                </div>
                
                <x-form.input type="password" name="password" label="Password" required />
                <x-form.input type="password" name="password_confirmation" label="Confirm Password" required />
                
                <!-- Temporary User Toggle -->
                <div class="col-span-2 mt-4">
                    <div x-data="{ temporary: false }">
                        <div class="flex items-center">
                            <input id="temporary" name="temporary" type="checkbox" 
                                x-model="temporary"
                                class="h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                            <label for="temporary" class="ml-2 text-gray-700 font-medium">
                                Create as temporary user
                            </label>
                        </div>
                        
                        <div x-show="temporary" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-form.input type="date" name="expiry_date" label="Account Expiry Date" 
                                x-bind:required="temporary" min="{{ now()->addDay()->format('Y-m-d') }}" />
                                
                            <div>
                                <x-form.label name="expiry_action" label="Action After Expiry" />
                                <div class="mt-2 grid grid-cols-1">
                                    <select name="expiry_action" id="expiry_action" x-bind:required="temporary"
                                        class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-2 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 invalid:outline-red-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                                        <option value="disable">Disable Account</option>
                                        <option value="delete">Delete Account</option>
                                    </select>
                                    <svg aria-hidden="true"
                                        class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4"
                                        data-slot="icon" fill="currentColor" viewbox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path clip-rule="evenodd"
                                            d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z"
                                            fill-rule="evenodd">
                                        </path>
                                    </svg>
                                </div>
                                <x-form.error name="expiry_action" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pt-5 mt-8 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('users.index') }}">
                    <x-button type="button" text="Cancel" containerColor="gray-300" contentColor="gray-700" />
                </a>
                <x-button type="submit" text="Create User" />
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userData', () => ({
            temporary: false
        }))
    })
</script>
@endsection