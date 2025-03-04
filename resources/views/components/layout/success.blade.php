@php
if (!isset($success) && !isset($successObject)) {
    return redirect()->route($redirectTo);
}    
@endphp

<x-layout.auth>
    <div class="grid h-screen place-content-center px-4">
        <div class="text-center">
            @yield('svg')
    
            <h1 class="mt-6 text-xl font-black tracking-tight text-primary w-1/2 mx-auto sm:text-4xl">
                {{ $success }}
            </h1>
    
            <p class="mt-4 text-gray-500" id="sub-message">
                Redirecting in <span id="countdown">5</span> seconds...
            </p>
        </div>
    </div>
</x-layout.auth>
    
<script>
    let countdown = 5;
    const countdownElement = document.getElementById('countdown');
    const interval = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        if (countdown === 0) {
            clearInterval(interval);
            window.location.href = "{{ route($redirectTo) }}";
        }
    }, 1000);
</script>