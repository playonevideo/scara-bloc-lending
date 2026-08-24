@extends('layouts.guest')

@section('title', 'Verificare în doi pași — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Verificare în doi pași</h1>
    <p class="mt-1 text-sm text-gray-500">Am trimis un cod prin SMS pe numărul tău de telefon. Introdu-l mai jos.</p>

    <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Cod de verificare</label>
            <input id="code" type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus autocomplete="one-time-code"
                placeholder="••••••"
                class="mt-1 block w-full rounded-xl border-gray-300 text-center text-2xl tracking-[0.5em] shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Verifică
        </button>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-brand-600 hover:text-brand-700">
            Nu ai primit codul? Trimite din nou
        </button>
    </form>
@endsection
