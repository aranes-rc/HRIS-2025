@php
    use App\Enums\ShiftType;
    use App\Enums\AttendanceType;
    use App\Enums\WorkMode;
@endphp

@extends('components.layout.root')

@section('content')
<div class="container mx-auto p-6" x-data="previewModal()">
  <!-- Dashboard Header -->
  <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">
        @if (request()->user()->isEmployee())
            My Attendances
        @else
            Company Attendance Dashboard
        @endif
    </h1>
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <!-- Filter Form -->
      <form action="{{ route('attendance.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
        <input 
          type="text" 
          name="search" 
          placeholder="Search employee" 
          value="{{ request('search') }}"
          class="w-full sm:w-auto border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
        />
        <select 
          name="shift_type" 
          class="w-full sm:w-auto border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          <option value="">All Shifts</option>
          @foreach (ShiftType::options() as $key => $value)
            <option value="{{ $key }}" {{ request('shift_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
          @endforeach
          <!-- Add more shift options as needed -->
        </select>
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
          Filter
        </button>
      </form>
      <!-- Export Button -->
      <a href="{{ route('attendance.export', request()->all()) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center">
        Export
      </a>
    </div>
  </div>

  <!-- Attendance Table -->
  <div class="bg-white shadow overflow-hidden rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Work Mode</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proofs</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($attendances as $attendance)
        <tr>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attendance->id }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ $attendance->employee->getFullName() }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-900">{{ ShiftType::getLabel($attendance->shift_type) }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-900">{{ AttendanceType::getLabel($attendance->type) }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-900">{{ WorkMode::getLabel($attendance->work_mode) }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            {{ \Carbon\Carbon::parse($attendance->created_at)->format('M d, Y H:i') }}
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm">
            <!-- Buttons for Screenshot Previews -->
            <div class="mt-2 flex flex-wrap gap-2">
                <x-outlined-button text="Selfie" @click="openModal('{{ asset('storage/'.$attendance->screenshot_workstation_selfie) }}', 'Selfie')" />
                <x-outlined-button text="CGC" @click="openModal('{{ asset('storage/'.$attendance->screenshot_cgc_chat) }}', 'Company Group Chat')" />
                <x-outlined-button text="Dept" @click="openModal('{{ asset('storage/'.$attendance->screenshot_department_chat) }}', 'Department Chat')" />
                <x-outlined-button text="Team" @click="openModal('{{ asset('storage/'.$attendance->screenshot_team_chat) }}', 'Team Chat')" />
                <x-outlined-button text="Group" @click="openModal('{{ asset('storage/'.$attendance->screenshot_group_chat) }}', 'Group Chat')" />
              </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm">
            <a href="{{ route('attendance.show', $attendance->id) }}" class="text-indigo-600 hover:text-indigo-900">
              View
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-6 py-4 text-center text-gray-500">
            No attendance records found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

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
        <button @click="closeModal()" class="cursor-pointer text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
      </div>
      <span class="text-lg font-semibold" x-text="imageLabel"></span>
      <div class="mt-2">
        <img :src="imageUrl" alt="Screenshot Preview" class="w-full rounded">
      </div>
    </div>
</div>

<!-- Alpine.js Component for Modal -->
<script>
    function previewModal() {
      return {
        open: false,
        imageUrl: '',
        imageLabel: '',
        openModal(url, label) {
          this.imageLabel = label;
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
