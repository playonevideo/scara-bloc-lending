@extends('layouts.app')

@section('title', 'Mesaje — '.config('app.name', 'Vecini'))

@section('mainClass', 'overflow-hidden')

@section('content')
    <livewire:messaging :conversation-id="$conversation?->id" />
@endsection
