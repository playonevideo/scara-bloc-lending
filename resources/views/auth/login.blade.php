@extends('layouts.guest')

@section('title', 'Autentificare — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Bine ai revenit!</h1>
    <p class="mt-1 text-sm text-gray-500">Autentifică-te pentru a accesa comunitatea ta.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Adresă de email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Parolă</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                Ține-mă minte
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                Ai uitat parola?
            </a>
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Autentificare
        </button>
    </form>

    <div class="mt-6 border-t border-gray-100 pt-6 text-center">
        <p class="text-sm text-gray-500">Nu ai încă un cont?</p>
        <p class="mt-1 text-sm text-gray-500">Conturile se creează doar prin invitație din partea administratorului.</p>
    </div>
@endsection
