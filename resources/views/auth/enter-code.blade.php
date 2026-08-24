@extends('layouts.guest')

@section('title', 'Creare cont — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Creează un cont nou</h1>
    <p class="mt-1 text-sm text-gray-500">Introdu codul de invitație primit de la administrator pentru a continua înregistrarea.</p>

    <form method="POST" action="{{ route('register.verify') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Cod de invitație</label>
            <input id="code" type="text" name="code" value="{{ old('code') }}" required autofocus autocomplete="off"
                placeholder="ex. VECINI2026"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-center text-lg font-semibold uppercase tracking-widest text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
            @error('code')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Continuă
        </button>
    </form>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700">Înapoi la autentificare</a>
    </p>
@endsection
