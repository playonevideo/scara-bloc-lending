@props(['object'])

@php
    $image = $object->coverImage();
    $imageUrl = $image ? \Illuminate\Support\Facades\Storage::url($image->path) : null;
@endphp

<a href="{{ route('objects.show', $object) }}" class="group flex flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm transition hover:shadow-md">
    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $object->title }}" loading="lazy"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center text-brand-300">
                <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
            </div>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-center justify-between gap-2">
            <span class="truncate text-xs font-medium uppercase tracking-wide text-brand-600">{{ $object->category?->name ?? 'Diverse' }}</span>
            @if ($object->isAvailable())
                <span class="rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Disponibil</span>
            @else
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">{{ $object->status->label() }}</span>
            @endif
        </div>

        <h3 class="mt-1 line-clamp-1 font-semibold text-gray-900">{{ $object->title }}</h3>

        @if ($object->description)
            <p class="mt-1 line-clamp-2 text-sm text-gray-500">{{ $object->description }}</p>
        @endif

        <div class="mt-auto flex items-center justify-between pt-3">
            <span class="text-sm text-gray-500">{{ $object->owner->name }} · {{ $object->owner->locationLabel() }}</span>
            <span class="text-sm font-medium text-brand-600">Vezi detalii →</span>
        </div>
    </div>
</a>
