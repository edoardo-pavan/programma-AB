@extends('layouts.gestionale')

@section('titolo', 'Nuova offerta')

@section('contenuto')
    <h1 class="h4 mb-3">Nuova offerta</h1>
    <form method="POST" action="{{ route('offerte.store') }}">
        @csrf
        @include('offerte._form')
    </form>
@endsection
