@extends('layouts.app')

@section('title', 'Editează obiect — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-6 text-2xl font-bold text-gray-900">Editează obiectul</h1>

        <form method="POST" action="{{ route('objects.update', $object) }}" enctype="multipart/form-data"
            class="space-y-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($object->images->isNotEmpty())
                <div>
                    <p class="mb-2 text-sm font-medium text-gray-700">Fotografii existente</p>
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach ($object->images as $image)
                            <label class="group relative aspect-square cursor-pointer overflow-hidden rounded-xl">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="" class="h-full w-full object-cover">
                                <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" class="peer sr-only">
                                <span class="absolute inset-0 hidden items-center justify-center bg-black/50 text-2xl text-white peer-checked:flex">✕</span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Apasă pe o fotografie pentru a o marca spre ștergere.</p>
                </div>
            @endif

            @include('objects._form', ['categories' => $categories, 'conditions' => $conditions, 'object' => $object])

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('objects.show', $object) }}" class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">Anulează</a>
                <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    Salvează
                </button>
            </div>
        </form>
    </div>
@endsection
