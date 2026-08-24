<div x-data="{ sidebarWidth: 280, dragging: false }"
    x-on:mousemove.window="if (dragging) sidebarWidth = Math.min(560, Math.max(220, $event.clientX))"
    x-on:mouseup.window="dragging = false"
    :class="{ 'select-none': dragging }"
    class="flex h-[calc(100vh-8rem)] overflow-hidden bg-white md:h-[calc(100vh-4rem)]">
    {{-- Conversation list --}}
    <div :style="`--sidebar-w: ${sidebarWidth}px`" @class([
        'msg-sidebar flex h-full shrink-0 flex-col border-r border-gray-100',
        'hidden md:flex' => $activeId,
    ])>
        <div class="border-b border-gray-100 px-4 py-3">
            <h2 class="font-semibold text-gray-900">Mesaje</h2>
        </div>

        <div class="flex-1 overflow-y-auto">
            @forelse ($conversations as $conversation)
                @php $cOther = $conversation->otherParticipant(auth()->user()); @endphp
                <button wire:click="select({{ $conversation->id }})"
                    @class([
                        'flex w-full items-center gap-3 border-b border-gray-50 px-4 py-3 text-left transition hover:bg-gray-50',
                        'bg-brand-50' => $activeId === $conversation->id,
                    ])>
                    <span class="relative flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                        {{ $cOther ? strtoupper(mb_substr($cOther->name, 0, 1)) : '?' }}
                        @if ($cOther?->isOnline())
                            <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-green-500"></span>
                        @endif
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate font-medium text-gray-900">{{ $cOther?->name ?? '—' }}</p>
                            <span class="shrink-0 text-[10px] text-gray-400">{{ $conversation->latestMessage?->created_at?->format('H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-2">
                            <p class="truncate text-xs text-gray-500">
                                @if ($conversation->latestMessage?->attachments?->isNotEmpty())
                                    📎 {{ $conversation->latestMessage->attachments->count() > 1 ? $conversation->latestMessage->attachments->count().' fișiere' : 'Fișier' }}
                                @else
                                    {{ $conversation->latestMessage?->body }}
                                @endif
                            </p>
                            @if ($conversation->unread > 0)
                                <span class="ml-2 flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-bold text-white">{{ $conversation->unread }}</span>
                            @endif
                        </div>
                    </div>
                </button>
            @empty
                <p class="p-4 text-center text-sm text-gray-400">Nu ai conversații încă.</p>
            @endforelse
        </div>
    </div>

    <div x-on:mousedown.prevent="dragging = true" class="hidden w-1.5 shrink-0 cursor-col-resize bg-gray-200 transition hover:bg-brand-400 md:block" title="Trage pentru a redimensiona"></div>

    {{-- Chat pane --}}
    <div @class([
        'flex-1 flex-col',
        'flex' => $activeId,
        'hidden md:flex' => ! $activeId,
    ])>
        @if ($active && $other)
            <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-2.5">
                <button type="button" wire:click="$set('activeId', null)" class="rounded-lg p-1.5 text-gray-500 transition hover:bg-gray-100 md:hidden" aria-label="Înapoi">←</button>

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
            </div>

            <div class="flex-1 space-y-3 overflow-y-auto p-4" id="messages" wire:poll.3s>
                @foreach ($active->messages as $message)
                    @php $mine = $message->sender_id === auth()->id(); @endphp
                    <div @class(['group flex items-end gap-1', 'justify-end' => $mine, 'justify-start' => ! $mine])>
                        @if ($mine)
                            <button type="button" wire:click="deleteMessage({{ $message->id }})"
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

                            @foreach ($message->attachments as $attachment)
                                @if ($attachment->isImage())
                                    <a href="/storage/{{ $attachment->path }}" target="_blank" class="mt-1 block">
                                        <img src="/storage/{{ $attachment->path }}" alt="{{ $attachment->name }}" class="max-h-48 rounded-lg">
                                    </a>
                                @else
                                    <a href="/storage/{{ $attachment->path }}" target="_blank"
                                        @class(['mt-1 flex items-center gap-2 rounded-lg px-2 py-1.5 text-xs', 'bg-white/20 text-white' => $mine, 'bg-gray-200 text-gray-700' => ! $mine])>
                                        <span>📎</span> {{ $attachment->name }}
                                    </a>
                                @endif
                            @endforeach

                            <p @class(['mt-1 text-right text-[10px]', 'text-brand-100' => $mine, 'text-gray-400' => ! $mine])>
                                {{ $message->created_at->format('d.m H:i') }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($attachments)
                <div x-data="{ previewUrl: null }" class="border-t border-gray-100 px-4 py-2">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($attachments as $index => $attachment)
                            <div class="flex items-center gap-2 rounded-xl bg-gray-50 px-2 py-1.5 ring-1 ring-gray-200">
                                @if ($attachment->isPreviewable())
                                    <button type="button" @click="previewUrl = '{{ $attachment->temporaryUrl() }}'" class="shrink-0 overflow-hidden rounded-lg" title="Vezi preview">
                                        <img src="{{ $attachment->temporaryUrl() }}" alt="" class="h-9 w-9 object-cover transition hover:opacity-80">
                                    </button>
                                @endif
                                <div class="max-w-[10rem]">
                                    <p class="truncate text-xs font-medium text-gray-700">✓ {{ $attachment->getClientOriginalName() }}</p>
                                </div>
                                <button type="button" wire:click="removeAttachment({{ $index }})" class="text-xs text-gray-400 hover:text-red-500" title="Elimină">✕</button>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="previewUrl" x-cloak @click="previewUrl = null" class="fixed inset-0 z-50 bg-black/80">
                        <button type="button" @click="previewUrl = null" class="absolute right-4 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-white text-lg font-bold text-gray-800 shadow-lg hover:bg-gray-200" aria-label="Închide">✕</button>
                        <div class="flex h-full w-full items-center justify-center p-4">
                            <img :src="previewUrl" @click.stop class="max-h-[80vh] max-w-[80vw] rounded-lg object-contain shadow-2xl">
                        </div>
                    </div>
                </div>
            @endif

            <div class="border-t border-gray-100 p-3"
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true; progress = 0"
                x-on:livewire-upload-finish="uploading = false; progress = 100"
                x-on:livewire-upload-progress="progress = $event.detail.progress"
                x-on:livewire-upload-error="uploading = false">

                @if ($isBlocked)
                    <p class="mb-2 text-center text-xs text-gray-500">Nu poți trimite mesaje acestui utilizator.</p>
                @endif

                <div wire:loading wire:target="attachments" class="mb-2">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 animate-spin text-brand-600" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                            Se încarcă fișierele…
                        </span>
                        <span x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="mt-1 h-2 overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-brand-600 transition-all duration-150" :style="`width: ${progress}%`"></div>
                    </div>
                </div>

                <form wire:submit="send" class="flex items-center gap-2">
                    <input id="chat-attachments" type="file" wire:model="attachments" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.doc,.docx,.txt">
                    <label for="chat-attachments" class="cursor-pointer rounded-xl p-2.5 text-gray-500 transition hover:bg-gray-100" title="Atașează fișiere">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                    </label>

                    <label for="body" class="sr-only">Mesaj</label>
                    <input id="body" type="text" wire:model="body" placeholder="Scrie un mesaj..." autocomplete="off" @disabled($isBlocked)
                        class="flex-1 rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 disabled:bg-gray-50 disabled:text-gray-400">
                    <button type="submit" @disabled($isBlocked)
                        class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700 disabled:opacity-50">
                        Trimite
                    </button>
                </form>

                @error('body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @error('attachments.*') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @else
            <div class="flex flex-1 items-center justify-center p-8 text-center text-sm text-gray-400">
                <div>
                    <p class="text-3xl">💬</p>
                    <p class="mt-2">Selectează o conversație pentru a începe.</p>
                </div>
            </div>
        @endif
    </div>
</div>
