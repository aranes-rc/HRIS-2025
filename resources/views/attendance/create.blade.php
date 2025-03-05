<x-layout.auth>
    <div class="bg-primary/20 p-6">
        <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <div class="flex gap-4 mb-8 divide-gray-200 items-center">
                <h1 class="text-2xl font-bold text-primary">Attendance</h1>
                -
                <p id="clock" class="text-2xl font-bold text-primary"></p>
            </div>

            <form id="attendance-form" class="space-y-8" action="{{ route('attendance.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div>
                    <x-form.input type="number" name="employee_id" label="Employee ID" value="{{ $employee->id }}"
                        required />
                </div>

                {{-- <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-form.input type="text" name="first-name" label="First name" required />
                    <x-form.input type="text" name="middle-name" label="Middle name" />
                    <x-form.input type="text" name="last-name" label="Last name" required />
                </div> --}}

                {{-- <div>
                    <x-form.input type="email" name="email" id="email" label="Email" autocomplete="email" required />
                </div> --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.select label="Shift" name="shift_type"
                        selected="{{ old('shift_type') ?? $currentShiftType }}" :options="$shiftTypes" required />

                    <x-form.select label="Attendance Type" name="type"
                        selected="{{ old('type') ?? $currentAttendanceType }}" :options="$attendanceTypes" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.select label="Work mode" name="work_mode" selected="{{ old('work_mode') }}"
                        :options="$workModes" required />

                    <x-form.select label="Employment Program Type" name="employment_type"
                        selected="{{ old('employment_type') ?? $employee->employment_type->value }}"
                        :options="$employmentTypes" required />
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.select label="Department" name="department"
                        selected="{{ old('department') ?? $employee->department->value }}" :options="$departments"
                        required />

                    <x-form.select label="Department Team" name="department_team" :options="$departmentTeams"
                        required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.select label="Department Role" name="department" :options="[
                                'tl' => 'Team Leader',
                                'gl' => 'Group Leader',
                                'member' => 'Group Member',
                            ]" required />

                    <x-form.select label="Department Team Group Assigned" name="department_team" :options="[
                                '1' => 'Group 1',
                                '2' => 'Group 2',
                                '3' => 'Group 3',
                                '4' => 'Group 4',
                                '5' => 'Group 5',
                                '6' => 'Group 6',
                                '7' => 'Group 7',
                                '8' => 'Group 8',
                            ]" required />
                </div>

                <x-form.file label="Acceptable Workstation Selfie" name="screenshot_workstation_selfie"
                    accept=".jpeg,.jpg,.png" class="mt-2" required />

                <x-form.file label="Acceptable Company Group Chat Screenshot" name="screenshot_cgc_chat"
                    accept=".jpeg,.jpg,.png" class="mt-2" required />

                <x-form.file label="Acceptable Department Chat Screenshot" name="screenshot_department_chat"
                    accept=".jpeg,.jpg,.png" class="mt-2" required />

                <x-form.file label="Acceptable Team Chat Screenshot" name="screenshot_team_chat"
                    accept=".jpeg,.jpg,.png" class="mt-2" required />

                <x-form.file label="Acceptable Group Chat Screenshot" name="screenshot_group_chat"
                    accept=".jpeg,.jpg,.png" class="mt-2" required />

                <div class="text-sm text-gray-600">
                    <p class="mb-4">After submitting this form, you can check if a ticket has been generated in
                        the "My Tickets" section. Please ensure that all necessary attachments are included. If
                        any are missing, kindly reply to the ticket and attach the required files to ensure
                        proper documentation.</p>
                    <p>The time and date of submission will be recorded based on when the form was submitted.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" class="h-4 w-4" required>
                    <label class="text-sm">I have read and agree to the <a href="#" class="text-primary underline">Terms
                            and
                            Conditions</a> and <a href="#" class="text-primary underline">Privacy Policy</a></label>
                </div>

                <x-button text="Submit" class="px-4 py-2" type="submit" for="attendance-form" />
            </form>
        </div>
    </div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto bg-black/60 hidden" id="error-modal">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left shadow-xl transition-all data-closed:translate-y-4 data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in sm:my-8 sm:w-full sm:max-w-lg sm:p-6 data-closed:sm:translate-y-0 data-closed:sm:scale-95"
                data-headlessui-state="open" data-open="" id="headlessui-dialog-panel-:r7:" style="">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:size-10">
                        <svg aria-hidden="true" class="size-6 text-red-600" data-slot="icon" fill="none"
                            stroke="currentColor" stroke-width="1.5" viewbox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"
                                stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-base font-semibold text-gray-900" data-headlessui-state="open" data-open=""
                            id="headlessui-dialog-title-:r8:">
                            Confirm Duplicate Attendance
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500" id="error-message"></p>
                            <br />
                            <p class="text-sm text-gray-500">
                                Do you still want to record it anyway?
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button id="confirm-button"
                        class="cursor-pointer inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 sm:ml-3 sm:w-auto">
                        Yes, proceed
                    </button>
                    <button id="cancel-button"
                        class="cursor-pointer mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 shadow-xs ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto"
                        data-autofocus="true">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layout.auth>

<script>
    function updateClock() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds} ${ampm}`;
    }
    
    setInterval(updateClock, 1000);
    updateClock();
</script>

<script>
    const attendanceForm = document.getElementById("attendance-form");
    const errorModal = document.getElementById("error-modal");
    const confirmButton = document.getElementById("confirm-button");
    const cancelButton = document.getElementById("cancel-button");
  
    attendanceForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        const employeeId = document.querySelector('input[name="employee_id"]').value;
        const shiftType = document.querySelector('select[name="shift_type"]').value;
        const attendanceType = document.querySelector('select[name="type"]').value;

        try {
            const { data } = await axios.post("{{ route('attendance.exists') }}", {
                employee_id: employeeId,
                shift_type: shiftType,
                type: attendanceType
            });
            
            if (data.exists) {
                const errorMessage = `An attendance record for <strong>${titleCase(shiftType)} ${titleCase(attendanceType.replace("_", " "))}</strong> already exists for today which was created at <strong>${data.created_at}</strong>.`;
                document.getElementById("error-message").innerHTML = errorMessage;

                errorModal.classList.remove("hidden");
            } else {
                // If no duplicate exists, submit the form normally
                attendanceForm.submit();
            }
        } catch (error) {
            console.error("Error checking attendance exists:", error);
        }
    });
    
    confirmButton.addEventListener("click", () => {
        attendanceForm.submit();
    });

    cancelButton.addEventListener("click", () => {
        errorModal.classList.add("hidden");
    });

    function titleCase(s) {
        return s.toLowerCase()
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
    }
</script>