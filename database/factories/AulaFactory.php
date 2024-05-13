<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Aula;
use App\Models\Sede;

class AulaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Aula::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        static $aulaNumber = 1;

        return [
            'nombre' => 'aula '. $aulaNumber++,
            'sede_id' => Sede::factory(),
            'calidad' => rand(1, 5),
        ];
    }
}
