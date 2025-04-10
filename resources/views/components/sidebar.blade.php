@php
    use App\Enums\UserRole;
@endphp

<div 
    x-cloak
    x-data="{ 
        open: true,
        showRoleModal: false
    }" 
    class="bg-slate-800 text-white flex-shrink-0 transition-all duration-300 overflow-hidden h-screen"
    :class="open ? 'w-64' : 'w-20'"
>
    <!-- Header/Logo area -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700">
        <div class="flex items-center overflow-hidden">
            <div class="flex-shrink-0" :class="open ? 'w-full' : 'w-8'">
                <img src="{{ asset('img/roc-logo.png') }}" alt="ROC.PH" class="h-auto max-h-10">
            </div>
        </div>
        <button @click="open = !open" class="text-gray-400 hover:text-white">
            <svg x-show="open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
        </button>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="py-4">
        <ul>
            <!-- Dashboard -->
            <li class="mb-1">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center py-2 px-4 {{ (request()->routeIs('dashboard') || request()->routeIs('dashboard.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }} rounded-md transition-colors">
                    <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Dashboard</span>
                </a>
            </li>
            
            <!-- Attendance -->
            <li class="mb-1">
                <a href="{{ route('attendance.index') }}" 
                   class="flex items-center py-2 px-4 {{ (request()->routeIs('attendance') || request()->routeIs('attendance.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }} rounded-md transition-colors">
                    <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Attendance</span>
                </a>
            </li>
            
            <!-- Leave Request -->
            <li class="mb-1">
                <a href="{{ route('leave.index') }}" 
                   class="flex items-center py-2 px-4 {{ (request()->routeIs('leave') || request()->routeIs('leave.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }} rounded-md transition-colors">
                    <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Leave Request</span>
                </a>
            </li>
            
            <!-- Document -->
            <li class="mb-1">
                <a href="{{ route('document.index') }}" 
                   class="flex items-center py-2 px-4 {{ (request()->routeIs('document') || request()->routeIs('document.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }} rounded-md transition-colors">
                    <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Document</span>
                </a>
            </li>
            
            <!-- Work Requests -->
            <li class="mb-1">
                <a href="{{ route('work-request.index') }}" 
                   class="flex items-center py-2 px-4 {{ (request()->routeIs('work-request') || request()->routeIs('work-request.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }} rounded-md transition-colors">
                    <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Work Requests</span>
                </a>
            </li>
            
            @if(auth()->user()->getActiveRole() == UserRole::ADMIN->value)
                <!-- Work Requests -->
                <li class="mb-1">
                    <a href="{{ route('users.index') }}" 
                    class="flex items-center py-2 px-4 {{ (request()->routeIs('users') || request()->routeIs('users.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }} rounded-md transition-colors">
                        <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21" stroke="currentColor">
                            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M10.5 2.5a3 3 0 0 1 3 3v2a3 3 0 1 1-6 0v-2a3 3 0 0 1 3-3m7 14v-.728c0-3.187-3.686-5.272-7-5.272s-7 2.085-7 5.272v.728a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1"/>
                        </svg>
                        <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Manage Users</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>
    
    <!-- User Profile and Logout -->
    <div class="mt-auto border-t border-slate-700">
        <!-- User Profile -->
        <a href="{{ route('profile') }}" class="flex items-center p-4 hover:bg-slate-700 {{ (request()->routeIs('profile') || request()->routeIs('profile.*')) ? 'bg-accent1 text-white' : 'text-gray-300 hover:bg-slate-700 hover:text-white' }}  rounded-md transition-colors">
            <img class="h-8 w-8 rounded-full flex-shrink-0" src="https://ui-avatars.com/api/?name={{ str_replace(' ', '+', auth()->user()->employee->getFullName()) }}" alt="User avatar">
            <div class="ml-3 overflow-hidden" x-show="open" x-transition>
                <p class="text-sm font-medium text-white whitespace-nowrap">{{ auth()->user()->employee->getFullName() }}</p>
                <p class="text-xs text-gray-400 whitespace-nowrap">View Profile</p>
            </div>
        </a>

        
        @if(count(auth()->user()->roles) > 1)
        <div class="px-4 pb-2">
            <button 
                @click="showRoleModal = true" 
                class="w-full flex items-center py-2 px-4 text-gray-300 hover:bg-slate-700 hover:text-white rounded-md transition-colors"
            >
                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a4 4 0 100 8 4 4 0 000-8zM16 7a4 4 0 100 8 4 4 0 000-8z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11h8" />
                </svg>
                <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Switch Role</span>
            </button>
        </div>
        @endif
        
        <!-- Logout Button -->
        <form action="{{ route('logout') }}" class="px-4 pb-4">
            @csrf
            <button type="submit" class="w-full flex items-center py-2 px-4 text-gray-300 hover:bg-red-600 hover:text-white rounded-md transition-colors">
                <svg class="h-5 w-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="ml-3 whitespace-nowrap" x-show="open" x-transition>Logout</span>
            </button>
        </form>
    </div>

    <!-- Role Switcher Modal -->
    <div 
        x-show="showRoleModal" 
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen">
            <!-- Backdrop -->
            <div 
                @click="showRoleModal = false" 
                class="fixed inset-0 bg-black/60 transition-opacity"
            ></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white rounded-lg shadow-xl w-80 max-w-md z-10">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Select Role</h3>
                    <p class="text-sm text-gray-500">Choose which role to use</p>
                </div>
                
                <div class="p-4">
                    <div class="space-y-2">
                        @foreach(auth()->user()->roles as $role)
                        <form action="{{ route('role.switch') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="{{ $role }}">
                            <button 
                                type="submit"
                                class="{{ auth()->user()->getActiveRole() == $role ? 'bg-indigo-100 text-indigo-800' : 'bg-white text-gray-800' }} w-full text-left px-4 py-2 rounded-md hover:bg-gray-100 transition-colors flex items-center"
                            >
                                <span class="font-medium">{{ UserRole::getLabel(UserRole::tryFrom($role)) }}</span>
                                @if(auth()->user()->getActiveRole() == $role)
                                <svg class="h-5 w-5 text-indigo-800 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                @endif
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-200 flex justify-end">
                    <button 
                        @click="showRoleModal = false" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>