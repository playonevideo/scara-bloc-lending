@extends('layouts.app')

@section('title', 'Obiecte — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Obiecte</h1>
        <a href="{{ route('objects.create') }}"
            class="inline-flex items-center gap-1 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>
            Adaugă obiect
        </a>
    </div>

    <form method="GET" action="{{ route('objects.index') }}" class="mb-6 space-y-3 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <label for="q" class="sr-only">Caută</label>
                <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}" placeholder="Caută un obiect..."
                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Caută</button>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div>
                <label for="category" class="block text-xs font-medium text-gray-500">Categorie</label>
                <select name="category" id="category" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Toate</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(($filters['category'] ?? '') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-500">Disponibilitate</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Toate</option>
                    <option value="available" @selected(($filters['status'] ?? '') === 'available')>Disponibile</option>
                    <option value="reserved" @selected(($filters['status'] ?? '') === 'reserved')>Rezervate</option>
                    <option value="borrowed" @selected(($filters['status'] ?? '') === 'borrowed')>Împrumutate</option>
                </select>
            </div>
            <div>
                <label for="floor" class="block text-xs font-medium text-gray-500">Etaj</label>
                <select name="floor" id="floor" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="">Toate</option>
                    @foreach ($floors as $floor)
                        <option value="{{ $floor }}" @selected(($filters['floor'] ?? '') == $floor)>Etajul {{ $floor }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="sort" class="block text-xs font-medium text-gray-500">Sortare</label>
                <select name="sort" id="sort" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Cele mai noi</option>
                    <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>Populare</option>
                    <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>După rating</option>
                </select>
            </div>
        </div>
    </form>

    @if ($objects->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center">
            <p class="text-gray-500">Nu am găsit niciun obiect.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($objects as $object)
                <x-object-card :object="$object" />
            @endforeach
        </div>

        <div class="mt-8">
            {{ $objects->links() }}
        </div>
    @endif
@endsection
