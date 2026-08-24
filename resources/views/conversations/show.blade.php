@extends('layouts.app')

@section('title', 'Conversație — '.config('app.name', 'Vecini'))

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('conversations.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">← Înapoi la mesaje</a>
        <form method="POST" action="{{ route('conversations.archive', $conversation) }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700">Arhivează</button>
        </form>
    </div>

    @livewire('chat', ['conversation' => $conversation])
@endsection
