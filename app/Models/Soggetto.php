<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soggetto extends Model
{
    protected $table = 'soggetti';

    protected $guarded = [];

    protected $casts = [
        'is_cliente' => 'boolean',
        'is_fornitore' => 'boolean',
        'attivo' => 'boolean',
    ];

    public const CATEGORIE_FORNITORE = [
        'officina', 'carburante', 'ricambi', 'gommista',
        'cancelleria', 'ristorazione', 'altro',
    ];
}