@props(['label', 'name', 'accept'])

@php
    $newAttributes = $attributes->merge([
        'value' => old($name),
        "class" => "cursor-pointer file:mr-3 file:py-2 file:px-4 file:border-0 text-sm border-1 border-gray-200 rounded-md file:bg-primary file:text-white font-semibold shadow-md transform hover:-translate-y-1 transition duration-400"
    ])
@endphp

<div class="flex items-center justify-center bg-white">
    <div class="mx-auto w-full">
        <div>
            <x-form.label :name="$name" :label="$label" />
            <input name="{{ $name }}" id="{{ $name }}" type="file" {{ $newAttributes }} />
            <x-form.label name="" label="Accepts: {{ $accept }}" class="mt-2 opacity-60" />
            <x-form.error :name="$name" />
        </div>
    </div>
</div>