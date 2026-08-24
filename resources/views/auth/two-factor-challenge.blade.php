@extends('layouts.guest')

@section('title', 'Verificare în doi pași — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="text-xl font-semibold text-gray-900">Verificare în doi pași</h1>
    <p class="mt-1 text-sm text-gray-500">Am trimis un cod prin WhatsApp pe numărul tău de telefon. Introdu-l mai jos.</p>

    @if (session('sms_code'))
        <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800 ring-1 ring-amber-200">
            Mod dezvoltare — codul de verificare: <strong class="tracking-widest">{{ session('sms_code') }}</strong>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="code" class="block text-sm font-medium text-gray-700">Cod de verificare</label>
            <input id="code" type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required autofocus autocomplete="one-time-code"
                placeholder="••••••"
                class="mt-1 block w-full rounded-xl border-0 bg-gray-100 px-4 py-2.5 text-center text-2xl tracking-[0.5em] text-gray-900 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-brand-500">
        </div>

        <button type="submit"
            class="w-full rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2">
            Verifică
        </button>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="mt-4 text-center"
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
@endsection
