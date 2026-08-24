<div class="flex h-[calc(100vh-8rem)] flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3">
        @if ($other = $conversation->otherParticipant(auth()->user()))
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                {{ strtoupper(mb_substr($other->name, 0, 1)) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate font-semibold text-gray-900">{{ $other->name }}</p>
                @if ($conversation->object)
                    <p class="truncate text-xs text-gray-500">Despre: {{ $conversation->object->title }}</p>
                @endif
            </div>
        @else
            <p class="font-semibold text-gray-900">{{ $conversation->subject ?? 'Conversație' }}</p>
        @endif
    </div>

    <div class="flex-1 space-y-3 overflow-y-auto p-4" id="messages" wire:poll.3s>
        @foreach ($conversation->messages as $message)
            @php $mine = $message->sender_id === auth()->id(); @endphp
            <div @class(['flex', 'justify-end' => $mine, 'justify-start' => ! $mine])>
                <div @class([
                    'max-w-[80%] rounded-2xl px-4 py-2 text-sm',
                    'bg-brand-600 text-white rounded-br-sm' => $mine,
                    'bg-gray-100 text-gray-800 rounded-bl-sm' => ! $mine,
                ])>
                    <p class="whitespace-pre-line">{{ $message->body }}</p>
                    <p @class(['mt-1 text-right text-[10px]', 'text-brand-100' => $mine, 'text-gray-400' => ! $mine])>
                        {{ $message->created_at->format('d.m H:i') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="border-t border-gray-100 p-3">
        <form wire:submit="send" class="flex gap-2">
            <label for="body" class="sr-only">Mesaj</label>
            <input id="body" type="text" wire:model="body" placeholder="Scrie un mesaj..." autocomplete="off"
                class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <button type="submit" class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                Trimite
            </button>
        </form>
        @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
