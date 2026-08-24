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
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Parolă</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
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

    <div class="my-6 flex items-center gap-3 text-xs text-gray-400">
        <span class="h-px flex-1 bg-gray-100"></span>
        sau
        <span class="h-px flex-1 bg-gray-100"></span>
    </div>

    <button type="button" id="passkey-btn" onclick="loginWithPasskey()"
        class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33"/></svg>
        Autentificare cu passkey
    </button>

    <p class="mt-2 hidden text-center text-xs text-gray-400" id="passkey-unsupported">
        Dispozitivul tău nu suportă passkey-uri.
    </p>

    <script>
        if (typeof PublicKeyCredential === 'undefined') {
            document.getElementById('passkey-btn').classList.add('hidden');
            document.getElementById('passkey-unsupported').classList.remove('hidden');
        }
    </script>

    <div class="mt-6 border-t border-gray-100 pt-6 text-center">
        <p class="text-sm text-gray-500">Ești nou în comunitate?</p>
        <a href="{{ route('register.code') }}" class="mt-1 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">
            Creează un cont cu un cod de invitație
        </a>
    </div>
@endsection
