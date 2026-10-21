@extends('layouts.gestionale')

@section('titolo', 'Modifica dipendente')

@section('contenuto')
    <h1 class="h4 mb-3">Modifica {{ $dipendente->nome_completo }}</h1>
    <form method="POST" action="{{ route('dipendenti.update', $dipendente) }}">
        @csrf
        @method('PUT')
        @include('dipendenti._form')
    </form>
@endsection