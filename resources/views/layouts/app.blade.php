<!DOCTYPE html>
<html lang="ro" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Vecini'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-gray-50 font-sans text-gray-900 antialiased">

    @php
        $user = auth()->user();
        $unreadNotificationCount = $user->unreadNotifications()->count();
        $recentNotifications = $user->notifications()->latest()->limit(6)->get();

        $conversations = $user->conversations()
            ->with(['messages', 'participants'])
            ->get()
            ->filter(fn ($c) => $c->messages->isNotEmpty())
            ->map(function ($c) use ($user) {
                $c->setAttribute('unread', $c->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count());
                $c->setAttribute('latestMessage', $c->messages->last());

                return $c;
            })
            ->sortByDesc(fn ($c) => $c->latestMessage?->created_at)
            ->values();

        $unreadMessageCount = $conversations->sum('unread');
        $recentConversations = $conversations->take(5);
    @endphp

    <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-lg font-bold text-white">V</span>
                <span class="hidden text-lg font-bold sm:inline">{{ config('app.name', 'Vecini') }}</span>
            </a>

            <nav class="hidden items-center gap-1 md:flex" aria-label="Navigare principală">
                <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">Acasă</x-nav-link>
                <x-nav-link href="{{ route('objects.index') }}" :active="request()->routeIs('objects.*')">Obiecte</x-nav-link>
                <x-nav-link href="{{ route('loans.index') }}" :active="request()->routeIs('loans.*')">Împrumuturi</x-nav-link>
            </nav>

            <div class="flex items-center gap-1 sm:gap-2">
                <a href="{{ route('objects.create') }}" class="hidden items-center gap-1 rounded-xl bg-brand-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 sm:inline-flex">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                    Adaugă
                </a>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative rounded-xl p-2 text-gray-600 transition hover:bg-gray-100" aria-label="Mesaje" :aria-expanded="open">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                        @if ($unreadMessageCount > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">{{ $unreadMessageCount > 9 ? '9+' : $unreadMessageCount }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak x-transition
                        class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg sm:w-80">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">Mesaje</p>
                            <a href="{{ route('conversations.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">Vezi toate mesajele</a>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse ($recentConversations as $conversation)
                                @php $other = $conversation->otherParticipant($user); @endphp
                                <a href="{{ route('conversations.show', $conversation) }}" class="flex items-center gap-3 border-b border-gray-50 px-4 py-3 transition last:border-0 hover:bg-gray-50">
                                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">{{ $other ? strtoupper(mb_substr($other->name, 0, 1)) : '?' }}</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="truncate text-sm font-medium text-gray-900">{{ $other?->name ?? '—' }}</p>
                                            <span class="shrink-0 text-[10px] text-gray-400">{{ $conversation->latestMessage?->created_at?->diffForHumans() }}</span>
                                        </div>
                                        <p class="truncate text-xs text-gray-500">{{ $conversation->latestMessage?->body }}</p>
                                    </div>
                                    @if ($conversation->unread > 0)
                                        <span class="flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-bold text-white">{{ $conversation->unread }}</span>
                                    @endif
                                </a>
                            @empty
                                <p class="px-4 py-6 text-center text-sm text-gray-400">Nu ai conversații încă.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative rounded-xl p-2 text-gray-600 transition hover:bg-gray-100" aria-label="Notificări" :aria-expanded="open">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                        @if ($unreadNotificationCount > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak x-transition
                        class="absolute right-0 mt-2 w-72 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg sm:w-80">
                        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
                            <p class="text-sm font-semibold text-gray-900">Notificări</p>
                            <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">Vezi toate notificările</a>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse ($recentNotifications as $notification)
                                @php $data = $notification->data; @endphp
                                <a href="{{ $data['url'] ?? route('notifications.index') }}"
                                    @class(['flex items-start gap-3 border-b border-gray-50 px-4 py-3 transition last:border-0 hover:bg-gray-50', 'bg-brand-50/50' => $notification->unread()])>
                                    <span @class(['mt-1.5 h-2 w-2 flex-shrink-0 rounded-full', 'bg-brand-500' => $notification->unread(), 'bg-gray-200' => $notification->read()])></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $data['title'] ?? 'Notificare' }}</p>
                                        <p class="truncate text-xs text-gray-500">{{ $data['message'] ?? '' }}</p>
                                        <p class="mt-0.5 text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @empty
                                <p class="px-4 py-6 text-center text-sm text-gray-400">Nu ai notificări.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 rounded-xl p-1.5 transition hover:bg-gray-100" aria-haspopup="true" :aria-expanded="open">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-cloak x-transition
                        class="absolute right-0 mt-2 w-56 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->locationLabel() }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Profilul meu</a>
                        <a href="{{ route('security.show') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Securitate</a>
                        <a href="{{ route('history.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Istoric</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">Deconectare</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 pb-28 pt-6 sm:px-6 md:pb-12">
        @yield('content')
    </main>

    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-white/95 backdrop-blur md:hidden" aria-label="Navigare mobilă">
        <div class="grid grid-cols-5">
            <x-mobile-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                <span class="text-[10px]">Acasă</span>
            </x-mobile-nav-link>
            <x-mobile-nav-link href="{{ route('objects.index') }}" :active="request()->routeIs('objects.*')">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/></svg>
                <span class="text-[10px]">Obiecte</span>
            </x-mobile-nav-link>
            <x-mobile-nav-link href="{{ route('objects.create') }}" :active="request()->routeIs('objects.create')">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                <span class="text-[10px]">Adaugă</span>
            </x-mobile-nav-link>
            <x-mobile-nav-link href="{{ route('conversations.index') }}" :active="request()->routeIs('conversations.*')">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                <span class="text-[10px]">Mesaje</span>
            </x-mobile-nav-link>
            <x-mobile-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.*') || request()->routeIs('security.*')">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <span class="text-[10px]">Profil</span>
            </x-mobile-nav-link>
        </div>
    </nav>

    @livewireScripts
</body>
</html>
