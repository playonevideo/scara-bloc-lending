@extends('layouts.app')

@section('title', 'Adaugă obiect — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="mb-6 text-2xl font-bold text-gray-900">Adaugă un obiect</h1>

        <form method="POST" action="{{ route('objects.store') }}" enctype="multipart/form-data"
            class="space-y-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('objects._form', ['categories' => $categories, 'conditions' => $conditions])

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('objects.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">Anulează</a>
                <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    Publică obiectul
                </button>
            </div>
        </form>
    </div>
@endsection
