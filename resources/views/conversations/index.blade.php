@extends('layouts.app')

@section('title', 'Mesaje — '.config('app.name', 'Vecini'))

@section('mainClass', 'h-[calc(100vh-4rem)] overflow-hidden px-4 pt-4 sm:px-6')

@section('content')
    <livewire:messaging :conversation-id="$conversation?->id" />
@endsection
