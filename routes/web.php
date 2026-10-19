<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SoggettoController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('soggetti', SoggettoController::class)
        ->except(['show', 'destroy'])
        ->parameters(['soggetti' => 'soggetto']);
});