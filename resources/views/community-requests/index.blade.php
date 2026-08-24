@extends('layouts.app')

@section('title', 'Cereri — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Cereri în comunitate</h1>

        <form method="POST" action="{{ route('community-requests.store') }}" class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            @csrf
            <h2 class="font-semibold text-gray-900">Am nevoie de...</h2>
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Ce cauți?</label>
                <input id="title" type="text" name="title" required
                    placeholder="ex. Am nevoie de o bormașină pentru două ore"
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Detalii</label>
                <textarea id="description" name="description" rows="3" required
                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
            </div>
            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                Publică cererea
            </button>
        </form>

        <div class="space-y-3">
            @forelse ($requests as $request)
                <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900">{{ $request->title }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $request->description }}</p>
                            <p class="mt-2 text-xs text-gray-400">{{ $request->user->name }} · {{ $request->created_at->diffForHumans() }}</p>
                        </div>
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-medium',
                            'bg-green-50 text-green-700' => $request->status === 'open',
                            'bg-gray-100 text-gray-500' => $request->status === 'closed',
                        ])>{{ $request->status === 'open' ? 'Deschisă' : 'Închisă' }}</span>
                    </div>

                    @if ($request->status === 'open' && ($request->user_id === auth()->id() || auth()->user()->role->isAdmin()))
                        <form method="POST" action="{{ route('community-requests.close', $request) }}" class="mt-3">
                            @csrf
                            <button class="text-sm font-medium text-gray-500 hover:text-gray-700">Închide</button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500">Nu există cereri.</p>
            @endforelse
        </div>

        <div>{{ $requests->links() }}</div>
    </div>
@endsection
