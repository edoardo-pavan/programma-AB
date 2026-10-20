@extends('layouts.gestionale')

@section('titolo', 'Modifica mezzo')

@section('contenuto')
    <h1 class="h4 mb-3">Modifica {{ $mezzo->targa }}</h1>
    <form method="POST" action="{{ route('mezzi.update', $mezzo) }}">
        @csrf
        @method('PUT')
        @include('mezzi._form')
    </form>
@endsection