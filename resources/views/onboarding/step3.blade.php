@php
    use App\Enums\EmploymentType;
    use App\Enums\WorkMode;
    use App\Enums\Department;
    use App\Enums\DepartmentTeam;
    use App\Enums\UserRole;
@endphp

@extends('components.layout.onboarding')

@section('title') Job Information - Step 3 @endsection

@section('subcontent')
<div class="relative z-10 flex min-h-screen flex-col justify-center px-6 py-12 lg:px-8">
            <!-- <div class="sm:mx-auto sm:w-full sm:max-w-md -mb-8 relative z-10">
                <img src="{{ asset('img/roc-logo.png') }}" alt="ROC.PH Logo" class="px-3 h-auto w-auto">
            </div> -->

            <div class="sm:mx-auto sm:w-full sm:max-w-2xl bg-white shadow-xl rounded-lg p-10">
                <h2 class="mt-2 text-left text-2xl/9 font-bold tracking-tight text-gray-900">Job Information
                </h2>

                <div class="mt-10">
                    <form class="space-y-6" action="{{ route('onboarding.processStep2') }}" method="POST">
                        @csrf
                        
                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.select type="select" name="employment_type" id="employment_type" label="Employment Type" required>
                                    <option value="" selected disabled hidden>Choose an option</option>
                                    @foreach (EmploymentType::options() as $key => $value)
                                        <option value="{{ $key }}" {{ old('employment_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                            <div class="flex-1">
                                <x-form.select type="select" name="work_mode" id="work_mode" label="Work Mode" required>
                                    <option value="" selected hidden disabled>Choose an option</option>
                                    @foreach (WorkMode::options() as $key => $value)
                                        <option value="{{ $key }}" {{ old('work_mode') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                        </div>

                        <x-form.input type="date" name="date_of_orientation" id="date_of_orientation" label="Date of Orientation Day" required />
                        
                        <x-form.input type="date" name="date_of_start" id="date_of_start" label="Date of Start" required />

                        <x-form.select type="select" name="department" id="department" label="Department Assigned" required>
                            <option value="" selected disabled hidden>Choose an Option</option>
                            @foreach (Department::options() as $key => $value)
                                <option value="{{ $key }}" {{ old('department') == $key ? 'selected' : ''}}>{{ $value }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select type="select" name="department_team" id="department_team" label="Team Assigned" required>
                            <option value="" selected disabled hidden>Choose an Option</option>
                            @foreach (DepartmentTeam::options() as $key => $value)
                                <option value="{{ $key }}" {{ old('department_team') == $key ? 'selected' : ''}}>{{ $value }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select type="select" name="position_applied" id="position_applied" label="Position Applied" required>
                            <option value="" selected disabled hidden>Choose an Option</option>
                            @foreach (UserRole::values() as $key => $value)
                                <option value="{{ $value }}" {{ old('position_applied') == $value ? 'selected' : ''}}>{{ $value }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.input type="number" name="group" id="group" label="Group Number" required/>


                        <div class="w-full flex justify-between">
                            <button onclick="history.back()" type="button"
                                class="flex w-fit justify-center rounded-md bg-primary px-5 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-primary/80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary/60">Back</button>
                            <button type="submit"
                                class="flex w-fit justify-center rounded-md bg-primary px-5 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-primary/80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary/60">Next</button>
                        </div>
                    </form>

                    <p class="mt-10 text-center text-sm/6 text-gray-500">
                        Already have an account?
                        <a href="" class="font-semibold text-primary/80 hover:text-primary-500">Login now</a>
                    </p>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
{{-- ... --}}
@endsection