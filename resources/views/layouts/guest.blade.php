<!DOCTYPE html>
<html lang="ro" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Vecini'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-brand-50 font-sans text-gray-900 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-600 text-xl font-bold text-white shadow-lg shadow-brand-600/20">V</span>
                    <span class="text-2xl font-bold text-gray-900">{{ config('app.name', 'Vecini') }}</span>
                </a>
                <p class="mt-2 text-sm text-gray-500">Împrumută și partajează obiecte cu vecinii tăi.</p>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-xl shadow-brand-900/5 ring-1 ring-gray-100 sm:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

            <p class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Vecini') }}
            </p>
        </div>
    </div>
</body>
</html>
