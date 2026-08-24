@extends('layouts.guest')

@section('title', 'Înregistrare — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Creează-ți contul</h1>
    <p class="mt-1 text-sm text-gray-500">Ai fost invitat(ă) să te alături comunității.
        @if ($invitation->apartment)
            Apartamentul asociat: <strong>{{ $invitation->apartment->fullLabel() }}</strong>.
        @endif
    </p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
        @csrf
        <input type="hidden" name="code" value="{{ $invitation->code }}">

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nume complet</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Adresă de email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Telefon (opțional)</label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                placeholder="+40 7xx xxx xxx"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
            <p class="mt-1 text-xs text-gray-400">Folosit pentru autentificarea în doi pași prin SMS.</p>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Parolă</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmă parola</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Creează contul
        </button>
    </form>
@endsection
