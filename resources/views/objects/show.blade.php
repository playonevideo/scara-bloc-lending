@extends('layouts.app')

@section('title', $object->title.' — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
            @if ($object->coverImage())
                <div class="aspect-[16/9] w-full overflow-hidden bg-gray-100">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($object->coverImage()->path) }}" alt="{{ $object->title }}"
                        class="h-full w-full object-cover">
                </div>
            @endif

            <div class="p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-700">{{ $object->category?->name ?? 'Diverse' }}</span>
                    @if ($object->isAvailable())
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700">Disponibil</span>
                    @else
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">{{ $object->status->label() }}</span>
                    @endif
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700">{{ $object->condition->label() }}</span>
                </div>

                <h1 class="mt-3 text-2xl font-bold text-gray-900 sm:text-3xl">{{ $object->title }}</h1>

                <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                        {{ strtoupper(mb_substr($object->owner->name, 0, 1)) }}
                    </span>
                    <span>{{ $object->owner->name }}</span>
                    <span>·</span>
                    <span>{{ $object->owner->locationLabel() }}</span>
                </div>

                @if ($object->description)
                    <p class="mt-5 whitespace-pre-line text-gray-700">{{ $object->description }}</p>
                @endif

                <dl class="mt-6 grid grid-cols-1 gap-4 rounded-2xl bg-gray-50 p-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">Perioadă maximă</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">{{ $object->max_borrow_days }} zile</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase text-gray-400">Predare</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-800">
                            {{ $object->can_leave_at_door ? 'Poate fi lăsat la ușă' : 'Predare personală' }}
                        </dd>
                    </div>
                    @if ($object->special_conditions)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase text-gray-400">Condiții speciale</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $object->special_conditions }}</dd>
                        </div>
                    @endif
                    @if ($object->usage_instructions)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase text-gray-400">Instrucțiuni de utilizare</dt>
                            <dd class="mt-1 whitespace-pre-line text-sm text-gray-700">{{ $object->usage_instructions }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    @if ($canRequest)
                        <button type="button" x-data @click="document.getElementById('loan-form').classList.toggle('hidden')"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700">
                            Solicită împrumut
                        </button>
                    @endif

                    <form method="POST" action="{{ route('objects.favorite', $object) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                            @if ($isFavorite)
                                <span class="text-amber-500">★</span> În favorite
                            @else
                                <span>☆</span> Adaugă la favorite
                            @endif
                        </button>
                    </form>

                    @if ($object->owner_id !== auth()->id())
                        <form method="POST" action="{{ route('conversations.store') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $object->owner_id }}">
                            <input type="hidden" name="object_id" value="{{ $object->id }}">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                Mesajează proprietarul
                            </button>
                        </form>
                    @endif

                    @if ($object->owner_id === auth()->id())
                        <a href="{{ route('objects.edit', $object) }}" class="inline-flex items-center rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50">Editează</a>
                        <form method="POST" action="{{ route('objects.destroy', $object) }}" onsubmit="return confirm('Sigur dorești să ștergi acest obiect?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center rounded-xl border border-red-200 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50">Șterge</button>
                        </form>
                    @endif
                </div>

                @if ($object->owner_id !== auth()->id())
                    <details class="mt-6 rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
                        <summary class="cursor-pointer text-sm font-medium text-gray-500 hover:text-gray-700">Raportează acest obiect</summary>
                        <form method="POST" action="{{ route('reports.store') }}" class="mt-3 space-y-3">
                            @csrf
                            <input type="hidden" name="reportable_type" value="object">
                            <input type="hidden" name="reportable_id" value="{{ $object->id }}">
                            <div>
                                <label for="reason" class="block text-sm font-medium text-gray-700">Motiv</label>
                                <select id="reason" name="reason" required class="mt-1 block w-full rounded-xl border-gray-300 focus:border-brand-500 focus:ring-brand-500">
                                    @foreach (\App\Enums\ReportReason::options() as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="details" class="block text-sm font-medium text-gray-700">Detalii (opțional)</label>
                                <textarea id="details" name="details" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-brand-500 focus:ring-brand-500"></textarea>
                            </div>
                            <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Trimite raportarea</button>
                        </form>
                    </details>
                @endif

                @if ($canRequest)
                    <div id="loan-form" class="mt-6 hidden rounded-2xl border border-brand-100 bg-brand-50/50 p-5">
                        <h2 class="font-semibold text-gray-900">Solicită împrumutul</h2>
                        <form method="POST" action="{{ route('loans.store', $object) }}" class="mt-4 space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="starts_at" class="block text-sm font-medium text-gray-700">De la</label>
                                    <input id="starts_at" type="date" name="starts_at" required min="{{ now()->toDateString() }}"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                                <div>
                                    <label for="ends_at" class="block text-sm font-medium text-gray-700">Până la</label>
                                    <input id="ends_at" type="date" name="ends_at" required min="{{ now()->toDateString() }}"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                </div>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Mesaj (opțional)</label>
                                <textarea id="message" name="message" rows="3" placeholder="ex. Aș avea nevoie de ea sâmbătă..."
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500"></textarea>
                            </div>
                            <button type="submit" class="rounded-xl bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-700">
                                Trimite solicitarea
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
