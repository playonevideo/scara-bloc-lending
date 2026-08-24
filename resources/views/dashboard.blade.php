@extends('layouts.app')

@section('title', 'Acasă — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Bun venit, {{ explode(' ', trim(auth()->user()->name))[0] }}!</h1>
        <p class="mt-1 text-sm text-gray-500">{{ auth()->user()->locationLabel() }}</p>
    </div>

    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        <a href="{{ route('objects.index') }}" class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </span>
            <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['available'] }}</p>
            <p class="text-sm text-gray-500">Obiecte disponibile</p>
        </a>

        <a href="{{ route('objects.index', ['mine' => 1]) }}" class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
            </span>
            <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['mine'] }}</p>
            <p class="text-sm text-gray-500">Obiectele mele</p>
        </a>

        <a href="{{ route('loans.index') }}" class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
            </span>
            <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['activeLoans'] }}</p>
            <p class="text-sm text-gray-500">Împrumuturi active</p>
        </a>

        <a href="{{ route('conversations.index') }}" class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
            </span>
            <p class="mt-3 text-2xl font-bold text-gray-900">{{ $stats['unreadMessages'] }}</p>
            <p class="text-sm text-gray-500">Mesaje necitite</p>
        </a>
    </div>

    @if ($pendingRequests->isNotEmpty())
        <section class="mt-8">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Cereri de împrumut</h2>
                <a href="{{ route('loans.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Vezi toate</a>
            </div>
            <div class="space-y-3">
                @foreach ($pendingRequests as $loan)
                    <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $loan->object->title }}</p>
                            <p class="text-sm text-gray-500">{{ $loan->borrower->name }} · {{ $loan->starts_at?->format('d.m.Y') }} — {{ $loan->ends_at?->format('d.m.Y') }}</p>
                        </div>
                        <a href="{{ route('objects.show', $loan->object) }}" class="rounded-xl bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700">Răspunde</a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-8">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Disponibile în apropiere</h2>
            <a href="{{ route('objects.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Vezi tot</a>
        </div>

        @if ($availableObjects->isEmpty())
            <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-sm text-gray-500">
                Nu există încă obiecte disponibile. Fii primul care publică unul!
            </p>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($availableObjects as $object)
                    <x-object-card :object="$object" />
                @endforeach
            </div>
        @endif
    </section>

    @if ($activeLoans->isNotEmpty())
        <section class="mt-8">
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Împrumuturi active</h2>
                <a href="{{ route('loans.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Vezi toate</a>
            </div>
            <div class="space-y-3">
                @foreach ($activeLoans as $loan)
                    <div class="flex items-center gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-medium text-gray-900">{{ $loan->object->title }}</p>
                            <p class="text-sm text-gray-500">
                                @if ($loan->borrower_id === auth()->id())
                                    de la {{ $loan->lender->name }}
                                @else
                                    către {{ $loan->borrower->name }}
                                @endif
                            </p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700">{{ $loan->status->label() }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
