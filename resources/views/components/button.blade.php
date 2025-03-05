@php
    $sizeClass = $sizes[$size] ?? $sizes['sm'];
    $newAttributes = $attributes->merge([
        "class" => "cursor-pointer rounded-$roundness bg-$containerColor font-semibold text-$contentColor shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-$containerColor $sizeClass transition delay-150 duration-300 ease-in-out hover:-translate-y-1 hover:scale-110"
    ])
@endphp

<button {{ $newAttributes }}>{{ $text ?? $slot }}</button>
