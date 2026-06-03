<?php

namespace Database\Seeders;

use App\Models\Dentista;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\Tratamiento;
use Illuminate\Database\Seeder;

class RecetaSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = [
            'Amoxicilina 500mg',
            'Ibuprofeno 400mg',
            'Paracetamol 500mg',
            'Clindamicina 300mg',
            'Metronidazol 500mg',
            'Diclofenaco 50mg',
            'Ketorolaco 10mg',
            'Tramadol 50mg',
            'Dexametasona 8mg',
            'Clorhexidina 0.12% enjuague',
        ];

        $frecuencias    = ['Cada 8 horas', 'Cada 12 horas', 'Cada 6 horas', 'Una vez al día', 'Dos veces al día'];
        $duraciones     = ['3 días', '5 días', '7 días', '10 días', '14 días'];
        $dosis          = ['1 tableta', '2 tabletas', '1 cápsula', '5 ml', '10 ml'];

        $pacienteIds    = Paciente::pluck('id')->toArray();
        $dentistaIds    = Dentista::pluck('id')->toArray();
        $tratamientoIds = Tratamiento::pluck('id')->toArray();

        $rows = [];
        for ($i = 0; $i < 1500; $i++) {
            $rows[] = [
                'paciente_id'    => $pacienteIds[array_rand($pacienteIds)],
                'dentista_id'    => $dentistaIds[array_rand($dentistaIds)],
                'tratamiento_id' => fake()->boolean(60) && !empty($tratamientoIds)
                    ? $tratamientoIds[array_rand($tratamientoIds)]
                    : null,
                'medicamento'   => $medicamentos[array_rand($medicamentos)],
                'dosis'         => $dosis[array_rand($dosis)],
                'frecuencia'    => $frecuencias[array_rand($frecuencias)],
                'duracion'      => $duraciones[array_rand($duraciones)],
                'indicaciones'  => fake()->optional(0.6)->sentence(),
                'fecha_emision' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (count($rows) >= 500) {
                Receta::insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            Receta::insert($rows);
        }
    }
}
