<?php

namespace Database\Factories;

use App\Models\Luogo;
use Illuminate\Database\Eloquent\Factories\Factory;

class LuogoFactory extends Factory
{
    protected $model = Luogo::class;

    public function definition(): array
    {
        $comune = fake()->city();

        return [
            'nome' => fake()->randomElement(['Deposito', 'Sede', 'Magazzino', 'Piattaforma', 'Hub']) . ' ' . $comune,
            'comune' => $comune,
            'indirizzo' => fake()->optional()->streetAddress(),
            'attivo' => true,
        ];
    }
}
