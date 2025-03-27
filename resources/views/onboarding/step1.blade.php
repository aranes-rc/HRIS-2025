@php
    use App\Enums\Gender;
    use App\Enums\MaritalStatus;
@endphp

@extends('components.layout.onboarding')

@section('title') Personal Information - Step 1 @endsection

@section('subcontent')
<div class="relative z-10 flex min-h-screen flex-col justify-center px-6 py-12 lg:px-8">
            <!-- <div class="sm:mx-auto sm:w-full sm:max-w-md -mb-8 relative z-10">
                <img src="{{ asset('img/roc-logo.png') }}" alt="ROC.PH Logo" class="px-3 h-auto w-auto">
            </div> -->

            <div class="sm:mx-auto sm:w-full sm:max-w-2xl bg-white shadow-xl rounded-lg p-10">
                <h2 class="mt-2 text-left text-2xl/9 font-bold tracking-tight text-gray-900">Enter your information
                </h2>

                <div class="mt-10">
                    <form class="space-y-6" action="{{ route('onboarding.processStep1') }}" method="POST">
                        @csrf
                        
                        <div class="w-full flex gap-3">
                            <x-form.input type="text" name="first_name" id="first_name" label="First Name" required />

                            <x-form.input type="text" name="middle_name" id="middle_name" label="Middle Name" required />

                            <x-form.input type="text" name="last_name" id="last_name" label="Last Name" required />

                            <x-form.input class="" type="text" name="suffix" id="suffix" label="Suffix" required />
                        </div>
                        
                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input type="email" name="email" id="email" label="Email" autocomplete="email" required />    
                            </div>
                            <div class="flex-1">
                                <x-form.input type="tel" name="contact_number" id="contact_number" label="Mobile Number" required />
                            </div>
                        </div>

                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input type="date" name="birthdate" id="birthdate" label="Date of Birth" required />
                            </div>
                            <div class="flex-1">
                                <x-form.select class="h-9" type="select" name="gender" label="Gender" required>
                                    <option value="" selected disabled hidden>Choose an option</option> 
                                    @foreach (Gender::values() as $key => $value)
                                        <option value="{{ $value }}" {{ old('gender') == $value ? 'selected' : '' }}>{{ ucfirst($value) }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                        </div>
                        
                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input type="text" name="birthplace" id="birthplace" label="Place of Birth" required />
                            </div>

                            <div class="flex-1">
                                <x-form.select class="h-9" type="select" name="marital_status" label="Marital Status" required>
                                    <option value="" selected disabled hidden>Choose an option</option>
                                    @foreach (MaritalStatus::values() as $key => $value)
                                        <option value="{{ $value }}" {{ old('marital_status') == $value ? 'selected' : '' }}>{{ ucfirst($value) }}</option>
                                    @endforeach
                                </x-form.select>
                            </div>
                        </div>

                        <x-form.input type="text" name="country_citizenship" id="country_citizenship" label="Country of Citizenship" required />
                        
                        <x-form.input type="text" name="address" id="address" label="Address" required />

                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input type="text" name="city" id="city" label="City" required />
                            </div>
                            <div class="flex-1">
                                <x-form.input type="text" name="province" id="province" label="Province" required />
                            </div>
                        </div>

                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input type="number" name="zip" id="zip" label="Zip Code" required />
                            </div>
                            <div class="flex-1">
                             <x-form.input type="text" name="user_country" id="user_country" label="Country" required />
                            </div>
                        </div>

                        <div class="w-full flex justify-end">
                            <button type="submit"
                                class="flex w-fit justify-center rounded-md bg-primary px-5 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-primary/80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary/60">Next</button>
                        </div>
                    </form>

                    <p class="mt-10 text-center text-sm/6 text-gray-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-primary/80 hover:text-primary-500">Login now</a>
                    </p>
                </div>
            </div>
        </div>
@endsection

@section('scripts')
{{-- ... --}}
@endsection
