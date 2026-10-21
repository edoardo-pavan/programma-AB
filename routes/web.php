<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoggettoController;
use App\Http\Controllers\MezzoController;
use App\Http\Controllers\DipendenteController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ScadenzaController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

    Route::resource('mezzi', MezzoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['mezzi' => 'mezzo']);
    
    Route::resource('dipendenti', DipendenteController::class)
        ->except(['show', 'destroy'])
        ->parameters(['dipendenti' => 'dipendente']);
        Route::post('{entita}/{id}/documenti', [DocumentoController::class, 'store'])
        ->whereIn('entita', ['mezzi', 'dipendenti'])
        ->whereNumber('id')
        ->name('documenti.store');
        
    Route::get('documenti/{documento}/apri', [DocumentoController::class, 'apri'])->name('documenti.apri');
    Route::get('documenti/{documento}/scarica', [DocumentoController::class, 'scarica'])->name('documenti.scarica');
    Route::delete('documenti/{documento}', [DocumentoController::class, 'destroy'])->name('documenti.destroy');
    Route::get('scadenze', [ScadenzaController::class, 'index'])->name('scadenze.index');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('soggetti', SoggettoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['soggetti' => 'soggetto']);
});

