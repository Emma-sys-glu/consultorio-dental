<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paciente>
 */
class PacienteFactory extends Factory
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

        'telefono' => fake()->numerify('715#######'),

        'correo' => fake()->unique()->safeEmail(),

        'fecha_nacimiento' => fake()->date(),

        'curp' => strtoupper(fake()->bothify('????######??????##')),

        'tipo_sangre' => fake()->randomElement([
            'A+',
            'A-',
            'B+',
            'B-',
            'AB+',
            'AB-',
            'O+',
            'O-'
        ]),

        'alergias' => fake()->randomElement([
            'Ninguna',
            'Penicilina',
            'Polvo',
            'Lactosa',
            'Mariscos'
        ]),

        'antecedentes_medicos' => fake()->sentence(),
    ];
}
}
