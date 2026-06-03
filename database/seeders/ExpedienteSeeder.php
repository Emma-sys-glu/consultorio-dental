<?php

namespace Database\Seeders;

use App\Models\Expediente;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class ExpedienteSeeder extends Seeder
{
    public function run(): void
    {
        $diagnosticos = [
            'Caries dental múltiple',
            'Gingivitis moderada',
            'Maloclusión clase II',
            'Bruxismo nocturno',
            'Periodontitis crónica',
            'Hipersensibilidad dental',
            'Erupción del tercer molar',
            'Fractura dental',
            'Absceso periapical',
            'Sin patología relevante',
        ];

        $procedimientos = [
            'Obturación con resina compuesta',
            'Limpieza dental profunda',
            'Extracción simple',
            'Tratamiento de conducto',
            'Aplicación de selladores',
            'Colocación de corona',
            'Blanqueamiento dental',
            'Instalación de brackets',
            'Cirugía periodontal',
        ];

        Paciente::chunk(500, function ($pacientes) use ($diagnosticos, $procedimientos) {
            $rows = $pacientes->map(fn ($p) => [
                'paciente_id'              => $p->id,
                'diagnostico'              => $diagnosticos[array_rand($diagnosticos)],
                'observaciones'            => fake()->optional(0.7)->sentence(),
                'procedimientos_realizados' => $procedimientos[array_rand($procedimientos)],
                'evolucion_tratamiento'    => fake()->optional(0.5)->sentence(),
                'created_at'               => now(),
                'updated_at'               => now(),
            ])->toArray();

            Expediente::insert($rows);
        });
    }
}
