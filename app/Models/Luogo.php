<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Luogo extends Model
{
    use HasFactory;

    protected $table = 'luoghi';

    protected $guarded = [];

    protected $casts = [
        'attivo' => 'boolean',
    ];

    public function getEtichettaAttribute(): string
    {
        if ($this->comune) {
            return $this->nome . ' (' . $this->comune . ')';
        }

        return $this->nome;
    }
}
