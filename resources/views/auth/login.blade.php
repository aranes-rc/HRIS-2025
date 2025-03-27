<x-layout.guest>
    <div class="relative min-h-screen bg-gray-50">
        <!-- Background SVG -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="{{ asset('img/login/login-bg.png') }}" alt="Login background" class="mt-20 size-full">
        </div>

        <!-- Content container -->
        <div class="relative z-10 flex min-h-screen flex-col justify-center px-6 py-12 lg:px-8">
            <div class="sm:mx-auto sm:w-full sm:max-w-md -mb-8 relative z-10">
                <img src="{{ asset('img/roc-logo.png') }}" alt="ROC.PH Logo" class="px-3 h-auto w-auto">
            </div>

            <div class="sm:mx-auto sm:w-full sm:max-w-md bg-white shadow-xl rounded-lg p-10">
                <h2 class="mt-2 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Login to your account
                </h2>

                <div class="mt-10">
                    <form class="space-y-6" action="{{ route('authenticate') }}" method="POST">
                        @csrf

                        <x-form.input type="number" name="employee-number" id="employee-number" label="Employee number" required />

                        <x-form.input type="email" name="email" id="email" label="Email" autocomplete="email" required />

                        <x-form.input type="password" name="password" id="password" label="Password" autocomplete="current-password" required />
                        
                        <div class="flex items-center justify-between">
                            <div class="flex gap-3">
                                <div class="flex h-6 shrink-0 items-center">
                                    <div class="group grid size-4 grid-cols-1">
                                        <input class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-primary checked:bg-primary indeterminate:border-primary indeterminate:bg-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 
                           forced-colors:appearance-auto" id="remember-me" name="remember-me" type="checkbox" />
                                        <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25"
                                            fill="none" viewbox="0 0 14 14">
                                            <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            </path>
                                            <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <label class="block text-sm/6 text-gray-900" for="remember-me">
                                    Remember me
                                </label>
                            </div>
                            <div class="text-sm/6">
                                <a class="font-semibold text-primary hover:text-primary" href="#">
                                    Forgot password?
                                </a>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="flex w-full justify-center rounded-md bg-primary px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-primary/80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary/60">Sign
                                in</button>
                        </div>
                    </form>

                    <p class="mt-10 text-center text-sm/6 text-gray-500">
                        Don't have an account?
                        <a href="{{ route('onboarding.step1') }}" class="font-semibold text-primary/80 hover:text-primary-500">Sign up now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout.guest>