@extends('layouts.app')

@section('title', 'Favorite — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-gray-900 sm:text-3xl">Obiecte favorite</h1>

    @if ($objects->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center text-gray-500">
            Nu ai salvat încă niciun obiect la favorite.
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($objects as $object)
                <x-object-card :object="$object" />
            @endforeach
        </div>
    @endif
@endsection
