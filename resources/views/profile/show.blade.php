@extends('layouts.app')

@section('title', 'Profil — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-2xl space-y-6">
        <h1 class="text-2xl font-bold text-gray-900">Profilul meu</h1>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-100 text-2xl font-semibold text-brand-700">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
                <div>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->locationLabel() }}</p>
                    <p class="mt-1 text-sm text-amber-500">
                        @if ($averageRating)
                            {{ str_repeat('★', round($averageRating)) }} {{ number_format($averageRating, 1) }} / 5
                        @else
                            Fără recenzii încă
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            @if (session('status'))
                <div class="rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">{{ session('status') }}</div>
            @endif

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nume complet</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" value="{{ $user->email }}" disabled
                    class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-400">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefon</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>

            <div>
                <p class="mb-2 text-sm font-medium text-gray-700">Confidențialitate — ce este vizibil pentru vecini</p>
                <div class="space-y-2 rounded-xl bg-gray-50 p-4">
                    @foreach ([
                        'show_apartment' => 'Numărul apartamentului',
                        'show_floor' => 'Etajul',
                        'show_phone' => 'Numărul de telefon',
                        'show_email' => 'Adresa de email',
                    ] as $field => $label)
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $user->$field))
                                class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                Salvează
            </button>
        </form>

        @if ($user->reviewsReceived->isNotEmpty())
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-gray-900">Recenzii primite</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($user->reviewsReceived as $review)
                        <div class="rounded-xl bg-gray-50 p-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">{{ $review->reviewer->name }}</p>
                                <span class="text-sm text-amber-500">{{ str_repeat('★', $review->rating) }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="mt-1 text-sm text-gray-600">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
