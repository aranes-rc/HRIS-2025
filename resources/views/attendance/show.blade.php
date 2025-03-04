@php
    use App\Enums\ShiftType;
    use App\Enums\AttendanceType;
    use App\Enums\WorkMode;
    use \Carbon\Carbon;
@endphp

@extends('components.layout.root')

@section('content')
<div class="container mx-auto p-6" x-data="previewModal()">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Attendance Detail</h1>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <!-- Attendance and Employee Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Attendance Information -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Attendance Information</h2>
                <p><span class="font-medium">ID:</span> {{ $attendance->id }}</p>
                <p><span class="font-medium">Employee ID:</span> {{ $attendance->employee_id }}</p>
                <p><span class="font-medium">Shift Type:</span> {{ ShiftType::getLabel($attendance->shift_type) }}</p>
                <p><span class="font-medium">Type:</span> {{ AttendanceType::getLabel($attendance->type) }}</p>
                <p><span class="font-medium">Work Mode:</span> {{ WorkMode::getLabel($attendance->work_mode) }}</p>
                <p><span class="font-medium">Created At:</span> {{ Carbon::parse($attendance->created_at)->format('M d, Y H:i') }}</p>
                <p><span class="font-medium">Updated At:</span> {{ Carbon::parse($attendance->updated_at)->format('M d, Y H:i') }}</p>
            </div>
            <!-- Employee Information -->
            <div>
                <h2 class="text-xl font-semibold mb-4">Employee Information</h2>
                <p><span class="font-medium">Name:</span> {{ $attendance->employee->name ?? 'N/A' }}</p>
                <p><span class="font-medium">Contact:</span> {{ $attendance->employee->contact_number ?? 'N/A' }}</p>
                <p><span class="font-medium">Department:</span> {{ $attendance->employee->department ?? 'N/A' }}</p>
                <p><span class="font-medium">Employment Type:</span> {{ $attendance->employee->employment_type ?? 'N/A' }}</p>
                <p><span class="font-medium">Status:</span> {{ $attendance->employee->status ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Proof Screenshots Gallery -->
        <div class="mt-6">
            <h2 class="text-xl font-semibold mb-4">Proof Screenshots</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <img src="{{ asset("storage/".$attendance->screenshot_workstation_selfie) }}" alt="Selfie Preview" class="w-full h-40 object-cover rounded cursor-pointer" @click="openModal('{{ asset("storage/".$attendance->screenshot_workstation_selfie) }}')">
                    <p class="text-center mt-2">Selfie</p>
                </div>
                <div>
                    <img src="{{ asset("storage/".$attendance->screenshot_cgc_chat) }}" alt="CG Chat Preview" class="w-full h-40 object-cover rounded cursor-pointer" @click="openModal('{{ asset("storage/".$attendance->screenshot_cgc_chat) }}')">
                    <p class="text-center mt-2">CG Chat</p>
                </div>
                <div>
                    <img src="{{ asset("storage/".$attendance->screenshot_department_chat) }}" alt="Department Chat Preview" class="w-full h-40 object-cover rounded cursor-pointer" @click="openModal('{{ asset("storage/".$attendance->screenshot_department_chat) }}')">
                    <p class="text-center mt-2">Department Chat</p>
                </div>
                <div>
                    <img src="{{ asset("storage/".$attendance->screenshot_team_chat) }}" alt="Team Chat Preview" class="w-full h-40 object-cover rounded cursor-pointer" @click="openModal('{{ asset("storage/".$attendance->screenshot_team_chat) }}')">
                    <p class="text-center mt-2">Team Chat</p>
                </div>
                <div>
                    <img src="{{ asset("storage/".$attendance->screenshot_group_chat) }}" alt="Group Chat" class="w-full h-40 object-cover rounded cursor-pointer" @click="openModal('{{ asset("storage/".$attendance->screenshot_group_chat) }}')">
                    <p class="text-center mt-2">Group Chat</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Conditional Update & Delete Buttons -->
    @if(auth()->user()->isAdmin() || auth()->user()->isHr())
    <div class="mt-6 flex gap-4">
        <x-button text="Update" onclick="window.location.href = '{{ route('attendance.edit', $attendance->id) }}'" />
        <form action="" method="POST" onsubmit="return confirm('Are you sure you want to delete this attendance?');">
            @csrf
            @method('DELETE')
            <x-button text="Delete" type="submit" />
        </form>
    </div>
    @endif

    <!-- Modal Preview -->
    <div x-show="open" 
         class="fixed inset-0 flex items-center justify-center z-50" 
         style="display: none;" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="fixed inset-0 bg-gray-900 opacity-50" @click="closeModal()"></div>
        <div class="bg-white rounded-lg p-4 z-10 max-w-3xl w-full">
            <div class="flex justify-end">
                <button @click="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
            <div class="mt-2">
                <img :src="imageUrl" alt="Screenshot Preview" class="w-full rounded">
            </div>
        </div>
    </div>
</div>

<!-- Alpine.js Component for Modal -->
<script>
  function previewModal() {
    return {
      open: false,
      imageUrl: '',
      openModal(url) {
        this.imageUrl = url;
        this.open = true;
      },
      closeModal() {
        this.open = false;
      }
    }
  }
</script>
@endsection
