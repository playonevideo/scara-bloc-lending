<div class="flex h-[26rem] max-h-[calc(100vh-12rem)] flex-col overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
    <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-2.5">
        @if ($other)
            <span class="relative flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                {{ strtoupper(mb_substr($other->name, 0, 1)) }}
                @if ($other->isOnline())
                    <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-green-500"></span>
                @endif
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate font-semibold text-gray-900">{{ $other->name }}</p>
                @if ($other->lastActiveLabel())
                    <p @class(['truncate text-xs', 'text-green-600' => $other->isOnline(), 'text-gray-400' => ! $other->isOnline()])>
                        {{ $other->lastActiveLabel() }}
                    </p>
                @elseif ($conversation->object)
                    <p class="truncate text-xs text-gray-500">Despre: {{ $conversation->object->title }}</p>
                @endif
            </div>

            <div class="relative" x-data="{ open: false, reportOpen: false }">
                <button @click="open = !open" class="rounded-lg p-1.5 text-gray-500 transition hover:bg-gray-100" aria-label="Mai multe opțiuni">⋯</button>
                <div x-show="open" @click.outside="open = false; reportOpen = false" x-cloak x-transition
                    class="absolute right-0 z-10 mt-1 w-52 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-lg">
                    <button wire:click="toggleBlock" @click="open = false"
                        class="block w-full px-4 py-2.5 text-left text-sm {{ $isBlocking ? 'text-green-600' : 'text-red-600' }} hover:bg-gray-50">
                        {{ $isBlocking ? 'Deblochează utilizatorul' : 'Blochează utilizatorul' }}
                    </button>
                    <button @click="reportOpen = !reportOpen"
                        class="block w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50">
                        Raportează utilizatorul
                    </button>
                    <div x-show="reportOpen" x-cloak class="border-t border-gray-100 bg-gray-50 p-3">
                        <form method="POST" action="{{ route('reports.store') }}" class="space-y-2">
                            @csrf
                            <input type="hidden" name="reportable_type" value="user">
                            <input type="hidden" name="reportable_id" value="{{ $other->id }}">
                            <select name="reason" required class="block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500">
                                @foreach (\App\Enums\ReportReason::options() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <textarea name="details" rows="2" placeholder="Detalii (opțional)" class="block w-full rounded-lg border-gray-300 text-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                            <button type="submit" class="w-full rounded-lg bg-red-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-red-700">Trimite raportarea</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <p class="font-semibold text-gray-900">{{ $conversation->subject ?? 'Conversație' }}</p>
        @endif
    </div>

    <div class="flex-1 space-y-3 overflow-y-auto p-4" id="messages" wire:poll.3s>
        @foreach ($conversation->messages as $message)
            @php $mine = $message->sender_id === auth()->id(); @endphp
            <div @class(['group flex items-end gap-1', 'justify-end' => $mine, 'justify-start' => ! $mine])>
                @if ($mine)
                    <button wire:click="deleteMessage({{ $message->id }})"
                        class="mb-1 text-xs text-gray-300 opacity-0 transition hover:text-red-500 group-hover:opacity-100" title="Șterge mesajul">✕</button>
                @endif
                <div @class([
                    'max-w-[80%] rounded-2xl px-4 py-2 text-sm',
                    'bg-brand-600 text-white rounded-br-sm' => $mine,
                    'bg-gray-100 text-gray-800 rounded-bl-sm' => ! $mine,
                ])>
                    @if ($message->body)
                        <p class="whitespace-pre-line">{{ $message->body }}</p>
                    @endif

                    @if ($message->attachment)
                        @php
                            $isImage = preg_match('/\.(jpe?g|png|gif|webp)$/i', $message->attachment);
                        @endphp
                        @if ($isImage)
                            <a href="/storage/{{ $message->attachment }}" target="_blank" class="mt-1 block">
                                <img src="/storage/{{ $message->attachment }}" alt="{{ $message->attachment_name }}" class="max-h-48 rounded-lg">
                            </a>
                        @else
                            <a href="/storage/{{ $message->attachment }}" target="_blank"
                                @class(['mt-1 flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs', 'bg-white/20 text-white' => $mine, 'bg-gray-200 text-gray-700' => ! $mine])>
                                <span>📎</span> {{ $message->attachment_name ?? 'Fișier' }}
                            </a>
                        @endif
                    @endif

                    <p @class(['mt-1 text-right text-[10px]', 'text-brand-100' => $mine, 'text-gray-400' => ! $mine])>
                        {{ $message->created_at->format('d.m H:i') }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="border-t border-gray-100 p-3">
        @if ($isBlocked)
            <p class="mb-2 text-center text-xs text-gray-500">Nu poți trimite mesaje acestui utilizator.</p>
        @endif

        <form wire:submit="send" class="flex items-center gap-2">
            <label class="cursor-pointer rounded-xl p-2.5 text-gray-500 transition hover:bg-gray-100" title="Atașează un fișier">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                <input type="file" wire:model="attachment" class="hidden">
            </label>
            <label for="body" class="sr-only">Mesaj</label>
            <input id="body" type="text" wire:model="body" placeholder="Scrie un mesaj..." autocomplete="off" @disabled($isBlocked)
                class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:bg-gray-50 disabled:text-gray-400">
            <button type="submit" @disabled($isBlocked)
                class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 disabled:opacity-50">
                Trimite
            </button>
        </form>

        @if ($attachment)
            <p class="mt-1 text-xs text-gray-500">Atașat: {{ $attachment->getClientOriginalName() }}</p>
        @endif

        @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        @error('attachment') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>
