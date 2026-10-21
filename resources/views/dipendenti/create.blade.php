@extends('layouts.gestionale')

@section('titolo', 'Nuovo dipendente')

@section('contenuto')
    <h1 class="h4 mb-3">Nuovo dipendente</h1>
    <form method="POST" action="{{ route('dipendenti.store') }}">
        @csrf
        @include('dipendenti._form')
    </form>
@endsection