@extends('layouts.app')

@section('title', 'Notificări — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Notificări</h1>
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-brand-600 hover:text-brand-700">Marchează totul ca citit</button>
        </form>
    </div>

    @if ($notifications->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center text-gray-500">
            Nu ai notificări.
        </div>
    @else
        <div class="space-y-2">
            @foreach ($notifications as $notification)
                @php $data = $notification->data; @endphp
                <div @class([
                    'flex items-start gap-3 rounded-2xl border p-4',
                    'border-brand-100 bg-brand-50/50' => $notification->unread(),
                    'border-gray-100 bg-white' => $notification->read(),
                ])>
                    <span @class([
                        'mt-1.5 h-2.5 w-2.5 flex-shrink-0 rounded-full',
                        'bg-brand-500' => $notification->unread(),
                        'bg-gray-200' => $notification->read(),
                    ])></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $data['title'] ?? 'Notificare' }}</p>
                        <p class="mt-0.5 text-sm text-gray-600">{{ $data['message'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if (! empty($data['url']))
                            <a href="{{ $data['url'] }}" class="rounded-lg px-2 py-1 text-xs font-medium text-brand-600 hover:bg-brand-50">Deschide</a>
                        @endif
                        @if ($notification->unread())
                            <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                @csrf
                                <button class="rounded-lg px-2 py-1 text-xs text-gray-400 hover:text-gray-600" aria-label="Marchează ca citită">✕</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
@endsection
