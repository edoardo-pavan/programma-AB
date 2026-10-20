<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoggettoController;
use App\Http\Controllers\MezzoController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

    Route::resource('mezzi', MezzoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['mezzi' => 'mezzo']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('soggetti', SoggettoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['soggetti' => 'soggetto']);
});

