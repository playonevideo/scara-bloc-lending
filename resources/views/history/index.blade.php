@extends('layouts.app')

@section('title', 'Istoric — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-gray-900 sm:text-3xl">Istoricul împrumuturilor</h1>

    <div class="space-y-4">
        @forelse ($loans as $loan)
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $loan->object->title }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if ($loan->borrower_id === auth()->id())
                                Împrumutat de la <strong>{{ $loan->lender->name }}</strong>
                            @else
                                Împrumutat către <strong>{{ $loan->borrower->name }}</strong>
                            @endif
                            · {{ $loan->starts_at?->format('d.m.Y') }} — {{ $loan->ends_at?->format('d.m.Y') }}
                        </p>
                    </div>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-medium',
                        'bg-green-50 text-green-700' => $loan->status->value === 'completed',
                        'bg-gray-100 text-gray-600' => in_array($loan->status->value, ['refused', 'cancelled']),
                        'bg-blue-50 text-blue-700' => $loan->status->value === 'returned',
                    ])>{{ $loan->status->label() }}</span>
                </div>
            </div>
        @empty
            <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center text-gray-500">Nu ai împrumuturi în istoric.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $loans->links() }}</div>
@endsection
