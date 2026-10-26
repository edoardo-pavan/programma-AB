<?php

namespace Database\Factories;

use App\Models\Soggetto;
use Illuminate\Database\Eloquent\Factories\Factory;

class SoggettoFactory extends Factory
{
    protected $model = Soggetto::class;

    public function definition(): array
    {
        return [
            'ragione_sociale' => fake()->company(),
            'partita_iva' => fake()->optional()->numerify('IT###########'),
            'codice_fiscale' => fake()->optional()->bothify('??#######A'),
            'codice_sdi' => fake()->optional()->bothify('???????'),
            'pec' => fake()->optional()->safeEmail(),
            'indirizzo' => fake()->optional()->streetAddress(),
            'cap' => fake()->optional()->postcode(),
            'comune' => fake()->optional()->city(),
            'provincia' => fake()->optional()->randomElement(['MI', 'RM', 'TO', 'NA', 'BO']),
            'telefono' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'condizioni_pagamento' => fake()->optional()->randomElement(['Bonifico 30 gg', 'Bonifico 60 gg', 'RID']),
            'categoria_fornitore' => fake()->optional()->randomElement(['officina', 'carburante', 'ricambi', 'gommista', 'altro']),
            'note' => fake()->optional()->text(),
            'is_cliente' => fake()->boolean(70),
            'is_fornitore' => fake()->boolean(30),
            'attivo' => true,
        ];
    }

    public function cliente(): static
    {
        return $this->state(['is_cliente' => true, 'is_fornitore' => false]);
    }

    public function fornitore(): static
    {
        return $this->state(['is_cliente' => false, 'is_fornitore' => true]);
    }
}
