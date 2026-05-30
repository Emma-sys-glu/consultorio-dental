<?php

namespace Database\Factories;

use App\Models\Dentista;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dentista>
 */
class DentistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'nombre' => fake()->firstName(),
        'apellido_paterno' => fake()->lastName(),
        'apellido_materno' => fake()->lastName(),
        'especialidad' => fake()->randomElement([
            'Odontología general',
            'Ortodoncia',
            'Endodoncia',
            'Periodoncia',
            'Cirugía dental',
            'Odontopediatría'
        ]),
        'cedula_profesional' => fake()->unique()->numerify('CED######'),
        'telefono' => fake()->numerify('715#######'),
        'correo' => fake()->unique()->safeEmail(),
        'horario_inicio' => fake()->randomElement([
            '08:00:00',
            '09:00:00',
            '10:00:00'
        ]),
        'horario_fin' => fake()->randomElement([
            '14:00:00',
            '15:00:00',
            '16:00:00'
        ]),
        'consultorio' => fake()->randomElement([
            'Consultorio 1',
            'Consultorio 2',
            'Consultorio 3',
            'Consultorio 4'
        ]),
    ];
}
}
