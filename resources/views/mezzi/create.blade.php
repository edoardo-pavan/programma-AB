@extends('layouts.gestionale')

@section('titolo', 'Nuovo mezzo')

@section('contenuto')
    <h1 class="h4 mb-3">Nuovo mezzo</h1>
    <form method="POST" action="{{ route('mezzi.store') }}">
        @csrf
        @include('mezzi._form')
    </form>
@endsection