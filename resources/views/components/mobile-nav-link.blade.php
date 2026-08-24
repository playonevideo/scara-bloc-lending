@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
    @class([
        'flex flex-col items-center justify-center gap-0.5 py-2 transition',
        'text-brand-600' => $active,
        'text-gray-500 hover:text-gray-700' => ! $active,
    ])
    {{ $attributes }}>
    {{ $slot }}
</a>
