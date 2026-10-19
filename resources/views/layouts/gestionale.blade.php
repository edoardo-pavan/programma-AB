<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titolo', 'Dashboard') · Autotrasporti Bovi</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bovi-body">

@php
    $menu = [
        ['Dashboard',            'bi-house-door-fill',    'home', 'home'],
        ['Viaggi',               'bi-truck',              null,   null],
        ['Offerte',              'bi-file-earmark-text',  null,   null],
        ['Clienti e fornitori', 'bi-people', 'soggetti.index', 'soggetti.*'],
        ['Mezzi',                'bi-truck-flatbed',      null,   null],
        ['Dipendenti',           'bi-person-badge',       null,   null],
        ['Scadenze',             'bi-calendar-event',     null,   null],
        ['Report',               'bi-bar-chart',          null,   null],
        ['Impostazioni',         'bi-gear',               null,   null],
    ];
    $prossimamente = [
        ['Rifiuti / FIR', 'bi-recycle'],
        ['Fatturazione',  'bi-receipt'],
    ];
@endphp

<aside class="bovi-sidebar">
    <div class="bovi-logo">AUTOTRASPORTI <strong>BOVI</strong></div>

    <nav class="bovi-nav">
        @foreach ($menu as [$nome, $icona, $rotta, $attiva])
            <a href="{{ $rotta ? route($rotta) : '#' }}"
               class="{{ $attiva && request()->routeIs($attiva) ? 'attiva' : '' }}">
                <i class="bi {{ $icona }}"></i> {{ $nome }}
            </a>
        @endforeach

        <div class="sezione">In arrivo</div>
        @foreach ($prossimamente as [$nome, $icona])
            <a href="#" class="disabilitata"><i class="bi {{ $icona }}"></i> {{ $nome }}</a>
        @endforeach
    </nav>
</aside>

<div class="bovi-main">
    <header class="bovi-topbar">
        <span>Moviamo il tuo mondo</span>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end lh-sm">
                <div class="text-white">{{ Auth::user()->name }}</div>
                <small>{{ ucfirst(str_replace('_', ' ', Auth::user()->ruolo)) }}</small>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light" type="submit">
                    <i class="bi bi-box-arrow-right"></i> Esci
                </button>
            </form>
        </div>
    </header>

    <main class="bovi-content">
        @yield('contenuto')
    </main>
</div>

</body>
</html>