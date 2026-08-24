@extends('layouts.app')

@section('title', 'Securitate — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Securitate</h1>

        @if (session('status'))
            <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Autentificare în doi pași (SMS)</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        @if ($user->two_factor_enabled)
                            Activă pentru {{ $user->phone }}. La autentificare vei primi un cod prin SMS.
                        @else
                            Primește un cod prin SMS la fiecare autentificare.
                        @endif
                    </p>
                </div>
                <form method="POST" action="{{ route('security.toggle-2fa') }}">
                    @csrf
                    <button type="submit"
                        @class([
                            'rounded-xl px-4 py-2 text-sm font-semibold',
                            'bg-red-50 text-red-600 hover:bg-red-100' => $user->two_factor_enabled,
                            'bg-brand-600 text-white hover:bg-brand-700' => ! $user->two_factor_enabled,
                        ])>
                        {{ $user->two_factor_enabled ? 'Dezactivează' : 'Activează' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Chei de acces (Passkeys)</h2>
            <p class="mt-1 text-sm text-gray-500">Autentifică-te rapid și sigur cu amprenta, Face ID sau Windows Hello. Această opțiune este disponibilă pe pagina de autentificare.</p>
            <a href="{{ route('login') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">Configurează passkey-ul →</a>
        </div>

        <form method="POST" action="{{ route('security.update-password') }}" class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="font-semibold text-gray-900">Schimbă parola</h2>

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700">Parola curentă</label>
                <input id="current_password" type="password" name="current_password" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Parolă nouă</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmă parola nouă</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            @error('current_password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                Schimbă parola
            </button>
        </form>
    </div>
@endsection
