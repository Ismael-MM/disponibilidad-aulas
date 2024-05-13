<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Curso;

class CursoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Curso::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->word(),
            'turno' => $this->faker->randomElement(["M","T"]),
            'horas' => $this->faker->numberBetween(1,2000),
            'horas_diarias' => $this->faker->randomDigitNotNull(),
            'calidad' => rand(1, 5),
            'codigo' => 'C'. rand(1, 1000),
        ];
    }
}
