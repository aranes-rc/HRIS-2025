@extends('components.layout.onboarding')

@section('title') Family Information - Step 2 @endsection

@section('subcontent')
<div class="relative z-10 flex min-h-screen flex-col justify-center px-6 py-12 lg:px-8">
            <!-- <div class="sm:mx-auto sm:w-full sm:max-w-md -mb-8 relative z-10">
                <img src="{{ asset('img/roc-logo.png') }}" alt="ROC.PH Logo" class="px-3 h-auto w-auto">
            </div> -->

            <div class="sm:mx-auto sm:w-full sm:max-w-2xl bg-white shadow-xl rounded-lg p-10">
                <h2 class="mt-2 text-left text-2xl/9 font-bold tracking-tight text-gray-900">Family Information
                </h2>
                <h4 class="text-gray-900 font-bold">Family, Guardian Information and Emergency Contact details.</h4>

                <div class="mt-10">
                    <form class="space-y-6" action="{{ route('onboarding.processStep2') }}" method="POST">
                        @csrf
                        
                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input class="flex-1" type="text" name="father_first_name" id="father_first_name" label="Father's First Name" required />
                            </div>
                            <div class="flex-1">
                                <x-form.input class="flex-1" type="text" name="father_last_name" id="father_last_name" label="Last Name" required />
                            </div>
                        </div>

                        <x-form.input type="date" name="father_birthdate" id="father_birthdate" label="Father's Date of Birth" required />
                        
                        <x-form.input type="tel" name="father_contact_number" id="father_contact_number" label="Father's Mobile Number" required />

                        <x-form.input type="text" name="father_occupation" id="father_occupation" label="Father's Occupation" required />

                        <x-form.input type="text" name="father_employer" id="father_employer" label="Father's Name of Employer" required />

                        <div class="w-full flex gap-5">
                            <div class="flex-1">
                                <x-form.input type="text" name="mother_first_name" id="mother_first_name" label="Mother's First Name" required />
                            </div>
                            <div class="flex-1">
                                <x-form.input type="text" name="mother_last_name" id="mother_last_name" label="Last Name" required />
                            </div>
                        </div>

                        <x-form.input type="date" name="mother_birthdate" id="mother_birthdate" label="Mother's Date of Birth" required />
                        
                        <x-form.input type="text" name="mother_occupation" id="mother_occupation" label="Mother's Occupation" required />

                        <x-form.input type="text" name="mother_employer" id="mother_employer" label="Mother's Employer" required />


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
