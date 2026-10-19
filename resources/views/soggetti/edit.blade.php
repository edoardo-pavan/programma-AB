@extends('layouts.gestionale')

@section('titolo', 'Modifica soggetto')

@section('contenuto')
    <h1 class="h4 mb-3">Modifica {{ $soggetto->ragione_sociale }}</h1>
    <form method="POST" action="{{ route('soggetti.update', $soggetto) }}">
        @csrf
        @method('PUT')
        @include('soggetti._form')
    </form>
@endsection