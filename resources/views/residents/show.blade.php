@extends('layouts.app')

@section('title', $resident->name.' — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            <div class="h-24 bg-gradient-to-r from-brand-500 to-brand-700"></div>

            <div class="px-6 pb-6 sm:px-8">
                <div class="-mt-10 flex flex-col items-start gap-4 sm:flex-row sm:items-end">
                    <span class="flex h-20 w-20 items-center justify-center rounded-2xl bg-brand-100 text-3xl font-bold text-brand-700 ring-4 ring-white">
                        {{ strtoupper(mb_substr($resident->name, 0, 1)) }}
                    </span>

                    <div class="flex-1">
                        <h1 class="flex flex-wrap items-center gap-2 text-2xl font-bold text-gray-900">
                            {{ $resident->name }}
                            @if ($resident->isOnline())
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Online
                                </span>
                            @elseif ($resident->lastActiveLabel())
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">{{ $resident->lastActiveLabel() }}</span>
                            @endif
                        </h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $resident->locationLabel() }}</p>

                        @if ($averageRating)
                            <p class="mt-1 flex items-center gap-1 text-sm text-amber-500">
                                <span>{{ str_repeat('★', round($averageRating)) }}</span>
                                <span class="font-medium text-gray-700">{{ number_format($averageRating, 1) }}</span>
                                <span class="text-gray-400">({{ $reviewsCount }} {{ $reviewsCount == 1 ? 'recenzie' : 'recenzii' }})</span>
                            </p>
                        @endif
                    </div>

                    @if ($resident->id !== auth()->id())
                        <form method="POST" action="{{ route('conversations.store') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $resident->id }}">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                                Trimite un mesaj
                            </button>
                        </form>
                    @endif
                </div>

                <dl class="mt-6 grid grid-cols-1 gap-4 rounded-2xl bg-gray-50 p-5 sm:grid-cols-2">
                    @if ($resident->show_floor && $resident->apartment?->floor)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Etaj</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-800">Etajul {{ $resident->apartment->floor->number }}</dd>
                        </div>
                    @endif

                    @if ($resident->apartment)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Clădire</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-800">{{ $resident->apartment->floor?->staircase?->building?->name ?? '—' }} · {{ $resident->apartment->floor?->staircase?->name ?? '—' }}</dd>
                        </div>
                    @endif

                    @if ($resident->show_phone && $resident->phone)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Telefon</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-800">{{ $resident->phone }}</dd>
                        </div>
                    @endif

                    @if ($resident->show_email && $resident->email)
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Email</dt>
                            <dd class="mt-1 break-all text-sm font-medium text-gray-800">{{ $resident->email }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Membru din</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">{{ $resident->created_at?->format('F Y') }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Obiecte publicate</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">{{ $objects->total() }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div>
            <h2 class="mb-3 text-lg font-semibold text-gray-900">Obiectele lui {{ explode(' ', trim($resident->name))[0] }}</h2>

            @if ($objects->isEmpty())
                <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
                    Acest vecin nu a publicat încă niciun obiect.
                </p>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($objects as $object)
                        <x-object-card :object="$object" />
                    @endforeach
                </div>

                <div class="mt-6">{{ $objects->links() }}</div>
            @endif
        </div>
    </div>
@endsection
