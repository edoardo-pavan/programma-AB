<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mezzo extends Model
{
    protected $table = 'mezzi';

    protected $guarded = [];

    protected $casts = [
        'adr' => 'boolean',
        'attivo' => 'boolean',
    ];

    public const TIPI = [
        'trattore' => 'Trattore',
        'motrice' => 'Motrice',
        'semirimorchio' => 'Semirimorchio',
    ];

    public const SOTTOTIPI = [
        'ribaltabile' => 'Ribaltabile',
        'centina' => 'Centina',
        'cisterna' => 'Cisterna',
        'altro' => 'Altro',
    ];
}