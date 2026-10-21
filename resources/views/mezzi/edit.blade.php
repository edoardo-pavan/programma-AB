@extends('layouts.gestionale')

@section('titolo', 'Modifica mezzo')

@section('contenuto')
    <h1 class="h4 mb-1">Modifica {{ $mezzo->etichetta }}</h1>
    @if ($mezzo->data_immatricolazione)
        <p class="text-muted">Immatricolato il {{ $mezzo->data_immatricolazione->format('d/m/Y') }}</p>
    @endif

    <form method="POST" action="{{ route('mezzi.update', $mezzo) }}">
        @csrf
        @method('PUT')
        @include('mezzi._form')
    </form>

    @include('documenti._pannello', ['proprietario' => $mezzo, 'entita' => 'mezzi'])
@endsection
