@extends('layouts.app')

@section('title', 'Împrumuturi — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-gray-900 sm:text-3xl">Împrumuturi</h1>

    <div class="mb-6 flex gap-1 rounded-xl bg-gray-100 p-1">
        @foreach ([['active' => 'Active'], ['requests' => 'Cereri primite'], ['sent' => 'Trimise de mine']] as $tabItem)
            @php [$key, $label] = $tabItem; @endphp
            <a href="{{ route('loans.index', ['tab' => $key]) }}"
                @class([
                    'flex-1 rounded-lg px-3 py-2 text-center text-sm font-medium transition',
                    'bg-white text-brand-700 shadow-sm' => $tab === $key,
                    'text-gray-600 hover:text-gray-900' => $tab !== $key,
                ])>{{ $label }}</a>
        @endforeach
    </div>

    @if ($tab === 'active')
        <div class="space-y-4">
            @forelse ($active as $loan)
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
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
                            'bg-blue-50 text-blue-700' => $loan->status->value === 'accepted',
                            'bg-amber-50 text-amber-700' => $loan->status->value === 'borrowed',
                            'bg-red-50 text-red-700' => $loan->status->value === 'overdue',
                        ])>{{ $loan->status->label() }}</span>
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @if ($loan->status->value === 'accepted')
                            <form method="POST" action="{{ route('loans.mark-borrowed', $loan) }}">
                                @csrf
                                <button class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Confirmă predarea</button>
                            </form>
                            @if ($loan->borrower_id === auth()->id())
                                <form method="POST" action="{{ route('loans.cancel', $loan) }}">
                                    @csrf
                                    <button class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Anulează</button>
                                </form>
                            @endif
                        @elseif (in_array($loan->status->value, ['borrowed', 'overdue']))
                            <form method="POST" action="{{ route('loans.mark-returned', $loan) }}">
                                @csrf
                                <button class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Confirmă returnarea</button>
                            </form>
                        @elseif ($loan->status->value === 'returned')
                            <form method="POST" action="{{ route('loans.complete', $loan) }}">
                                @csrf
                                <button class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Finalizează</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">Nu ai împrumuturi active.</p>
            @endforelse
        </div>
    @elseif ($tab === 'requests')
        <div class="space-y-4">
            @forelse ($received->where('status', 'requested') as $loan)
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <h3 class="font-semibold text-gray-900">{{ $loan->object->title }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $loan->borrower->name }} · {{ $loan->starts_at?->format('d.m.Y') }} — {{ $loan->ends_at?->format('d.m.Y') }}
                    </p>
                    @if ($loan->message)
                        <p class="mt-2 rounded-xl bg-gray-50 p-3 text-sm text-gray-600">„{{ $loan->message }}”</p>
                    @endif
                    <div class="mt-4 flex gap-2">
                        <form method="POST" action="{{ route('loans.accept', $loan) }}">
                            @csrf
                            <button class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Acceptă</button>
                        </form>
                        <form method="POST" action="{{ route('loans.refuse', $loan) }}">
                            @csrf
                            <button class="rounded-xl border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Refuză</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">Nu ai cereri de împrumut în așteptare.</p>
            @endforelse
        </div>
    @else
        <div class="space-y-4">
            @forelse ($sent as $loan)
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $loan->object->title }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                de la {{ $loan->lender->name }} · {{ $loan->starts_at?->format('d.m.Y') }} — {{ $loan->ends_at?->format('d.m.Y') }}
                            </p>
                        </div>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-medium',
                            'bg-amber-50 text-amber-700' => $loan->status->value === 'requested',
                            'bg-green-50 text-green-700' => $loan->status->value === 'accepted',
                            'bg-gray-100 text-gray-600' => in_array($loan->status->value, ['refused', 'cancelled']),
                            'bg-blue-50 text-blue-700' => in_array($loan->status->value, ['borrowed', 'returned', 'completed', 'overdue']),
                        ])>{{ $loan->status->label() }}</span>
                    </div>

                    @if (in_array($loan->status->value, ['requested', 'accepted']))
                        <div class="mt-4">
                            <form method="POST" action="{{ route('loans.cancel', $loan) }}">
                                @csrf
                                <button class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">Anulează solicitarea</button>
                            </form>
                        </div>
                    @endif

                    @if ($loan->status->value === 'completed')
                        @php $review = $loan->reviews->where('reviewer_id', auth()->id())->first(); @endphp
                        @if (! $review)
                            <form method="POST" action="{{ route('loans.review', $loan) }}" class="mt-4 space-y-3 rounded-xl bg-gray-50 p-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Evaluare (1–5)</label>
                                    <select name="rating" required class="mt-1 block w-full rounded-xl border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                                        <option value="">Alege</option>
                                        @foreach ([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'] as $value => $stars)
                                            <option value="{{ $value }}">{{ $value }} — {{ $stars }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Comentariu (opțional)</label>
                                    <textarea name="comment" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                                </div>
                                <button class="rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Trimite recenzia</button>
                            </form>
                        @else
                            <p class="mt-3 text-sm text-green-600">✓ Ai trimis o recenzie.</p>
                        @endif
                    @endif
                </div>
            @empty
                <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">Nu ai trimis nicio solicitare.</p>
            @endforelse
        </div>
    @endif
@endsection
