@extends('layouts.guest')

@section('title', 'Resetează parola — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Resetează parola</h1>
    <p class="mt-1 text-sm text-gray-500">Alege o parolă nouă pentru contul tău.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Parolă nouă</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmă parola</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Resetează parola
        </button>
    </form>
@endsection
