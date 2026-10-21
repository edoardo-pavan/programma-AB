<?php

namespace App\Http\Controllers;

use App\Services\ScadenzeService;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $urgenti = ScadenzeService::correnti()
            ->filter(fn ($d) => in_array($d->stato, ['scaduto', 'in_scadenza'], true));

        return view('home', [
            'scadenze' => $urgenti->take(6),
            'totaleScadenze' => $urgenti->count(),
        ]);
    }
}
