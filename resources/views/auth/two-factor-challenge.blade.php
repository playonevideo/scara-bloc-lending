@extends('layouts.guest')

@section('title', 'Verificare în doi pași — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Verificare în doi pași</h1>
    <p class="mt-1 text-sm text-gray-500">Introdu codul de 6 cifre generat de aplicația ta de autentificare (Google Authenticator, Authy etc.).</p>

    <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Cod de autentificare</label>
            <input id="code" type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus autocomplete="one-time-code"
                placeholder="••••••"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-center text-2xl tracking-[0.5em] text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Verifică
        </button>
    </form>
@endsection
