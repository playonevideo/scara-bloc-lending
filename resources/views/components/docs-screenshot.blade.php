@props(['file', 'alt' => 'Screenshot'])

@php
    $path = public_path('images/docs/'.$file);
    $exists = \Illuminate\Support\Facades\File::exists($path);
@endphp

@if ($exists)
    <figure class="my-4 overflow-hidden rounded-xl border border-gray-200 shadow-sm">
        <img src="{{ asset('images/docs/'.$file) }}" alt="{{ $alt }}" class="w-full">
        <figcaption class="bg-gray-50 px-3 py-2 text-xs text-gray-500">{{ $alt }}</figcaption>
    </figure>
@else
    <div class="my-4 flex items-center gap-3 rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 px-4 py-6 text-sm text-gray-400">
        <span class="text-xl">📷</span>
        <span>Screenshot: <strong>{{ $alt }}</strong> — adaugă imaginea în <code>public/images/docs/{{ $file }}</code></span>
    </div>
@endif
