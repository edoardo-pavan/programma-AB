@extends('layouts.gestionale')

@section('titolo', 'Nuovo soggetto')

@section('contenuto')
    <h1 class="h4 mb-3">Nuovo cliente o fornitore</h1>
    <form method="POST" action="{{ route('soggetti.store') }}">
        @csrf
        @include('soggetti._form')
    </form>
@endsection