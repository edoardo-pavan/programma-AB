@extends('layouts.gestionale')

@section('titolo', 'Dashboard')

@section('contenuto')
    <h1 class="h4 mb-1">Buongiorno {{ Auth::user()->name }}</h1>
    <p class="text-muted mb-4">
        Ecco una panoramica della tua azienda oggi, {{ now()->translatedFormat('j F Y') }}.
    </p>

    <div class="row g-3 mb-4">
        @foreach ([
            ['Viaggi oggi',          'bi-truck',                 '#2563eb', '0'],
            ['Tonnellate oggi',      'bi-box-seam',              '#16a34a', '0'],
            ['Ricavi stimati',       'bi-currency-euro',         '#f97316', '€ 0'],
            ['Viaggi senza offerta', 'bi-exclamation-triangle',  '#dc2626', '0'],
        ] as [$etichetta, $icona, $colore, $valore])
            <div class="col-md-6 col-xl-3">
                <div class="bovi-card bovi-kpi">
                    <div class="icona" style="background: {{ $colore }}"><i class="bi {{ $icona }}"></i></div>
                    <div>
                        <div class="etichetta">{{ $etichetta }}</div>
                        <div class="valore">{{ $valore }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="bovi-card">
                <h2 class="h6">Viaggi di oggi</h2>
                <p class="text-muted mb-0">Qui compariranno i viaggi inseriti dai rapportini.</p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="bovi-card">
                <h2 class="h6">Scadenze prossime</h2>
                <p class="text-muted mb-0">Qui compariranno le scadenze di mezzi e dipendenti.</p>
            </div>
        </div>
    </div>
@endsection