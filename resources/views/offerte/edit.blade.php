@extends('layouts.gestionale')

@section('titolo', 'Modifica bozza')

@section('contenuto')
    <h1 class="h4 mb-3">Modifica bozza</h1>

    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <form method="POST" action="{{ route('offerte.update', $offerta) }}">
        @csrf
        @method('PUT')
        @include('offerte._form')
    </form>
@endsection
