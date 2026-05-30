<?php

namespace Database\Factories;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $fecha = fake()->dateTimeBetween('+1 days', '+60 days');
    $horaInicio = fake()->randomElement([
        '08:00:00',
        '09:00:00',
        '10:00:00',
        '11:00:00',
        '12:00:00'
    ]);

    $duracion = fake()->randomElement([30, 45, 60]);

    $inicio = \Carbon\Carbon::parse($fecha->format('Y-m-d') . ' ' . $horaInicio);
    $fin = $inicio->copy()->addMinutes($duracion);

    return [
        'paciente_id' => \App\Models\Paciente::inRandomOrder()->first()->id,
        'dentista_id' => \App\Models\Dentista::inRandomOrder()->first()->id,
        'fecha' => $fecha->format('Y-m-d'),
        'hora_inicio' => $inicio->format('H:i:s'),
        'hora_fin' => $fin->format('H:i:s'),
        'duracion_minutos' => $duracion,
        'motivo' => fake()->randomElement([
            'Limpieza dental',
            'Revisión general',
            'Dolor dental',
            'Ortodoncia',
            'Extracción',
            'Blanqueamiento'
        ]),
        'estado' => fake()->randomElement([
            'pendiente',
            'confirmada',
            'finalizada'
        ])
    ];
}
}
