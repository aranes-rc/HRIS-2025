@extends('components.layout.root')

@section('content')
    <x-topbar />
    <div class="relative min-h-screen bg-gray-50">
        <!-- Background SVG -->
        <div class="absolute inset-0 z-0 overflow-hidden">
            <img src="{{ asset('img/login/login-bg.png') }}" alt="Login background" class="mt-20 size-full">
        </div>

        <!-- Content container -->
        @section('subcontent')
    </div>
@endsection