@extends('layouts.app')

@section('title', 'Securitate — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Securitate</h1>

        @if (session('status'))
            <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">{{ session('status') }}</div>
        @endif

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Autentificare în doi pași (aplicație de autentificare)</h2>

            @if ($user->two_factor_enabled)
                <p class="mt-1 text-sm text-gray-500">Activă. La autentificare vei introduce un cod generat de aplicația de autentificare (Google Authenticator, Authy etc.).</p>
                <form method="POST" action="{{ route('security.two-factor.disable') }}" class="mt-4"
                    onsubmit="return confirm('Sigur dorești să dezactivezi autentificarea în doi pași?')">
                    @csrf
                    <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 hover:bg-red-100">Dezactivează</button>
                </form>
            @elseif ($pendingSecret)
                <p class="mt-1 text-sm text-gray-500">Scanează codul QR cu aplicația de autentificare, apoi introdu codul de 6 cifre.</p>

                <div class="mt-4 flex flex-col items-start gap-4 sm:flex-row">
                    <div class="rounded-xl bg-white p-2 ring-1 ring-gray-200">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->generate($qrCodeUrl) !!}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-700">Cheia secretă (introducere manuală):</p>
                        <p class="mt-1 break-all rounded-lg bg-gray-50 px-3 py-2 font-mono text-xs text-gray-600">{{ $pendingSecret }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('security.two-factor.confirm') }}" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Cod de verificare</label>
                        <input id="code" type="text" name="code" inputmode="numeric" maxlength="6" required autofocus
                            class="mt-1 block w-full rounded-xl border-gray-300 text-center text-xl tracking-[0.5em] shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        Activează
                    </button>
                </form>
            @else
                <p class="mt-1 text-sm text-gray-500">Adaugă un strat suplimentar de securitate folosind un cod generat de o aplicație de autentificare (Google Authenticator, Authy, Microsoft Authenticator).</p>
                <form method="POST" action="{{ route('security.two-factor.setup') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">Configurează</button>
                </form>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Chei de acces (Passkeys)</h2>
            <p class="mt-1 text-sm text-gray-500">Autentifică-te rapid și sigur cu amprenta, Face ID sau Windows Hello. Poți înregistra mai multe chei de acces.</p>

            <div class="mt-4 space-y-2">
                @forelse ($user->webAuthnCredentials as $credential)
                    <div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-2.5">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $credential->alias ?? 'Passkey' }}</p>
                            <p class="text-xs text-gray-400">Creat la {{ $credential->created_at?->format('d.m.Y') }}</p>
                        </div>
                        <form method="POST" action="{{ route('security.remove-passkey', $credential->id) }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Elimină</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Nu ai înregistrat încă nicio cheie de acces.</p>
                @endforelse
            </div>

            <button type="button" onclick="registerPasskey()"
                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                Înregistrează passkey
            </button>
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
