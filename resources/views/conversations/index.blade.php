@extends('layouts.app')

@section('title', 'Mesaje — '.config('app.name', 'Vecini'))

@section('content')
    <h1 class="mb-6 text-2xl font-bold text-gray-900 sm:text-3xl">Mesaje</h1>

    @if ($conversations->isEmpty())
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-12 text-center">
            <p class="text-gray-500">Nu ai încă nicio conversație.</p>
            <p class="mt-1 text-sm text-gray-400">Pornește o conversație de pe pagina unui obiect.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            @foreach ($conversations as $conversation)
                @php $other = $conversation->otherParticipant(auth()->user()); @endphp
                <a href="{{ route('conversations.show', $conversation) }}" class="flex items-center gap-3 border-b border-gray-50 px-4 py-3 transition last:border-0 hover:bg-gray-50">
                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                        {{ $other ? strtoupper(mb_substr($other->name, 0, 1)) : '?' }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <p class="truncate font-medium text-gray-900">{{ $other?->name ?? '—' }}</p>
                            <span class="text-xs text-gray-400">{{ $conversation->lastMessage()?->created_at?->format('d.m H:i') }}</span>
                        </div>
                        <p class="truncate text-sm text-gray-500">{{ $conversation->lastMessage()?->body }}</p>
                    </div>
                    @if ($conversation->unread > 0)
                        <span class="flex h-6 min-w-6 items-center justify-center rounded-full bg-brand-600 px-1.5 text-xs font-bold text-white">{{ $conversation->unread }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endsection
