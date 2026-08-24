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
                    <h2 class="font-semibold text-gray-900">Autentificare în doi pași (WhatsApp)</h2>
                    <p class="mt-1 text-sm text-gray-500">
                        @if ($user->two_factor_enabled)
                            Activă pentru {{ $user->phone }}. La autentificare vei primi un cod prin WhatsApp.
                        @else
                            Primește un cod prin WhatsApp la fiecare autentificare.
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
            <h2 class="font-semibold text-gray-900">Numărul de telefon pentru 2FA</h2>
            <p class="mt-1 text-sm text-gray-500">Numărul curent: <strong>{{ $user->phone ?? '—' }}</strong></p>

            <form method="POST" action="{{ route('security.change-phone') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="new_phone" class="block text-sm font-medium text-gray-700">Număr nou</label>
                    <input id="new_phone" type="tel" name="new_phone" required placeholder="ex. 07xx xxx xxx"
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    @error('new_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Parola contului</label>
                    <input id="current_password" type="password" name="current_password" required
                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                </div>
                <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                    Trimite codul de confirmare
                </button>
            </form>

            @if (session('auth.pending_phone'))
                <form method="POST" action="{{ route('security.verify-phone') }}" class="mt-4 space-y-4 rounded-xl bg-gray-50 p-4">
                    @csrf
                    <p class="text-sm text-gray-600">Am trimis un cod pe <strong>{{ session('auth.pending_phone') }}</strong>.</p>
                    @if (session('sms_code'))
                        <p class="rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-800 ring-1 ring-amber-200">
                            Mod dezvoltare — codul de verificare: <strong class="tracking-widest">{{ session('sms_code') }}</strong>
                        </p>
                    @endif
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Cod de verificare</label>
                        <input id="code" type="text" name="code" inputmode="numeric" maxlength="6" required
                            class="mt-1 block w-full rounded-xl border-gray-300 text-center text-xl tracking-[0.5em] shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                        Confirmă noul număr
                    </button>
                </form>

                <form method="POST" action="{{ route('security.resend-phone-code') }}" class="mt-3"
                    x-data="{ remaining: {{ config('sms.code.throttle_seconds', 30) }} }"
                    x-init="setInterval(() => { if (remaining > 0) remaining-- }, 1000)">
                    @csrf
                    <button type="submit" :disabled="remaining > 0"
                        class="text-sm font-medium transition"
                        :class="remaining > 0 ? 'cursor-not-allowed text-gray-400' : 'text-brand-600 hover:text-brand-700'">
                        <span x-show="remaining > 0" x-cloak>Nu ai primit mesajul? Încearcă din nou în <span x-text="remaining"></span> s</span>
                        <span x-show="remaining === 0" x-cloak>Nu ai primit mesajul? Trimite din nou</span>
                    </button>
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
