@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block px-6 py-3 text-sm font-medium border-l-2 border-gray-900 text-gray-900 bg-white'
    : 'block px-6 py-3 text-sm font-medium border-l-2 border-transparent text-gray-600 hover:text-gray-900 hover:bg-white';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
